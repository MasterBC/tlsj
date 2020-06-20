<?php

namespace app\common\logic;

use app\common\model\branch\UsersBranch;
use app\common\model\Users;
use app\common\model\block\Block;
use app\common\model\UsersData;
use app\common\model\block\UsersBlock;
use app\common\model\block\BlockChange;
use app\common\model\block\BlockChangeLog;
use app\common\model\block\BlockTransform;
use app\common\model\block\BlockTransformLog;
use think\Db;
use think\facade\Log;
use app\facade\Password;
use think\facade\Request;

class BlockLogic
{
    /**
     * 货币转账
     * @param array $data
     * @param array $userInfo
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doBlockChange($data, $userInfo)
    {
        // 将传过来的值强制转换
        $bid = intval($data['bid']);
        $toBid = intval($data['to_bid']);
        $data['num'] = intval($data['num']);
        $data['toAccount'] = trim($data['toAccount']);
        $data['secpwd'] = trim($data['secpwd']);
        $userId = intval($userInfo['user_id']);

        if ($userId <= 0) {
            exception('网络错误，请刷新后重试');
        }

        if ($data['num'] <= 0) {
            exception('转出数量错误');
        }

        // 实例化所需的model类
        $usersBlockModel = new UsersBlock();
        $blockChangeLogModel = new BlockChangeLog();

        // 获取转账参数
        $blockChangeInfo = (new BlockChange())->getChangeBlockInfo($bid, $toBid, 1);
        if (!$blockChangeInfo) {
            exception('网络错误，请刷新后重试');
        }

        // 查出转入者的账号信息
        $toUserInfo = $this->getToUserInfo($data['toAccount'], $toBid);

        // 判断是否是自己转自己
        if ($toUserInfo['user_id'] == $userInfo['user_id']) {
            exception('不能自己转自己');
        }

        // 判断对方是否激活或者是否冻结
        if ($toUserInfo['frozen'] != 1 || $toUserInfo['activate'] != 1) {
            exception('对方已冻结或者未激活');
        }

        // 将参数强制转换为浮点型
        $low = floatval($blockChangeInfo['low']);
        $bei = floatval($blockChangeInfo['bei']);
        $out = floatval($blockChangeInfo['out']);
        $fee = floatval($blockChangeInfo['fee']);
        $dayNum = floatval($blockChangeInfo['day_num']);
        $dayTotal = floatval($blockChangeInfo['day_total']);

        // 最低金额
        if ($low > 0 && $data['num'] < $low) {
            exception('金额输入错误，转账' . $low . '起');
        }
        // 多少的倍数
        if ($bei > 0 && $data['num'] % $bei != 0) {
            exception('金额必须是' . $bei . '的倍数');
        }
        // 单笔最高
        if ($out > 0 && $data['num'] > $out) {
            exception('金额单笔最高' . $out);
        }

        // 判断每日转账次数 0 不限制
        if ($dayNum > 0) {
            $blockChangeLogDayNum = $blockChangeLogModel->getUserBlockChangeDayNum($userId);
            if ($blockChangeLogDayNum >= $dayNum) {
                exception('今天转账次数已封顶');
            }
        }

        // 判断每日最高转账数量 0 不限制
        if ($dayTotal > 0) {
            $blockChangeLogDayTotal = $blockChangeLogModel->getUserBlockChangeDayTotal($userId);
            if ($blockChangeLogDayTotal >= $dayTotal) {
                exception('今天转账数量已经封顶');
            }
        }

        // 推荐关系
        if ($blockChangeInfo['is_upper'] == 1 || $blockChangeInfo['is_lower'] == 1) {
            $blockChangeIsUpper = $blockChangeIsLower = 1;
            // 判断是否只能向上级转
            if ($blockChangeInfo['is_upper'] == 1) {
                $tjrArr = explode(',', $userInfo['tjr_path']);
                if (!in_array($toUserInfo['user_id'], $tjrArr)) {
                    $blockChangeIsUpper = 2;
                }
            }

            // 判断是否只能向下级转
            if ($blockChangeInfo['is_lower'] == 1) {
                $userIds = Users::getTeamUserId($userInfo);
                if (!in_array($toUserInfo['user_id'], $userIds)) {
                    $blockChangeIsLower = 2;
                }
            }

            if ($blockChangeIsUpper == 2 && $blockChangeIsLower == 2) {
                exception('只能转下级或者上级');
            }
        }

        // 接点关系
        if ($blockChangeInfo['is_above'] == 1 || $blockChangeInfo['is_below'] == 1) {

            $userBranchInfo = UsersBranch::getBranchInfoByUid($userInfo['user_id']);
            $toUserBranchInfo = UsersBranch::getBranchInfoByUid($toUserInfo['user_id']);
            $blockChangeIsUpper = $blockChangeIsLower = 1;
            // 判断是否只能向上级转
            if ($blockChangeInfo['is_above'] == 1) {
                $jdrArr = explode(',', $userBranchInfo['path']);
                if (!in_array($toUserBranchInfo['id'], $jdrArr)) {
                    $blockChangeIsUpper = 2;
                }
            }

            // 判断是否只能向下级转
            if ($blockChangeInfo['is_below'] == 1) {
                $jdrIds = UsersBranch::getBranchUid($userBranchInfo);
                if (!in_array($toUserBranchInfo['id'], $jdrIds)) {
                    $blockChangeIsLower = 2;
                }
            }

            if ($blockChangeIsUpper == 2 && $blockChangeIsLower == 2) {
                exception('只能转下线或者上线');
            }
        }

        // 默认备注为空
        $outNote = $enterNote = '';
        if ($fee > 0) {
            $poundage = $data['num'] * $fee / 100;
            if ($blockChangeInfo['fee_type'] == 1) {// 扣转出方手续费
                $outMoney = $data['num'] + $poundage;
                $enterMoney = $data['num'];
                $outNote = $data['num'] . '，转出手续费' . $poundage . '%';
            } else {// 扣转入方手续费
                $outMoney = $data['num'];
                $enterMoney = $data['num'] - $poundage;
                $enterNote = $data['num'] . '，转入手续费' . $poundage . '%';
            }
        } else {
            $outMoney = $enterMoney = $data['num'];
        }

        // 获取当前货币的余额
        $userBlock = get_Block_amount($userId, $bid, 1);

        // 查出货币的信息
        $blockInfo = (new Block())->getBlockInfo(['id' => $bid], 1, ['name_cn']);
        if ($outMoney > $userBlock) {
            exception($blockInfo['name_cn'] . '余额不足');
        }

        // 判断二级密码是否正确
        if (!Password::checkPayPassword($data['secpwd'], $userInfo)) {
            exception('二级密码错误');
        }

        Db::startTrans();
        try {
            // 结算到账
            $usersBlockModel->amountChange($userId, $bid, '-' . $outMoney, 101, $userInfo['account'] . '转至' . $toUserInfo['account'] . $outNote);
            $usersBlockModel->amountChange($toUserInfo['user_id'], $bid, $enterMoney, 102, $userInfo['account'] . '转入' . $toUserInfo['account'] . $enterNote);

            $blockChangeLogModel->addLog($userInfo, $toUserInfo, $bid, $toBid, $outMoney, $enterMoney, $fee, $poundage);

        } catch (\Exception $e) {
            Log::write('会员货币转账 会员id(' . $userId . '),对方会员id(' . $toUserInfo['user_id'] . '),block_id(' . $bid . '),金额(' . $outMoney . '),类型(' . 50 . '),备注 货币转账(' . $outNote . $enterMoney . '): ' . $e->getMessage(), 'error');
            Db::rollback();
            exception('操作失败');
        }
        Db::commit();

        return true;
    }


    /**
     * 查出对方账号信息
     * @param string $toAccount
     * @param int $bid
     * @param int $type
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \Exception
     */
    public function getToUserInfo($toAccount = '', $bid = 1, $type = 1)
    {
        $toUserInfo = false;
        $toAccount = $toAccount ? $toAccount : Request::param('toAccount');
        try {
            if (check_mobile($toAccount)) {
                $toUserData = UsersData::field('id')->getByMobile($toAccount);

                $toUserInfo = Users::field('user_id, account, frozen, activate')->getByDataId($toUserData['id']);
            } else {
                $toUserBlock = UsersBlock::where('address', $toAccount)->where('bid', $bid)->field('uid')->find();

                $toUserInfo = Users::where('user_id', $toUserBlock['uid'])->field('user_id,account, frozen, activate')->find();
            }
        } catch (\Exception $e) {
            Log::write('货币转账 获取转入者信息' . $e->getMessage(), 'error');
        }

        if (empty($toUserInfo) && $type == 1) {
            exception('对方钱包地址或者手机号不存在');
        }

        return $toUserInfo;
    }

