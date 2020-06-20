<?php

namespace app\api_admin\controller;

use app\api_admin\response\ReturnCode;
use app\common\model\block\BlockAdd;
use app\common\model\block\BlockChangeLog;
use app\common\model\block\BlockTransformLog;
use app\common\model\block\UsersBlock;
use app\common\model\block\UsersBlockLockLog;
use app\common\model\block\BlockChange;
use app\common\model\block\BlockTransform;
use app\common\model\block\BlockCarry;
use think\Request;
use think\db\Where;
use app\common\model\block\Block as BlockModel;
use think\facade\Log;
use app\common\model\Users;
use app\common\model\block\BlockLog;
use app\common\model\AdminLog;
use think\db;
use app\common\model\AdminUser;
use think\response\Json;

class Block extends Base
{

    /**
     * 货币列表
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function configList(Where $where)
    {
        try {
            $blockList = [];
            $list = BlockModel::where($where)->select();

            foreach ($list as $v) {
                $arr = $v;
                $arr['logo'] = get_img_domain() . $v['logo'];
                $arr['name'] = $v['name_cn'];
                $arr['float_price'] = (float)$arr['float_price'];

                $blockList[] = $arr;
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $blockList);
        } catch (\Exception $e) {
            Log::write('查询货币列表失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到货币信息');
        }
    }

    /**
     * 编辑货币
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function editConfig(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = BlockModel::getBlockInfoById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }
        if ($request->isPost()) {
            try {

                $info->name_cn = $request->param('name_cn');
                $info->logo = $request->param('logo');
                $info->thigh = $request->param('thigh');
                $info->total = $request->param('total', '', 'floatval');
                $info->now_price = $request->param('now_price', '', 'floatval');
                $info->float_price = $request->param('float_price       ', '', 'floatval');
                $info->day_price = $request->param('day_price', '', 'floatval');
                $info->save();
                AdminLog::addLog('修改货币参数', $request->param(), $this->adminUser['admin_id']);

                $info->_afterUpdate();

                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
            } catch (\Exception $e) {
                Log::write('修改货币失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE);
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $info);
        }
    }

    /**
     * 货币变动记录
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function blockLogList(Request $request, Where $where)
    {
        try {
            if ($account = $request->param('account', '', 'trim')) {
                $userId = (int)Users::where('account', $account)->value('user_id');
                $where['uid'] = $userId;
            }
            if ($comeAccount = $request->param('come_account', '', 'trim')) {
                $userId = (int)Users::where('account', $comeAccount)->value('user_id');
                $where['come_uid'] = $userId;
            }
            if ($kwd = $request->param('kwd', '', 'trim')) {
                $where['note'] = ['like', '%' . $kwd . '%'];
            }
            if ($time = $request->param('time', '', 'trim')) {
                $times = explode(' - ', $time);
                $startTime = strtotime($times[0]);
                $endTime = strtotime($times[1]);
                $where['edit_time'] = ['between', [$startTime, $endTime]];
            }
            $page = $request->get('p', '1', 'intval') - 1;
            $pageSize = $request->get('p_num', '10', 'intval');
            $list = BlockLog::where($where)
                ->order('id', 'desc')
                ->limit($page * $pageSize, $pageSize)->select();

            $userIds = array_unique(array_merge(get_arr_column($list, 'uid'), get_arr_column($list, 'come_uid')));

            $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
            $blockNames = get_block_name();

            $blockLogList = [];
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'account' => isset($users[$v['uid']]) ? $users[$v['uid']] : '',
                    'come_account' => isset($users[$v['come_uid']]) ? $users[$v['come_uid']] : '',
                    'wallet_name' => isset($blockNames[$v['bid']]) ? $blockNames[$v['bid']] : '',
                    'change_time' => date('Y-m-d H:i:s', $v['edit_time']),
                    'note' => $v['note'],
                    'amount' => $v['money'],
                    'total' => $v['total'],
                    'is_type' => block_log_type($v['is_type']),
                ];

                $blockLogList[] = $arr;
            }
            if (count($blockLogList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '没有变动记录'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $blockLogList,
                    'count' => BlockLog::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询货币变动记录失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到变动记录，请联系管理员');
        }
    }

    /**
     * 会员货币余额列表
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function userBlockList(Request $request, Where $where)
    {
        try {
            if ($account = $request->param('account', '', 'trim')) {
                $userId = (int)Users::where('account', $account)->value('user_id');
                $where['uid'] = $userId;
            }
            if ($blockId = $request->param('block_id', '', 'intval')) {
                $where['bid'] = $blockId;
            }
            $page = $request->get('p', '1', 'intval') - 1;
            $pageSize = $request->get('p_num', '10', 'intval');

            $list = UsersBlock::where($where)->order('id', 'desc')->limit($page * $pageSize, $pageSize)->select();

            $userBlockList = [];
            $userIds = array_unique(get_arr_column($list, 'uid'));
            $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
            $blockNames = BlockModel::getBlockNames();
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'account' => isset($users[$v['uid']]) ? $users[$v['uid']] : '',
                    'wallet_name' => $blockNames[$v['bid']] ?? '',
                    'amount_available' => $v['money'],
                    'froze_amount' => $v['frozen'],
                    'address' => $v['address'],
                    'total_amount' => $v['money'] + $v['frozen']
                ];

                $userBlockList[] = $arr;
            }
            if (count($userBlockList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '没有会员货币信息'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $userBlockList,
                    'count' => UsersBlock::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询会员货币余额列表失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到会员货币，请联系管理员');
        }
    }

    /**
     * 修改会员货币
     * @param Request $request
     * @param UsersBlock $userBlockModel
     * @return \think\response\Json|\think\response\View
     * @throws \think\Exception\DbException
     */
    public function editUserBlock(Request $request, UsersBlock $userBlockModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $userBlockModel->where('id', $id)->find();
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }
        if ($request->isPost()) {
            Db::startTrans();
            try {
                $str = '';
                if ($info['money'] != $request->param('money')) {
                    $userBlockModel->amountChange($info['uid'], $info['bid'], plus_minus_conversion($info['money'] - $request->param('money', '', 'floatval')));
                    $str .= '可用余额从' . $info['money'] . '修改成' . $request->param('money');
                }
                if ($info['frozen'] != $request->param('frozen')) {
                    $userBlockModel->amountLock($info['uid'], $info['bid'], plus_minus_conversion($info['frozen'] - $request->param('frozen', '', 'floatval')));
                    $str .= '冻结金额从' . $info['frozen'] . '修改成' . $request->param('frozen');
                }

                AdminLog::addLog('修改会员货币,会员id:' . $info['uid'] . ',货币id:' . $info['bid'] . $str, $request->param(), $this->adminUser['admin_id']);

                Db::commit();
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
            } catch (\Exception $e) {
                Db::rollback();
                Log::write('修改会员货币失败: ' . $e->getMessage(), 'error');

                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE);
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $info);
        }
    }

    /**
     * 货币金额拨出
     * @param Request $request
     * @param UsersBlock $userBlockModel
     * @param AdminUser $adminUserModel
     * @return \think\response\Json|\think\response\View
     */
    public function outUserBlock(Request $request, UsersBlock $userBlockModel, AdminUser $adminUserModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = UsersBlock::get($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }
        if ($request->isPost()) {
            $money = $request->param('money', '', 'floatval');
            $note = $request->param('note', '', 'trim');
            if ($money != 0) {
                Db::startTrans();
                try {
                    if ($note == '') {
                        return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '请输入备注');
                    }
                    $userBlockModel->amountChange($info['uid'], $info['bid'], $money, 104, $note, ['admin_id' => $adminUserModel->getAdminUserId()]);

                    AdminLog::addLog('拨出货币金额, 会员id:' . $info['uid'] . ',货币id:' . $info['bid'] . ',金额: ' . $money . ',备注:' . $note, $request->param(), $this->adminUser['admin_id']);

                    Db::commit();
                    return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
                } catch (\Exception $e) {
                    Db::rollback();
                    Log::write('拨出货币金额失败: ' . $e->getMessage(), 'error');
                    return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE);
                }
            } else {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '没有金额变动');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $info);
        }
    }

    /**
     * 冻结会员货币金额
     * @param Request $request
     * @param UsersBlock $userBlockModel
     * @param AdminUser $adminUserModel
     * @return \think\response\Json|\think\response\View
     */
    public function addLockBlock(Request $request, UsersBlock $userBlockModel, AdminUser $adminUserModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = UsersBlock::get($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }
        if ($request->isPost()) {
            $money = $request->param('money', '', 'floatval');
            $note = $request->param('note', '', 'trim');
            if ($money <= 0) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '冻结数量必须大于0');
            }
            if ($note == '') {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '请输入备注');
            }
            Db::startTrans();
            try {
                $userBlockModel->amountChange($info['uid'], $info['bid'], '-' . $money, 105, $note, ['admin_id' => $adminUserModel->getAdminUserId()]);
                $userBlockModel->amountLock($info['uid'], $info['bid'], $money, 101, $note);

                AdminLog::addLog('冻结会员金额,会员id:' . $info['uid'] . ',货币id:' . $info['bid'] . ',金额:' . $money, $request->param(), $this->adminUser['admin_id']);

                Db::commit();
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
            } catch (\Exception $e) {
                Db::rollback();
                Log::write('修改会员金额失败: ' . $e->getMessage(), 'error');

                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE);
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $info);
        }
    }

    /**
     * 货币冻结日志
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function lockBlockLogList(Request $request, Where $where)
    {
        if ($request->param('is_get_data') == true) {
            try {
                if ($account = $request->param('account', '', 'trim')) {
                    $userId = (int)Users::where('account', $account)->value('user_id');
                    $where['uid'] = $userId;
                }
                if ($type = $request->param('type', '', 'intval')) {
                    $where['type'] = $type;
                }
                if ($blockId = $request->param('block_id', '', 'intval')) {
                    $where['bid'] = $blockId;
                }
                if ($status = $request->param('status', '', 'intval')) {
                    $where['status'] = $status;
                }
                if ($kwd = $request->param('kwd', '', 'trim')) {
                    $where['lock_note'] = ['like', '%' . $kwd . '%'];
                }
                if ($time = $request->param('time', '', 'trim')) {
                    $times = explode(' - ', $time);
                    $startTime = strtotime($times[0]);
                    $endTime = strtotime($times[1]);
                    $where['add_time'] = ['between', [$startTime, $endTime]];
                }
                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');

                $list = UsersBlockLockLog::where($where)->order('id', 'desc')->limit($page * $pageSize, $pageSize)->select();

                $logList = [];
                $userIds = array_unique(get_arr_column($list, 'uid'));

                $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
                $blockNames = BlockModel::getBlockNames();
                foreach ($list as $v) {
                    if ($v['status'] == 1) {
                        $releaseType = '待释放 金额' . $v['stay_money'];
                    } elseif ($v['status'] == 2) {
                        $releaseType = '释放中 金额' . $v['stay_money'];
                    } elseif ($v['status'] == 9) {
                        $releaseType = '已释放';
                    }
                    $arr = [
                        'id' => $v['id'],
                        'account' => $users[$v['uid']] ?? '',
                        'wallet_name' => $blockNames[$v['bid']] ?? '',
                        'add_time' => date('Y-m-d H:i:s', $v['add_time']),
                        'lock_note' => $v['lock_note'],
                        'amount' => $v['frozen_money'],
                        'type' => UsersBlockLockLog::getLogType($v['type']),
                        'release_type' => $releaseType
                    ];
                    $logList[] = $arr;
                }
                if (count($logList) == 0) {
                    $data = [
                        'code' => ReturnCode::ERROR_CODE,
                        'msg' => '没有冻结记录'
                    ];
                } else {
                    $data = [
                        'code' => ReturnCode::SUCCESS_CODE,
                        'data' => $logList,
                        'count' => UsersBlockLockLog::where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询货币冻结记录失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到冻结记录，请联系管理员');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'blockLockLogTypes' => UsersBlockLockLog::getLogType(),
                'blockNames' => BlockModel::getBlockNames(),
                'lockStatus' => UsersBlockLockLog::$lockStatus
            ]);
        }
    }

    /**
     * 释放冻结货币
     * @param Request $request
     * @param UsersBlockLockLog $userBlockLockLog
     * @param UsersBlock $userBlockModel
     * @param AdminUser $adminUserModel
     * @return \think\response\Json
     */
    public function releaseBlock(Request $request, UsersBlockLockLog $userBlockLockLog, UsersBlock $userBlockModel, AdminUser $adminUserModel)
    {
        if ($request->isPost()) {
            Db::startTrans();
            try {
                $id = explode(',', $request->param('id'));
                foreach ($id as $k => $v) {
                    if (intval($v) <= 0) {
                        unset($id[$k]);
                    }
                }
                if (empty($id)) {
                    return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '请选择要执行数据');
                }
                $note = $request->param('note', '', 'trim');

                $lockLogs = $userBlockLockLog->whereIn('id', $id)->whereIn('status', [1, 2])->select();

                $successId = [];
                foreach ($lockLogs as $lockLog) {
                    $money = floatval($lockLog['stay_money']);
                    if ($money > 0) {
                        $userBlockModel->amountChange($lockLog['uid'], $lockLog['bid'], $money, 106, $note, ['admin_id' => $adminUserModel->getAdminUserId()]);
                    }
                    $userBlockModel->amountUnLock($lockLog, $money, $note);
                    $successId[] = $lockLog['id'];
                }
                AdminLog::addLog('释放会员冻结货币,日志id:' . implode(',', $id) . ', 成功id:' . implode(',', $successId), $request->param(), $this->adminUser['admin_id']);
                $num = count($successId);
                Db::commit();
                if ($num > 0) {
                    return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '成功释放' . $num . '条记录');
                } else {
                    return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '没有释放一条');
                }
            } catch (\Exception $e) {
                Db::rollback();
                Log::write('释放货币冻结日志失败: （id: ' . implode(',', $id) . ', note: ' . $note . '）' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '释放失败');
            }
        }
    }


    /**
     * 转账参数列表
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function changeConfigList(Request $request, Where $where)
    {
        try {
            $list = BlockChange::where($where)->select();

            $changeConfigList = [];
            $blockNames = get_block_name();
            foreach ($list as $v) {
                $arr = $v;
                $arr['wallet_name'] = $blockNames[$v['bid']] ?? '未绑定货币';
                $arr['to_wallet_name'] = isset($blockNames[$v['to_bid']]) ? $blockNames[$v['to_bid']] : ($blockNames[$v['bid']] ?? '未绑定货币');
                $arr['fee'] = (float)$v['fee'];
                $arr['to_per'] = (float)$v['to_per'];
                $arr['status'] = $v['status'] == 1 ? '启用' : '禁用';

                $changeConfigList[] = $arr;
            }
            if (count($changeConfigList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '未配置参数，请联系管理员'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $changeConfigList
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询货币转账参数失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到配置，请联系管理员');
        }
    }

    /**
     * 修改转账参数
     * @param Request $request
     * @param BlockChange $blockChangeModel
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editChangeConfig(Request $request, BlockChange $blockChangeModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $blockChangeModel->getChangeInfoById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }
        if ($request->isPost()) {
            try {
                foreach ($request->param() as $k => $v) {
                    if (!is_numeric($v)) {
                        return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '参数错误');
                    }
                }
                $info->allowField([
                    'status', 'low', 'out', 'bei', 'fee', 'fee_type', 'day_num', 'day_total', 'is_upper', 'is_lower', 'is_above', 'is_below', 'bid', 'to_per', 'to_bid'
                ])->save($request->param());
                $info->_afterUpdate();
                AdminLog::addLog('修改货币转账参数', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改转账配置失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
        } else {

            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info,
                'blockNames' => BlockModel::getBlockNames()
            ]);
        }
    }

    /**
     * 转账记录
     * @param Request $request
     * @param Where $where
     * @param Users $userModel
     * @return \think\response\Json|\think\response\View
     */
    public function changeLogList(Request $request, Where $where, Users $userModel)
    {
        try {
            if ($account = $request->param('account', '', 'trim')) {
                $userId = (int)$userModel->where('account', $account)->value('user_id');
                $where['uid'] = $userId;
            }
            if ($toAccount = $request->param('to_account', '', 'trim')) {
                $userId = (int)$userModel->where('account', $toAccount)->value('user_id');
                $where['to_uid'] = $userId;
            }
            if ($kwd = $request->param('kwd', '', 'trim')) {
                $where['note'] = ['like', '%' . $kwd . '%'];
            }
            if ($time = $request->param('time', '', 'trim')) {
                $times = explode(' - ', $time);
                $startTime = strtotime($times[0]);
                $endTime = strtotime($times[1]);
                $where['add_time'] = ['between', [$startTime, $endTime]];
            }
            $page = $request->get('p', '1', 'intval') - 1;
            $pageSize = $request->get('p_num', '10', 'intval');
            $list = BlockChangeLog::where($where)
                ->order('id', 'desc')
                ->limit($page * $pageSize, $pageSize)->select();

            $changeLogList = [];
            $userIds = array_unique(array_merge(get_arr_column($list, 'uid'), get_arr_column($list, 'to_uid')));

            $users = $userModel->whereIn('user_id', $userIds)->column('account', 'user_id');
            $blockNames = BlockModel::getBlockNames();
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'account' => $users[$v['uid']] ?? '',
                    'to_account' => $users[$v['to_uid']] ?? '',
                    'change_time' => date('Y-m-d H:i:s', $v['add_time']),
                    'wallet_name' => $blockNames[$v['bid']] ?? '',
                    'amount' => $v['money'],
                    'enter_amount' => $v['to_money'],
                    'fee_money' => $v['fee_money'],
                    'note' => $v['note']
                ];
                $changeLogList[] = $arr;
            }
            if (count($changeLogList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '没有转账记录'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $changeLogList,
                    'count' => BlockChangeLog::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询转账记录失败: ' . $e->getMessage(), 'error');

            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到转账记录，请联系管理员');
        }
    }


    /**
     * 转换参数列表
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function transformConfigList(Request $request, Where $where)
    {
        try {
            $list = BlockTransform::where($where)->select();

            $transformConfigList = [];
            $blockNames = BlockModel::getBlockNames();
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'wallet_name' => $blockNames[$v['bid']] ?? '未绑定货币',
                    'low' => (float)$v['low'],
                    'bei' => (float)$v['bei'],
                    'out' => (float)$v['out'],
                    'fee' => (float)$v['fee'],
                    'day_total' => (float)$v['day_total'],
                    'status' => $v['status'] == 1 ? '启用' : '禁用',
                    'to_wallet_name' => isset($blockNames[$v['to_bid']]) ? $blockNames[$v['to_bid']] : ($blockNames[$v['bid']] ?? '未绑定货币'),
                    'per' => (float)$v['per']
                ];

                $transformConfigList[] = $arr;
            }
            if (count($transformConfigList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '未配置参数，请联系管理员'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $transformConfigList
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询货币转换参数失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到配置，请联系管理员');
        }
    }

    /**
     * 修改转换参数
     * @param Request $request
     * @param BlockTransform $blockTransformModel
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editTransformConfig(Request $request, BlockTransform $blockTransformModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $blockTransformModel->getTransformInfoById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }
        if ($request->isPost()) {
            try {
                foreach ($request->param() as $k => $v) {
                    if (!is_numeric($v)) {
                        return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '参数错误');
                    }
                }
                $info->allowField([
                    'status', 'low', 'out', 'bei', 'fee', 'day_total', 'bid', 'per', 'to_bid'
                ])->save($request->param());
                $info->_afterUpdate();
                AdminLog::addLog('修改货币转换参数', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改转换配置失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info,
                'blockNames' => BlockModel::getBlockNames()
            ]);
        }
    }

    /**
     * 转换记录
     * @param Request $request
     * @param Where $where
     * @param Users $userModel
     * @return \think\response\Json|\think\response\View
     */
    public function transformLogList(Request $request, Where $where, Users $userModel)
    {
        try {
            if ($account = $request->param('account', '', 'trim')) {
                $userId = (int)$userModel->where('account', $account)->value('user_id');
                $where['uid'] = $userId;
            }
            if ($kwd = $request->param('kwd', '', 'trim')) {
                $where['note'] = ['like', '%' . $kwd . '%'];
            }
            if ($time = $request->param('time', '', 'trim')) {
                $times = explode(' - ', $time);
                $startTime = strtotime($times[0]);
                $endTime = strtotime($times[1]);
                $where['add_time'] = ['between', [$startTime, $endTime]];
            }
            $page = $request->get('p', '1', 'intval') - 1;
            $pageSize = $request->get('p_num', '10', 'intval');
            $list = BlockTransformLog::where($where)
                ->order('id', 'desc')
                ->limit($page * $pageSize, $pageSize)->select();

            $transformLogList = [];
            $userIds = array_unique(get_arr_column($list, 'uid'));

            $users = $userModel->whereIn('user_id', $userIds)->column('account', 'user_id');
            $blockNames = BlockModel::getBlockNames();
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'account' => $users[$v['uid']] ?? '',
                    'transform_time' => date('Y-m-d H:i:s', $v['add_time']),
                    'wallet_name' => $blockNames[$v['bid']] ?? '',
                    'to_wallet_name' => $blockNames[$v['to_bid']] ?? '',
                    'amount' => $v['money'],
                    'enter_amount' => $v['to_money'],
                    'fee_money' => $v['fee_money'],
                    'note' => $v['note']
                ];
                $transformLogList[] = $arr;
            }
            if (count($transformLogList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '没有转换记录'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $transformLogList,
                    'count' => BlockTransformLog::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询转换记录失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到转换记录，请联系管理员');
        }
    }


    /**
     * 提现记录
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function carryLogList(Request $request, Where $where)
    {
        try {
            if ($account = $request->param('account', '', 'trim')) {
                $userId = (int)Users::where('account', $account)->value('user_id');
                $where['uid'] = $userId;
            }
            if ($toAccount = $request->param('to_account', '', 'trim')) {
                $userId = (int)Users::where('account', $toAccount)->value('user_id');
                $where['to_uid'] = $userId;
            }
            if ($kwd = $request->param('kwd', '', 'trim')) {
                $where['note'] = ['like', '%' . $kwd . '%'];
            }
            if ($time = $request->param('time', '', 'trim')) {
                $times = explode(' - ', $time);
                $startTime = strtotime($times[0]);
                $endTime = strtotime($times[1]);
                $where['add_time'] = ['between', [$startTime, $endTime]];
            }
            $page = $request->get('p', '1', 'intval') - 1;
            $pageSize = $request->get('p_num', '10', 'intval');
            $list = BlockCarry::where($where)
                ->order('id', 'desc')
                ->limit($page * $pageSize, $pageSize)->select();

            $logList = [];
            $userIds = array_unique(get_arr_column($list, 'uid'));

            $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
            $blockNames = BlockModel::getBlockNames();
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'account' => $users[$v['uid']] ?? '',
                    'add_time' => date('Y-m-d H:i:s', $v['add_time']),
                    'wallet_name' => $blockNames[$v['bid']] ?? '',
                    'add_num' => $v['add_num'],
                    'out_num' => $v['out_num'],
                    'status' => $v['status'],
                    'status_msg' => BlockCarry::$carryStatus[$v['status']] ?? ''
                ];
                $logList[] = $arr;
            }
            if (count($logList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '没有提现记录'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $logList,
                    'count' => BlockCarry::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询提现记录失败: ' . $e->getMessage(), 'error');

            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到提现记录，请联系管理员');
        }
    }

    /**
     * 确认会员提现
     * @param Request $request
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function confirmCarry(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = BlockCarry::getCarryInfoById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '非法操作');
        }
        if ($info['status'] != 1 && $info['status'] != 2) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '此提现信息不支持此操作');
        }
        if ($request->isPost()) {
            $info->status = 9;
            $info->confirm_time = time();
            $res = $info->save();
            if ($res) {
                AdminLog::addLog('确认会员提现', $request->param(), $this->adminUser['admin_id']);
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '确认成功');
            } else {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '确认失败');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info,
                'status' => BlockCarry::$carryStatus
            ]);
        }
    }

    /**
     * 拒绝会员提现
     * @param Request $request
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     */
    public function refuseCarry(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = BlockCarry::getCarryInfoById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '非法操作');
        }
        if ($info['status'] != 1) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '此提现信息不支持此操作');
        }
        if ($request->isPost()) {
            if (!$content = $request->param('refuse_content', '', 'htmlspecialchars')) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '请输入拒绝理由');
            }
            $info->status = 3;
            $info->refuse_time = time();
            $info->refuse_content = $content;
            $res = $info->save();
            if ($res) {
                AdminLog::addLog('拒绝会员提现', $request->param(), $this->adminUser['admin_id']);
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '拒绝成功');
            } else {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '拒绝失败');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info,
                'status' => BlockCarry::$carryStatus
            ]);
        }
    }

    /**
     * 删除充值记录
     * @param Request $request
     * @return \think\Response
     */
    public function delCarryLog(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param('id', '', 'trim');
            if ($id == '') {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '请选择要删除的数据');
            }
            try {
                $id = explode(',', $id);
                $logList = BlockCarry::whereIn('id', $id)->select()->toArray();
                BlockCarry::whereIn('id', $id)->delete();
                AdminLog::addLog('删除会员提现记录', $logList, $this->adminUser['admin_id']);
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '删除成功');
            } catch (\Exception $e) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '删除失败');
            }
        }

    }


    /**
     * 货币充值记录
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function rechargeLogList(Request $request, Where $where)
    {
        try {
            if ($account = $request->param('account', '', 'trim')) {
                $userId = (int)Users::where('account', $account)->value('user_id');
                $where['uid'] = $userId;
            }
            if ($time = $request->param('time', '', 'trim')) {
                $times = explode(' - ', $time);
                $startTime = strtotime($times[0]);
                $endTime = strtotime($times[1]);
                $where['add_time'] = ['between', [$startTime, $endTime]];
            }
            $page = $request->get('p', '1', 'intval') - 1;
            $pageSize = $request->get('p_num', '10', 'intval');
            $list = BlockAdd::where($where)
                ->order('id', 'desc')
                ->limit($page * $pageSize, $pageSize)->select();

            $logList = [];
            $userIds = array_unique(get_arr_column($list, 'uid'));

            $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
            $blockNames = BlockModel::getBlockNames();
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'account' => $users[$v['uid']] ?? '',
                    'add_time' => date('Y-m-d H:i:s', $v['add_time']),
                    'wallet_name' => $blockNames[$v['bid']] ?? '',
                    'amount' => $v['add_money'],
                    'enter_amount' => $v['actual_money'],
                    'img' => explode(',', $v['img']),
                    'status' => $v['status'],
                    'status_msg' => BlockAdd::$rechargeStatus[$v['status']] ?? ''
                ];
                $logList[] = $arr;
            }
            if (count($logList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '没有货币充值记录'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $logList,
                    'count' => BlockAdd::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询货币充值记录失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到货币充值记录，请联系管理员');
        }
    }

    /**
     * 确认会员充值
     * @param Request $request
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function confirmRecharge(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param('id', 0, 'intval');

            $info = BlockAdd::where('id', $id)->find();
            if (empty($info) || $info['status'] != 1) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '此信息不支持操作，请刷新后重试');
            }
            try {
                $info->status = 9;
                $info->affirm_time = time();
                $info->save();

                $userBlockModel = new UsersBlock();
                $userBlockModel->amountChange($info['uid'], $info['bid'], $info['actual_money'], 131, '充值');

                AdminLog::addLog('确认会员充值', $info->toArray(), $this->adminUser['admin_id']);

                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
            } catch (\Exception $e) {
                Log::write('确认汇款充值失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE);
            }
        }
    }

    /**
     * 拒绝会员充值
     * @param Request $request
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function refuseRecharge(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param('id', 0, 'intval');

            $info = BlockAdd::where('id', $id)->find();
            if (empty($info) || $info['status'] != 1) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '此信息不支持操作，请刷新后重试');
            }

            $refuseContent = $request->param('refuse_content', '', 'htmlspecialchars');
            if ($refuseContent == '') {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '请输入拒绝理由');
            }
            try {
                $info->status = 3;
                $info->refuse = $refuseContent;
                $info->refuse_time = time();
                $info->save();

                AdminLog::addLog('拒绝会员充值', $info->toArray(), $this->adminUser['admin_id']);

                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
            } catch (\Exception $e) {
                Log::write('拒绝汇款充值失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE);
            }
        }
    }

    /**
     * 删除充值记录
     * @param Request $request
     * @return \think\Response
     */
    public function delRechargeLog(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param('id', '', 'trim');
            if ($id == '') {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '请选择要删除的数据');
            }
            try {
                $id = explode(',', $id);
                $logList = BlockAdd::whereIn('id', $id)->select()->toArray();
                BlockAdd::whereIn('id', $id)->delete();
                AdminLog::addLog('删除会员充值记录', $logList, $this->adminUser['admin_id']);
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '删除成功');
            } catch (\Exception $e) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '删除失败');
            }
        }

    }


}