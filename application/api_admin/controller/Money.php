<?php

namespace app\api_admin\controller;

use app\api_admin\response\ReturnCode;
use app\common\model\Bank;
use app\common\model\money\MoneyCarry;
use app\common\model\money\MoneyCarryBankLog;
use app\common\model\money\MoneyChange;
use app\common\model\money\MoneyChangeLog;
use app\common\model\money\MoneyLog;
use app\common\model\money\MoneyTransform;
use app\common\model\money\MoneyTransformLog;
use app\common\model\money\UsersMoneyAdd;
use app\common\model\money\UsersMoneyLockLog;
use think\Request;
use app\common\model\money\Money as MoneyModel;
use think\db\Where;
use think\facade\Log;
use app\common\model\AdminLog;
use app\common\model\Users;
use app\common\model\money\UsersMoney;
use app\common\model\AdminUser;
use think\db;

class Money extends Base
{

    /**
     * 钱包设置列表
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function configList(Where $where)
    {
        try {
            $list = MoneyModel::where($where)->select();

            $configList = [];
            foreach ($list as $v) {
                $arr = $v;
                $arr['id'] = $v['money_id'];
                $arr['name'] = $v['name_cn'];
                $arr['logo'] = get_img_domain() . $v['logo'];

                $configList[] = $arr;
            }

            $data = [
                'code' => ReturnCode::SUCCESS_CODE,
                'data' => $configList
            ];

            return json()->data($data);
        } catch (\Exception $e) {

            Log::write('查询钱包信息失败：' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到钱包信息');
        }
    }

    /**
     * 编辑钱包信息
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     * @throws \think\Exception\DbException
     */
    public function editConfig(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = MoneyModel::getMoneyInfoById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }

