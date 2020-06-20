<?php

namespace app\common\logic;

use app\common\model\money\Money;
use think\Db;
use think\facade\Log;
use app\facade\Password;
use think\facade\Request;
use app\common\model\Users;
use app\common\model\UserBank;
use app\common\model\money\UsersMoney;
use app\common\model\money\MoneyCarry;
use app\common\model\money\MoneyChange;
use app\common\model\money\MoneyChangeLog;
use app\common\model\money\MoneyCarryBankLog;
use app\common\model\UserAuthName;

class MoneyLogic
{

    /**
     * 钱包转账
     * @param $data
     * @param $userInfo
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doMoneyChange($userInfo)
    {
        $mid = Request::param('money_id', '', 'intval');
        $toMid = Request::param('money_id', '', 'intval');
        $toNum = Request::param('toNum', '', 'intval');
        $toAccount = Request::param('toAccount');
        $secpwd = Request::param('secpwd', '', 'trim');
        $userId = intval($userInfo['user_id']);

        if ($userId <= 0) {
            exception('网络错误，请刷新后重试');
        }

        if ($toNum <= 0) {
            exception('转出数量不能为零');
        }

        // 查出收款方的账号信息
        $toUserMoneyInfo = $this->getToUserMoneyInfo($toAccount, $toMid);

        // 判断是否是自己转自己
        if ($toUserMoneyInfo['user_id'] == $userId) {
            exception('不能自己转自己');
        }

        //实例化model类
        $MoneyChangeModel = new MoneyChange();
        $UsersMoneyModel = new UsersMoney();
        $MoneyChangeLogModel = new MoneyChangeLog();

        // 获取转账参数
        $moneyChangeInfo = $MoneyChangeModel->getChangeMoneyInfo($mid, $toMid);

        // 将参数强制转换为浮点型
        $low = floatval($moneyChangeInfo['low']); //起点金额
        $bei = floatval($moneyChangeInfo['bei']); //倍率
        $out = floatval($moneyChangeInfo['out']); //单笔最高金额
        $fee = floatval($moneyChangeInfo['fee']); //手续费
        // 判断转账最低金额
        if ($low > 0 && $toNum < $low) {
            exception('金额输入错误，最低' . $low);
        }

        // 判断转账金额的倍率
        if ($bei > 0 && kmod($toNum, $bei) != 0) {
            exception('金额输入错误，必须是' . $bei . '的倍数');
        }

        // 判断转账最高金额
        if ($out > 0 && $toNum > $out) {
            exception('金额输入错误，单笔最高' . $out);
        }

        // 判断只能转上线 和 下线
        $changeTjrId = $changeZdrId = 0;
        if ($moneyChangeInfo['is_upper'] == 1) {
            $upperUserIdArr = explode(',', $userInfo['tjr_path']);
            $upperUserList = Users::whereIn('user_id', $upperUserIdArr)->column('user_id');

            if (!in_array($toUserMoneyInfo['user_id'], $upperUserList)) {
                $changeTjrId = 1;
            }
        }

        if ($moneyChangeInfo['is_lower'] == 1) {
            $tjr_path = $userInfo['tjr_path'] . ',' . $userInfo['user_id'];
            $lowerUserList = Users::whereLike('tjr_path', '%' . $tjr_path . '%')->column('user_id');

            if (!in_array($toUserMoneyInfo['user_id'], $lowerUserList)) {
                $changeZdrId = 1;
            }
        }
        if ($changeTjrId == 1 && $changeZdrId == 1) {
            exception('必须是上级或者下级会员才可转让');
        }

        // 默认备注
        $outNote = $enterNote = '';

        if ($fee > 0) {
            $poundage = $toNum * $fee / 100; //手续费
            if ($moneyChangeInfo['fee_type'] == 1) {// 扣转账方手续费
                $outMoney = $toNum + $poundage;
                $enterMoney = $toNum;
                $outNote = $toNum . '，转出手续费' . $poundage . '%';
            } else {// 扣收款方手续费
                $outMoney = $toNum;
                $enterMoney = $toNum - $poundage;
                $enterNote = $toNum . '，转入手续费' . $poundage . '%';
            }
        } else {
            $poundage = $toNum * $fee / 100;
            $outMoney = $enterMoney = $toNum;
        }
        // 获取当前货币的余额
        $userMoney = $UsersMoneyModel::getUsersMoneyByUserId($userId, $mid, 1);
        // 查出钱包的信息
        $moneyName = (new Money())->getMoneyInfoByType($mid, 3, ['name_cn']);
        if ($outMoney > $userMoney) {
            exception($moneyName . '余额不足');
        }

        // 判断二级密码是否正确
        if (!Password::checkPayPassword($secpwd, $userInfo)) {
            exception('二级密码错误');
        }

        //启动事务
        Db::startTrans();
        try {
            // 结算到账
            $UsersMoneyModel->amountChange($userId, $mid, '-' . $outMoney, 101, $userInfo['account'] . '转至' . $toUserMoneyInfo['account'] . '金额:' . $outNote, ['come_uid' => $toUserMoneyInfo['user_id']]); //转账扣款
            $UsersMoneyModel->amountChange($toUserMoneyInfo['user_id'], $toMid, $enterMoney, 102, $userInfo['account'] . '转入' . $toUserMoneyInfo['account'] . '金额:' . $enterNote, ['come_uid' => $userId]); //收款方收款

            $MoneyChangeLogModel->addLog($userInfo, $toUserMoneyInfo, $mid, $toMid, $outMoney, $enterMoney, $fee, $poundage, ''); //添加扣款金额日志
            Db::commit();
        } catch (\Exception $e) {
            Log::write('会员钱包转账 会员ID(' . $userId . '),对方会员ID(' . $toUserMoneyInfo['user_id'] . '),money_id(' . $mid . '),金额(' . $outMoney . '),类型(' . 51 . '),备注 钱包转账(' . $outNote . $enterMoney . '): ' . $e->getMessage(), 'error');
            Db::rollback();
            exception('转账失败');
        }

        return true;
    }

    /**
     * 获取收款方信息
     * @param string $toAccount
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \Exception
     */
    public function getToUserMoneyInfo($toAccount = '')
    {
        $toAccount = $toAccount ? $toAccount : Request::param('toAccount');
        try {
            if ($toAccount) {
                //实例化model类
                $usersModel = new Users();

                $toUserInfo = $usersModel->getUserByAccount($toAccount, 1, 'user_id,account');
            }
        } catch (\Exception $e) {
            Log::write('钱包转账 获取收款方信息' . $e->getMessage(), 'error');
        }
        if (empty($toUserInfo)) {
            exception('对方账号不存在');
        }

        return $toUserInfo;
    }