    /**
     * 货币转换
     * @param $userInfo
     * @throws \Exception
     */
    public function doTransformAdd($userInfo)
    {
        // 接受参数
        $bid = Request::param('bid', '', 'int');
        $toBid = Request::param('to_bid', '', 'int');
        $num = Request::param('num', '', 'int');
        $secpwd = Request::param('secpwd', '', 'trim');

        // 判断二级密码是否正确
        if (!Password::checkPayPassword($secpwd, $userInfo)) {
            exception('二级密码错误');
        }

        // 获取转换参数
        $transformInfo = [];
        try {
            // 实例化
            $blockTransformModel = new BlockTransform();

            $transformInfo = $blockTransformModel->transformInfo($bid, $toBid);
        } catch (\Exception $e) {
            exception('网络错误，请稍后重试');
        }

        // 判断是否存在该转换
        if (!$transformInfo) {
            exception('网络错误，请稍后重试');
        }

        // 转换
        $fee = floatval($transformInfo['fee']);
        $dayTotal = floatval($transformInfo['day_total']);

        // 判断今天的兑换是否已经封顶
        if ($dayTotal > 0) {
            $blockTransformLogModel = new BlockTransformLog();
            $transformNum = $blockTransformLogModel->getUserBlockTransformDayTotal($bid, $toBid);
            if ($transformNum >= $transformInfo['day_total']) {
                exception('今日兑换已封顶');
            }
        }

        $blockNowPrice = $toBlockNowPrice = 0;
        try {
            $blockModel = new Block();
            // 获取转出货币的价格
            $blockNowPrice = $blockModel->getBlockInfo($bid, 1, ['now_price']);

            // 获取转入货币的价格
            $toBlockNowPrice = $blockModel->getBlockInfo($toBid, 1, ['now_price']);
        } catch (\Exception $e) {
            exception('网络错误，请稍后重试');
        }


        // 默认备注为空
        $outNote = $enterNote = $poundage = '';
        // 计算手续费
        if ($fee > 0) {
            $poundage = $num * $fee / 100;
            $outMoney = $num + $poundage;
            $enterMoney = $num;
            $outNote = $num . '，转换手续费' . $poundage . '%';
        } else {
            $outMoney = $enterMoney = $num;
        }
        $blockNames = get_block_name();

        // 获取当前货币的余额
        $userBlock = get_Block_amount($userInfo['user_id'], $bid, 1);
        if ($outMoney > $userBlock) {
            exception($blockNames[$bid] . '余额不足');
        }

        // 根据货币价格计算数量
        $toNum = floatval(($enterMoney * $blockNowPrice['now_price']) / $toBlockNowPrice['now_price']);
        Db::startTrans();
        try {
            $usersBlockModel = new UsersBlock();

            // 结算到账
            $usersBlockModel->amountChange($userInfo['user_id'], $bid, '-' . $outMoney, 103, $blockNames[$bid] . '转换' . $blockNames[$toBid] . $outNote);
            $usersBlockModel->amountChange($userInfo['user_id'], $toBid, $toNum, 103, $blockNames[$bid] . '转换' . $blockNames[$toBid] . $outNote);

            (new BlockTransformLog())->addLog($userInfo, $bid, $toBid, $outMoney, $enterMoney, $fee, $poundage, $blockNames[$bid] . '转换' . $blockNames[$toBid] . $outNote);

        } catch (\Exception $e) {
            Log::write('会员货币转换 会员id(' . $userInfo['user_id'] . ')  : ' . $blockNames[$bid] . '转换' . $blockNames[$toBid] . $e->getMessage(), 'error');
            Db::rollback();
            exception('操作失败');
        }
        Db::commit();
    }
}