        if ($request->isPost()) {
            try {
                $info->allowField([
                    'name_cn', 'logo', 'add_low', 'add_bei', 'add_out', 'c_pre', 't_pre', 'pay_low', 'pay_out', 'pay_bei'
                ])->save($request->param());

                $info->_afterUpdate();
                AdminLog::addLog('修改钱包信息', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改钱包信息失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info
            ]);
        }
    }

    /**
     * 会员余额列表
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function userMoneyList(Request $request, Where $where)
    {
        try {
            if ($account = $request->param('account', '', 'trim')) {
                $userId = (int)Users::where('account', $account)->value('user_id');
                $where['uid'] = $userId;
            }
            if ($moneyId = $request->param('money_id', '', 'intval')) {
                $where['mid'] = $moneyId;
            }
            $page = $request->get('p', '1', 'intval') - 1;
            $pageSize = $request->get('p_num', '10', 'intval');

            $list = UsersMoney::where($where)->order('id', 'desc')->limit($page * $pageSize, $pageSize)->select();

            $userMoneyList = [];
            $userIds = array_unique(get_arr_column($list, 'uid'));
            $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
            $moneyNames = MoneyModel::getMoneyNames();
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'account' => $users[$v['uid']] ?? '',
                    'wallet_name' => $moneyNames[$v['mid']] ?? '',
                    'amount_available' => $v['money'],
                    'froze_amount' => $v['frozen'],
                    'total_amount' => $v['money'] + $v['frozen']
                ];

                $userMoneyList[] = $arr;
            }
            if (count($userMoneyList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '没有会员钱包信息'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $userMoneyList,
                    'count' => UsersMoney::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询会员余额列表失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到会员钱包，请联系管理员');
        }
    }

    /**
     * 修改会员金额
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     * @throws \think\Exception\DbException
     */
    public function editUserMoney(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = UsersMoney::get($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }
        if ($request->isPost()) {
            Db::startTrans();
            try {
                $str = '';
                if ($info['money'] != $request->param('money')) {
                    $str .= '可用余额从' . $info['money'] . '修改成' . $request->param('money');
                }
                if ($info['frozen'] != $request->param('frozen')) {
                    $str .= '冻结金额从' . $info['frozen'] . '修改成' . $request->param('frozen');
                }

                $info->money = $request->param('money', '', 'floatval');
                $info->frozen = $request->param('frozen', '', 'floatval');
                $info->save();

                AdminLog::addLog('修改会员金额,会员id:' . $info['uid'] . ',钱包id:' . $info['mid'] . $str, $request->param(), $this->adminUser['admin_id']);

                Db::commit();
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
            } catch (\Exception $e) {
                Db::rollback();
                Log::write('修改会员金额失败: ' . $e->getMessage(), 'error');

                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '操作失败');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info
            ]);
        }
    }

    /**
     * 钱包金额拨出
     * @param Request $request
     * @param UsersMoney $userMoneyModel
     * @param AdminUser $adminUserModel
     * @return \think\response\Json|\think\response\View
     * @throws \think\Exception\DbException
     */
    public function outUserMoney(Request $request, UsersMoney $userMoneyModel, AdminUser $adminUserModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = UsersMoney::get($id);
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
                    $userMoneyModel->amountChange($info['uid'], $info['mid'], $money, 103, $note, ['admin_id' => $adminUserModel->getAdminUserId()]);

                    AdminLog::addLog('拨出钱包金额, 会员id:' . $info['uid'] . ',钱包id:' . $info['mid'] . ',金额: ' . $money . ',备注:' . $note, $request->param(), $this->adminUser['admin_id']);

                    Db::commit();
                    return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '操作成功');
                } catch (\Exception $e) {
                    Db::rollback();
                    Log::write('拨出钱包金额失败: ' . $e->getMessage(), 'error');
                    return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '操作失败');
                }
            } else {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '没有金额变动');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info
            ]);
        }
    }

    /**
     * 冻结会员金额
     * @param Request $request
     * @param UsersMoney $userMoneyModel
     * @param AdminUser $adminUserModel
     * @return \think\response\Json|\think\response\View
     */
    public function addLockMoney(Request $request, UsersMoney $userMoneyModel, AdminUser $adminUserModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = UsersMoney::get($id);
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
                $userMoneyModel->amountChange($info['uid'], $info['mid'], '-' . $money, 104, $note, ['admin_id' => $adminUserModel->getAdminUserId()]);
                $userMoneyModel->amountLock($info['uid'], $info['mid'], $money, 101, $note);

                AdminLog::addLog('冻结会员金额,会员id:' . $info['uid'] . ',钱包id:' . $info['mid'] . ',金额:' . $money, $request->param(), $this->adminUser['admin_id']);

                Db::commit();
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '操作成功');
            } catch (\Exception $e) {
                Db::rollback();
                Log::write('修改会员金额失败: ' . $e->getMessage(), 'error');

                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '操作失败');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info
            ]);
        }
    }

    /**
     * 钱包冻结日志
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function lockMoneyLogList(Request $request, Where $where)
    {
        $moneyNames = MoneyModel::getMoneyNames();
        $moneyLockLogTypes = money_lock_log_type();
        if ($request->param('is_get_data') == true) {
            try {
                if ($account = $request->param('account', '', 'trim')) {
                    $userId = (int)Users::where('account', $account)->value('user_id');
                    $where['uid'] = $userId;
                }
                if ($type = $request->param('type', '', 'intval')) {
                    $where['type'] = $type;
                }
                if ($moneyId = $request->param('money_id', '', 'intval')) {
                    $where['mid'] = $moneyId;
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

                $list = UsersMoneyLockLog::where($where)->order('id', 'desc')->limit($page * $pageSize, $pageSize)->select();

                $logList = [];
                $userIds = array_unique(get_arr_column($list, 'uid'));

                $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
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
                        'wallet_name' => $moneyNames[$v['mid']] ?? '',
                        'add_time' => date('Y-m-d H:i:s', $v['add_time']),
                        'lock_note' => $v['lock_note'],
                        'amount' => $v['frozen_money'],
                        'type' => $moneyLockLogTypes[$v['type']] ?? '',
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
                        'count' => UsersMoneyLockLog::where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询钱包冻结记录失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到冻结记录，请联系管理员');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'moneyLockLogTypes' => $moneyLockLogTypes,
                'moneyNames' => $moneyNames,
                'lockStatus' => UsersMoneyLockLog::$lockStatus
            ]);
        }
    }

    /**
     * 释放冻结钱包
     * @param Request $request
     * @param UsersMoney $userMoneyModel
     * @param AdminUser $adminUserModel
     * @return \think\response\Json
     */
    public function releaseMoney(Request $request, UsersMoney $userMoneyModel, AdminUser $adminUserModel)
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

                $lockLogs = UsersMoneyLockLog::whereIn('id', $id)->whereIn('status', [1, 2])->select();

                $successId = [];
                foreach ($lockLogs as $lockLog) {
                    $money = floatval($lockLog['stay_money']);
                    if ($money > 0) {
                        $userMoneyModel->amountChange($lockLog['uid'], $lockLog['mid'], $money, 105, $note, ['admin_id' => $adminUserModel->getAdminUserId()]);
                    }
                    $userMoneyModel->amountUnLock($lockLog, $money, $note);
                    $successId[] = $lockLog['id'];
                }
                AdminLog::addLog('释放会员冻结金额,日志id:' . implode(',', $id) . ', 成功id:' . implode(',', $successId), $request->param(), $this->adminUser['admin_id']);
                $num = count($successId);
                Db::commit();
                if ($num > 0) {
                    return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '成功释放' . $num . '条记录');
                } else {
                    return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '没有释放一条');
                }
            } catch (\Exception $e) {
                Db::rollback();
                Log::write('释放钱包冻结日志失败: （id: ' . implode(',', $id) . ', note: ' . $note . '）' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '释放失败');
            }
        }
    }

    /**
     * 钱包变动日志
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function moneyLogList(Request $request, Where $where)
    {
        $moneyLogTypes = money_log_type();
        $moneyNames = MoneyModel::getMoneyNames();
        if ($request->param('is_get_data') == true) {
            try {
                if ($account = $request->param('account', '', 'trim')) {
                    $userId = (int)Users::where('account', $account)->value('user_id');
                    $where['uid'] = $userId;
                }
                if ($comeAccount = $request->param('come_account', '', 'trim')) {
                    $userId = (int)Users::where('account', $comeAccount)->value('user_id');
                    $where['come_uid'] = $userId;
                }
                if ($type = $request->param('type', '', 'intval')) {
                    $where['is_type'] = $type;
                }
                if ($moneyId = $request->param('money_id', '', 'intval')) {
                    $where['mid'] = $moneyId;
                }
                if ($time = $request->param('time', '', 'trim')) {
                    $times = explode(' - ', $time);
                    $startTime = strtotime($times[0]);
                    $endTime = strtotime($times[1]);
                    $where['edit_time'] = ['between', [$startTime, $endTime]];
                }
                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');

                $list = MoneyLog::where($where)->order('id', 'desc')->limit($page * $pageSize, $pageSize)->select();

                $logList = [];
                $userIds = array_unique(array_merge(get_arr_column($list, 'uid'), get_arr_column($list, 'come_uid')));

                $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['id'],
                        'account' => $users[$v['uid']] ?? '',
                        'come_account' => $users[$v['come_uid']] ?? '',
                        'wallet_name' => $moneyNames[$v['mid']] ?? '',
                        'change_time' => date('Y-m-d H:i:s', $v['edit_time']),
                        'note' => $v['note'],
                        'amount' => $v['money'],
                        'total' => $v['total'],
                        'is_type' => $moneyLogTypes[$v['is_type']] ?? '',
                    ];
                    $logList[] = $arr;
                }
                if (count($logList) == 0) {
                    $data = [
                        'code' => ReturnCode::ERROR_CODE,
                        'msg' => '没有变动记录'
                    ];
                } else {
                    $data = [
                        'code' => ReturnCode::SUCCESS_CODE,
                        'data' => $logList,
                        'count' => MoneyLog::where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询钱包变动记录失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到变动记录，请联系管理员');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE,'', [
                'moneyLogTypes' => $moneyLogTypes,
                'moneyNames' => $moneyNames
            ]);
        }
    }


    /**
     * 钱包充值记录
     * @param Request $request
     * @param Where $where
     * @param Users $userModel
     * @param Bank $bankModel
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function remittanceRechargeLogList(Request $request, Where $where, Users $userModel, Bank $bankModel)
    {
        try {
            if ($account = $request->param('account', '', 'trim')) {
                $userId = (int)$userModel->where('account', $account)->value('user_id');
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
            $list = UsersMoneyAdd::where($where)
                ->order('id', 'desc')
                ->limit($page * $pageSize, $pageSize)->select();

            $logList = [];
            $userIds = array_unique(get_arr_column($list, 'uid'));

            $users = $userModel->whereIn('user_id', $userIds)->column('account', 'user_id');
            $moneyNames = MoneyModel::getMoneyNames();
            foreach ($list as $v) {
                $bank = $bankModel->getBankFieldById($v['bank_id'], 'username,account,name_cn');
                $arr = $v;
                $arr['account'] = $users[$v['uid']] ?? '';
                $arr['affirm_time'] = $v['affirm_time'] > 0 ? date('Y-m-d H:i:s', $v['affirm_time']) : '审核中';
                $arr['wallet_name'] = $moneyNames[$v['mid']] ?? '';
                $payImg = explode(',', $v['img']);

                foreach($payImg as $imgKey=>$imgVal) {
                    $payImg[$imgKey] = get_img_domain().$imgVal;
                }
                $arr['pay_img'] = $payImg;
                $arr['pay_time'] = date('Y-m-d H:i:s', $v['pay_time']);
                $arr['bank_account'] = $bank['account'];
                $arr['bank_username'] = $bank['username'];
                $arr['bank_name'] = $bank['name_cn'];
                $arr['status_msg'] = UsersMoneyAdd::$status[$v['status']] ?? '';
                $logList[] = $arr;
            }
            if (count($logList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '没有钱包充值记录'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $logList,
                    'count' => UsersMoneyAdd::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询钱包充值记录失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到钱包充值记录，请联系管理员');
        }
    }

    /**
     * 确认会员充值
     * @param Request $request
     * @param UsersMoneyAdd $usersMoneyAddModel
     * @return \think\Response|\think\response\Json
     * @throws \think\Exception\DbException
     */
    public function confirmRemittanceRecharge(Request $request, UsersMoneyAdd $usersMoneyAddModel)
    {
        if ($request->isPost()) {
            $id = $request->param('id', '', 'intval');
            $moneyAddInfo = $usersMoneyAddModel->getInfoById($id);

            Db::startTrans();
            try {
                $moneyAddInfo->affirmReview();
                AdminLog::addLog('确认会员充值', $moneyAddInfo->toArray(), $this->adminUser['admin_id']);
                Db::commit();
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '操作成功');
            } catch (\Exception $e) {
                Log::write('确认汇款充值失败: ' . $e->getMessage(), 'error');
                Db::rollback();
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '操作失败');
            }
        }
    }

    /**
     * 拒绝会员充值
     * @param Request $request
     * @param UsersMoneyAdd $usersMoneyAddModel
     * @return \think\Response|\think\response\Json
     * @throws \think\Exception\DbException
     */
    public function refuseRemittanceRecharge(Request $request, UsersMoneyAdd $usersMoneyAddModel)
    {
        if ($request->isPost()) {
            $id = $request->param('id', '', 'intval');
            $moneyAddInfo = $usersMoneyAddModel->getInfoById($id);

            Db::startTrans();
            try {
                $refuseContent = $request->param('refuse_content', '', 'trim');
                if ($refuseContent == '') {
                    return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '请输入拒绝理由');
                }
                $moneyAddInfo->refuseReview($refuseContent);
                AdminLog::addLog('拒绝会员充值', ['recharge_info' => $moneyAddInfo->toArray(), 'request_info' => $request->param()], $this->adminUser['admin_id']);
                Db::commit();
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '操作成功');
            } catch (\Exception $e) {
                Log::write('拒绝汇款充值失败: ' . $e->getMessage(), 'error');
                Db::rollback();
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '操作失败');
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
            $list = MoneyChange::where($where)->select();

            $changeConfigList = [];
            $moneyNames = get_money_name();
            foreach ($list as $v) {
                $arr = $v;
                $arr['wallet_name'] = $moneyNames[$v['mid']] ?? '未绑定钱包';
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
            Log::write('查询钱包转账参数失败: ' . $e->getMessage(), 'error');
            
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到配置，请联系管理员');
        }

    }

    /**
     * 修改转账参数
     * @param Request $request
     * @param MoneyChange $moneyChangeModel
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editChangeConfig(Request $request, MoneyChange $moneyChangeModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $moneyChangeModel->getChangeInfoById($id);
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
                    'status', 'low', 'out', 'bei', 'fee', 'fee_type', 'is_upper', 'is_lower', 'is_above', 'is_below', 'mid', 'to_per'
                ])->save($request->param());
                $info->_afterUpdate();
                AdminLog::addLog('修改货币转账参数', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改转账配置失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');
        } else {
            $moneyNames = MoneyModel::getMoneyNames();
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info,
                'moneyNames' => $moneyNames
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
            $list = MoneyChangeLog::where($where)
                ->order('id', 'desc')
                ->limit($page * $pageSize, $pageSize)->select();

            $changeLogList = [];
            $userIds = array_unique(array_merge(get_arr_column($list, 'uid'), get_arr_column($list, 'to_uid')));

            $users = $userModel->whereIn('user_id', $userIds)->column('account', 'user_id');
            $moneyNames = MoneyModel::getMoneyNames();
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'account' => $users[$v['uid']] ?? '',
                    'to_account' => $users[$v['to_uid']] ?? '',
                    'change_time' => date('Y-m-d H:i:s', $v['add_time']),
                    'wallet_name' => $moneyNames[$v['mid']] ?? '',
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
                    'count' => MoneyChangeLog::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询转账记录失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到转账记录，请联系管理员');
        }
    }

    /**
     * 兑换参数列表
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function transformConfigList(Request $request, Where $where)
    {
        try {
            $list = MoneyTransform::where($where)->select();

            $changeConfigList = [];
            $moneyNamess = MoneyModel::getMoneyNames();
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'wallet_name' => isset($moneyNamess[$v['mid']]) ? $moneyNamess[$v['mid']] : '未绑定钱包',
                    'to_wallet_name' => isset($moneyNamess[$v['to_mid']]) ? $moneyNamess[$v['to_mid']] : '未绑定钱包',
                    'low' => $v['low'],
                    'bei' => $v['bei'],
                    'out' => $v['out'],
                    'fee' => floatval($v['fee']),
                    'per' => floatval($v['per']),
                    'day_total' => $v['day_total'],
                    'status' => $v['status'] == 1 ? '启用' : '禁用',
                ];

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
            Log::write('查询钱包兑换参数失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到配置，请联系管理员');
        }
    }

    /**
     * 修改兑换参数
     * @param Request $request
     * @param MoneyTransform $moneyTransformModel
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editTransformConfig(Request $request, MoneyTransform $moneyTransformModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $moneyTransformModel->getTransformInfoById($id);
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
                    'status', 'low', 'out', 'bei', 'fee', 'day_total', 'mid', 'per', 'to_mid'
                ])->save($request->param());

                $info->_afterUpdate();
                AdminLog::addLog('修改钱包兑换参数', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改兑换配置失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');
            return json()->data(['code' => 1, 'msg' => '修改成功']);
        } else {
            $moneyNames = MoneyModel::getMoneyNames();
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info,
                'moneyNames' => $moneyNames
            ]);
        }
    }

    /**
     * 兑换记录
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
            $list = MoneyTransformLog::where($where)
                ->order('id', 'desc')
                ->limit($page * $pageSize, $pageSize)->select();

            $transformLogList = [];
            $userIds = array_unique(get_arr_column($list, 'uid'));

            $users = $userModel->whereIn('user_id', $userIds)->column('account', 'user_id');
            $moneyNames = MoneyModel::getMoneyNames();
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'account' => $users[$v['uid']] ?? '',
                    'transform_time' => date('Y-m-d H:i:s', $v['add_time']),
                    'wallet_name' => $moneyNames[$v['mid']] ?? '',
                    'to_wallet_name' => $moneyNames[$v['to_mid']] ?? '',
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
                    'count' => MoneyTransformLog::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询转换记录失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到转换记录，请联系管理员');
        }
    }

    /**
     * 提现参数列表
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function carryConfigList(Request $request, Where $where)
    {
        try {
            $list = MoneyCarry::where($where)->select();

            $changeConfigList = [];
            $moneyNames = MoneyModel::getMoneyNames();

            $statusData = [1 => '启用', 2 => '禁用'];

            foreach ($list as $v) {
                $arr = $v;
                $arr['mid'] = $moneyNames[$v['mid']] ?? '无';
                $arr['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
                $arr['out_time'] = date('Y-m-d H:i:s', $v['out_time']);
                $arr['fee'] = floatval($v['fee']) . '%';
                $arr['status'] = $statusData[$v['status']] ?? '';

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
            Log::write('查询钱包提现参数失败: ' . $e->getMessage(), 'error');
            
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未配置参数，请联系管理员');
        }
    }

    /**
     * 修改提现参数
     * @param Request $request
     * @param MoneyCarry $moneyCarryModel
     * @return \think\response\Json|\think\response\View
     */
    public function editCarryConfig(Request $request, MoneyCarry $moneyCarryModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $moneyCarryModel->getById($id);
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
                    'status', 'low', 'out', 'bei', 'fee', 'day_total', 'mid'
                ])->save($request->param());
                AdminLog::addLog('修改钱包提现参数', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改提现配置失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');
            return json()->data(['code' => 1, 'msg' => '修改成功']);
        } else {
            $moneyNames = MoneyModel::getMoneyNames();
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info,
                'moneyNames' => $moneyNames
            ]);
        }
    }

    /**
     * 提现记录
     * @param Request $request
     * @param Where $where
     * @param Users $userModel
     * @return \think\response\Json|\think\response\View
     */
    public function carryLogList(Request $request, Where $where, Users $userModel)
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
            $list = MoneyCarryBankLog::where($where)
                ->order('id', 'desc')
                ->limit($page * $pageSize, $pageSize)->select();

            $changeLogList = [];
            $userIds = get_arr_column($list, 'uid');

            $users = $userModel->whereIn('user_id', $userIds)->column('account', 'user_id');
            $moneyNames = MoneyModel::getMoneyNames();
            $bankNames = Bank::getBankNames();

            $statusData = MoneyCarryBankLog::$status;

            foreach ($list as $v) {
                $arr = $v;
                $arr['uid'] = $users[$v['uid']] ?? '';
                $arr['mid'] = $moneyNames[$v['mid']] ?? '';
                $arr['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
                $arr['opening_id'] = $bankNames[$v['opening_id']] ?? '无';
                $arr['status'] = $statusData[$v['status']] ?? '无';
                $arr['affirm_time'] = $v['affirm_time'] != '' ? date('Y-m-d H:i:s', $v['affirm_time']) : '';
                $arr['refuse_time'] = $v['refuse_time'] != '' ? date('Y-m-d H:i:s', $v['refuse_time']) : '';

                $changeLogList[] = $arr;
            }
            if (count($changeLogList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '没有提现记录'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $changeLogList,
                    'count' => MoneyCarryBankLog::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询提现记录失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到提现记录，请联系管理员');
        }
    }


    /**
     * 确认提现
     * @param Request $request
     * @return \think\Response|\think\response\Json
     */
    public function confirmCarry(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param('id');

            if (!$id) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '请选择');
            }
            Db::startTrans();
            try {
                $arrId = explode(',', $id);
                $carryList = MoneyCarryBankLog::whereIn('id', $arrId)->where('status', 1)->select();
                if (count($carryList) <= 0) {
                    return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '此信息不支持该操作');
                }
                $data = $carryList->toArray();
                $num = 0;
                foreach ($carryList as $v) {
                    $v->affirm_time = time();
                    $v->status = 9;
                    $v->save();
                    $num++;
                }
                Db::commit();
                AdminLog::addLog('确认会员提现', $data, $this->adminUser['admin_id']);
                if ($num > 0) {
                    return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '成功操作' . $num . '条数据');
                } else {
                    return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '没有任何数据发生变化');
                }
            } catch (\Exception $e) {
                Db::rollback();
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '操作失败');
            }
        }
    }


    /**
     * 拒绝提现
     * @param Request $request
     * @param UsersMoney $usersMoney
     * @return \think\Response|\think\response\Json
     */
    public function refuseCarry(Request $request, UsersMoney $usersMoney)
    {
        if ($request->isPost()) {
            $id = $request->param('id');
            $note = $request->param('note');

            if (!$id) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '请选择');
            }
            if ($note == '') {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '请输入拒绝理由');
            }
            Db::startTrans();
            try {
                $arrId = explode(',', $id);
                $carryList = MoneyCarryBankLog::whereIn('id', $arrId)->where('status', 1)->select();
                if (count($carryList) <= 0) {
                    return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '此信息不支持该操作');
                }
                $num = 0;
                $data = $carryList->toArray();
                foreach ($carryList as $v) {
                    $usersMoney->amountChange($v['uid'], $v['mid'], $v['add_money'], 121, '拒绝提现');
                    $v->status = 3;
                    $v->refuse_time = time();
                    $v->refuse = $note;
                    $v->save();
                    $num++;
                }

                Db::commit();
                AdminLog::addLog('拒绝会员提现', $data, $this->adminUser['admin_id']);
                if ($num > 0) {
                    return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '成功操作' . $num . '条数据');
                } else {
                    return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '没有任何数据发生变化');
                }
            } catch (\Exception $e) {
                Db::rollback();
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '操作失败');
            }
        }
    }


    /**
     * 删除提现日志
     * @param Request $request
     * @return \think\Response|\think\response\Json
     */
    public function delCarryLog(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param('id');

            if (!$id) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '请选择要删除的数据');
            }
            Db::startTrans();

            try {
                $arrId = explode(',', $id);
                $carryList = MoneyCarryBankLog::whereIn('id', $arrId)->select();
                if (count($carryList) <= 0) {
                    return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '数据错误');
                }
                $data = $carryList->toArray();
                foreach ($carryList as $v) {
                    $v->delete();
                }

                AdminLog::addLog('删除会员提现记录', $data, $this->adminUser['admin_id']);
                Db::commit();
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '操作成功');
            } catch (\Exception $e) {
                Db::rollback();
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '操作失败');
            }
        }
    }

}