    /**
     * 会员提现
     * @param array $userInfo 会员信息
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function carryAdd($userInfo)
    {

        $authNameStatus = intval(UserAuthName::where(['uid' => $userInfo['user_id']])->value('status'));
        if ($authNameStatus != 9) {
            exception('请先完成实名认证');
        }

        $carryConfigId = Request::param('carry_config_id');
        $carryType = Request::param('carry_type');
        $carryConfig = MoneyCarryBankLog::getCarryConfig()[$carryConfigId] ?? [];
        if ($carryType == '') {
            exception('请选择提现方式');
        }
        if (empty($carryConfig)) {
            exception('请选择提现金额');
        }
        if (empty($carryConfig) || !in_array($carryType, ['weixin', 'alipay', 'bank'])) {
            exception('参数错误');
        }

        $receiptInfo = [];
        switch ($carryType) {
            case 'weixin':
                if ($userInfo['wx_name'] == '' || $userInfo['wx_code'] == '') {
                    exception('请先设置微信收款信息');
                }
                $receiptInfo = [
                    'wx_name' => $userInfo['wx_name'],
                    'wx_code' => $userInfo['wx_code']
                ];
                break;
            case 'alipay':
                if ($userInfo['zfb_name'] == '' || $userInfo['zfb_code'] == '') {
                    exception('请先设置支付宝收款信息');
                }
                $receiptInfo = [
                    'zfb_name' => $userInfo['zfb_name'],
                    'zfb_code' => $userInfo['zfb_code']
                ];
                break;
            case 'bank':
                $userBankInfo = UserBank::where(['uid' => $userInfo['user_id'], 'bank_default' => 1])->find();
                if (empty($userBankInfo)) {
                    exception('请完善银行卡信息');
                }
                $receiptInfo = [
                    'opening_id' => $userBankInfo['opening_id'],
                    'bank_address' => $userBankInfo['bank_address'],
                    'bank_account' => $userBankInfo['bank_account'],
                    'bank_name' => $userBankInfo['bank_name']
                ];
                break;
        }
        $amount = $carryConfig['amount'];
        $arrival = $carryConfig['arrival'];
        $carryNum = (int) ($carryConfig['num'] ?? 0);
        if ($carryNum > 0) {
            $count = MoneyCarryBankLog::where('uid', $userInfo['user_id'])
                    ->where('add_money', $amount)
                    ->count();
            if ($count >= $carryNum) {
                exception($amount . '只能提取' . $carryNum . '次');
            }
        }

        $usersMoney = get_money_amount($userInfo['user_id'], 3, 1);

        if ($amount > $usersMoney) { // 判断余额
            exception(get_money_name(3) . '不足');
        }
        $fee = 3;
        $feeMoney = 0;
        Db::startTrans();
        try {
            $UsersMoneyModel = new UsersMoney();
            $UsersMoneyModel->amountChange($userInfo['user_id'], 3, '-' . $amount, 120, $userInfo['account'] . '会员提现', [
                'come_uid' => $userInfo['user_id']
            ]); //提现扣款


            $data = [
                'uid' => $userInfo['user_id'],
                'mid' => 3,
                'add_time' => time(),
                'add_money' => $amount,
                'fee' => $fee,
                'fee_money' => $feeMoney,
                'out_money' => $arrival,
                'receipt_type' => $carryType,
                'receipt_info' => json_encode($receiptInfo, JSON_UNESCAPED_UNICODE)
            ];

            MoneyCarryBankLog::insert($data);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            Log::write('申请提现失败' . $e->getMessage(), 'error');
            exception('操作失败');
        }

        return true;
    }

}
