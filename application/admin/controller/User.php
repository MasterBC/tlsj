<?php

namespace app\admin\controller;

use app\common\logic\UserLogic;
use app\common\model\grade\LeaderLog;
use app\common\model\grade\LevelLog;
use app\common\model\UserAuthName;
use app\common\model\UsersLog;
use app\common\model\money\UsersMoney;
use app\common\model\money\MoneyLog;
use think\Db;
use think\helper\Time;
use think\Request;
use think\db\Where;
use app\common\model\Users;
use think\facade\Log;
use app\common\logic\RegLogic;
use app\common\model\AdminLog;
use app\common\model\WebMessage;
use app\common\model\UserBank as UserBankModel;
use app\common\model\Bank;
use app\common\model\Message as MessageModel;

class User extends Base
{

    /**
     * 会员列表
     * @param Request $request
     * @param Users $userModel
     * @return \think\response\Json|\think\response\View
     */
    public function userList(Request $request, Users $userModel)
    {
        if ($request->isAjax()) {
            try {
                $where = new Where;
                if ($userType = $request->param('user_type', '', 'trim')) {
                    if ($userType == 'day_new') {
                        $where['reg_time'] = ['between', Time::today()];
                    } elseif ($userType == 'no_activate') {
                        $where['activate'] = ['<>', 1];
                    } elseif ($userType == 'activate') {
                        $where['activate'] = 1;
                    } elseif ($userType == 'lock') {
                        $where['frozen'] = ['<>', 1];
                    }
                }
                if ($userId = $request->param('id', '', 'intval')) {
                    $where['user_id'] = $userId;
                }
                if ($levelId = $request->param('level_id', '', 'intval')) {
                    $where['level'] = $levelId;
                }
                if ($account = $request->param('account')) {
                    $where['account'] = ['like', '%' . $account . '%'];
                }

                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');

                $list = $userModel->where($where)->limit($page * $pageSize, $pageSize)->order('user_id desc')->select();
                $userList = [];
                $userTjrIds = get_arr_column($list, 'tjr_id');
                $userBdrIds = get_arr_column($list, 'bdr_id');
                $userIds = array_unique(array_merge($userTjrIds, $userBdrIds));
                $users = $userModel->whereIn('user_id', $userIds)->column('account', 'user_id');
                $levelNames = get_level_name();
                $leaderNames = get_leader_name();
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['user_id'],
                        'account' => $v['account'],
                        'mobile' => $v['mobile'],
                        'email' => $v['email'],
                        'frozen' => $v['frozen'],
                        'tjr_num' => $userModel->where('tjr_id',$v['user_id'])->count(),
                        'team_num' => $userModel->getTeamUserNum($v),
                        'invitation_code' => $v['reg_code'] ? $v['reg_code'] : '无',
                        'bdr_account' => isset($users[$v['bdr_id']]) ? $users[$v['bdr_id']] : '无',
                        'tjr_account' => isset($users[$v['tjr_id']]) ? $users[$v['tjr_id']] : '无',
                        'level' => isset($levelNames[$v['level']]) ? $levelNames[$v['level']] : '无',
                        'is_product' => $v['is_hold_dividend_product'] == 1 ? '开启' : '关闭',
                        'is_niuqi' => $v['is_niuqi'] == 1 ? '开启' : '关闭',
                        'is_zilvu' => $v['is_zilvu'] == 1 ? '开启' : '关闭',
                        'leader' => isset($leaderNames[$v['leader']]) ? $leaderNames[$v['leader']] : '-',
                        'reg_time' => $v['reg_time'] ? date('Y-m-d H:i:s', $v['reg_time']) : '',
                        'jh_time' => $v['activate'] == 1 ? date('Y-m-d H:i:s', $v['jh_time']) : '未激活',
                    ];
                    $userList[] = $arr;
                }
                $count = $userModel::where($where)->count();
                $userNum = [
                    'all' => $userModel::count(),
                    'dayNew' => $userModel::whereBetween('reg_time', Time::today())->count(),
                    'lock' => $userModel::where('frozen', '<>', 1)->count(),
                    'noActivate' => $userModel::where('activate', '<>', 1)->count()
                ];
                if ($count == 0) {
                    $data = [
                        'code' => -1,
                        'msg' => '没有查询到会员',
                        'userNum' => $userNum
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $userList,
                        'count' => $count,
                        'userNum' => $userNum
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询会员列表失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '没有查询到会员'
                ];
                return json()->data($data);
            }
        } else {
            $levels = \app\common\model\grade\Level::getLevelNames();
            $assignData = [
                'levels' => $levels
            ];
            return view('user/user_list', $assignData);
        }
    }

    /**
     * 修改会员信息
     * @param Request $request
     * @param Users $userModel
     * @param UserLogic $userLogic
     * @return \think\response\Json|\think\response\View
     */
    public function editUserData(Request $request, Users $userModel, UserLogic $userLogic)
    {
        try {
            $userId = $request->param('id', '', 'intval');
            $userInfo = $userModel->where('user_id', $userId)->find();
            if ($request->isPost()) {
                $result = $this->validate($request->param(), 'app\common\validate\UserData.adminSaveUserData');
                if ($result !== true) {
                    return json(['code' => -1, 'msg' => $result]);
                }
                AdminLog::addLog('修改会员信息', $request->param());
                $userLogic->adminEditUserData($userInfo);

                return json()->data(['code' => 1, 'msg' => '修改成功']);
            }
        } catch (\Exception $e) {
            Log::write('修改会员信息失败: ' . $e->getMessage(), 'error');
            $this->error('操作失败');
        }
        return view('user/edit_user_data', ['userInfo' => $userInfo]);
    }

    /**
     * 删除会员
     * @param Request $request
     * @return \think\Response|\think\response\Json
     */
    public function delUser(Request $request)
    {
        if ($request->isAjax()) {
            $id = $request->param('id', '', 'trim');

            $id = explode(',', $id);
            if (!$id) {
                return json()->data(['code' => -1, 'msg' => '网络错误，请刷新后重试']);
            }

            $userInfo = Users::whereIn('user_id', $id)->select()->toArray();
//            if (!$userInfo) {
//                return json()->data(['code' => -1, 'msg' => '网络错误，请刷新后重试']);
//            }

            try {
                foreach ($userInfo as $value) {
                    $tjrCount = Users::where('tjr_id', $value['user_id'])->count();

                    if ($tjrCount > 0) {
                        return json()->data(['code' => -1, 'msg' => $value['account'] . '下级有人，不能删除']);
                    }

                    Users::where('user_id', $value['user_id'])->delete();
                }

                AdminLog::addLog('删除会员', $userInfo);

                return json()->data(['code' => 1, 'msg' => '删除成功']);
            } catch (\Exception $e) {
                Log::write('删除会员信息失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
        }
    }

    /**
     * 修改会员等级
     * @param Request $request
     * @param Users $userModel
     * @param LevelLog $levelLogModel
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editLevel(Request $request, Users $userModel, LevelLog $levelLogModel)
    {
        $userId = $request->param('id', '', 'intval');
        $userInfo = $userModel->where(['user_id' => $userId])->field('user_id,level,account')->find();
        $levelNames = get_level_name();
        if ($request->isAjax()) {
            try {
                $userId = $request->param('id', '', 'intval');
                $level = $request->param('level', '', 'intval');

                if ($userInfo['level'] == $level) {
                    return json()->data(['code' => -1, 'msg' => '请选择改动的级别']);
                }

                $note = $request->param('note', '', 'trim');
                $userModel->where(['user_id' => $userId])->update(['level' => $level]);
                $levelLogModel->addLevelLog($userId, $userInfo['level'], $level, 9, $note);

                $xin = $levelNames[$userInfo['level']] ?? '';
                $xinTwo = $levelNames[$level] ?? '';

                AdminLog::addLog('修改会员等级：' . $userInfo['account'] . $xin . ' > ' . $xinTwo, $request->param());
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                Log::write('修改会员等级失败' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        }

        $this->assign('levelNames', $levelNames);
        $this->assign('userInfo', $userInfo);
        return view('user/edit_level');
    }

    /**
     * 修改会员等级
     * @param Request $request
     * @param Users $userModel
     * @param LevelLog $levelLogModel
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editLeader(Request $request, Users $userModel, LeaderLog $leaderLog)
    {
        $userId = $request->param('id', '', 'intval');
        $userInfo = $userModel->where(['user_id' => $userId])->field('user_id,leader,account')->find();
        $leaderNames = get_leader_name();
        if ($request->isAjax()) {
            try {
                $userId = $request->param('id', '', 'intval');
                $leader = $request->param('leader', '', 'intval');

                if ($userInfo['leader'] == $leader) {
                    return json()->data(['code' => -1, 'msg' => '请选择改动的级别']);
                }

                $note = $request->param('note', '', 'trim');
                $userModel->where(['user_id' => $userId])->update(['leader' => $leader]);
                $leaderLog->addLog($userId, $userInfo['leader'], $leader, 9, $note);

                $xin = $leaderNames[$userInfo['leader']] ?? '';
                $xinTwo = $leaderNames[$leader] ?? '';

                AdminLog::addLog('修改领导等级：' . $userInfo['account'] . $xin . ' > ' . $xinTwo, $request->param());
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                Log::write('修改领导等级失败' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        }

        $this->assign('leaderNames', $leaderNames);
        $this->assign('userInfo', $userInfo);
        return view('user/edit_leader');
    }

    /**
     * 发送邮件
     * @param Request $request
     * @param Users $userModel
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sendMail(Request $request, Users $userModel)
    {
        $userId = $request->param('id');
        $sendType = $request->param('sendType', '1', 'intval');

        if ($sendType == 1) {
            $userList = $userModel::withJoin('userData', 'left')->whereIn('user_id', $userId)->field('user_id,data_id,nickname')->select();
        } else {
            $userList = [];
        }
        if ($request->isPost()) {
            $content = $request->param('content');
            if ($content == '') {
                return json()->data(['code' => -1, 'msg' => '请输入发送内容']);
            }
            if ($sendType == 2) {
                $userList = $userModel::withJoin('userData', 'left')->field('user_id,data_id,nickname')->select();
//                $userList = $userModel::withJoin('userData','left')->field('user_id,data_id,nickname')->select();
            }
            $emails = $names = [];
            foreach ($userList as $v) {
                if (check_mail($v['user_data']['email'])) {
                    $emails[] = $v['user_data']['email'];
                    $names[] = $v['nickname'];
                }
            }
            try {
                AdminLog::addLog('给会员发送邮件', ['email' => $emails, 'name' => $names, 'request' => $request->param()]);
                send_mail($emails, $names, $request->param('subject'), $content);
                return json()->data(['code' => 1, 'msg' => '发送成功']);
            } catch (\Exception $e) {
                Log::write('发送邮件失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '发送失败']);
            }
        } else {
            if ($userId) {
                $sendType = 1;
            } else {
                $sendType = 2;
            }

            return view('user/send_mail', ['sendType' => $sendType, 'userList' => $userList]);
        }
    }

    /**
     * 发送站内信
     * @param Request $request
     * @param Users $userModel
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function sendMessage(Request $request, Users $userModel, WebMessage $webMessageModel)
    {
        $userId = $request->param('id');
        $sendType = $request->param('sendType', '1', 'intval');

        if ($sendType == 1) {
            $userList = $userModel::whereIn('user_id', $userId)->field('user_id,nickname,account')->select();
        } else {
            $userList = [];
        }
        if ($request->isPost()) {
            $content = $request->param('content');
            if ($content == '') {
                return json()->data(['code' => -1, 'msg' => '请输入发送内容']);
            }
            if ($sendType == 2) {
                $userList = $userModel::field('user_id,nickname,account')->select();
            }
            try {
                AdminLog::addLog('给会员发送站内信', $request->param());
                $webMessageModel->sendMessage(get_arr_column($userList, 'user_id'), $content);
                return json()->data(['code' => 1, 'msg' => '发送成功']);
            } catch (\Exception $e) {
                Log::write('发送邮件失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '发送失败']);
            }
        } else {
            if ($userId) {
                $sendType = 1;
            } else {
                $sendType = 2;
            }

            return view('user/send_message', ['sendType' => $sendType, 'userList' => $userList]);
        }
    }

    /**
     * 注册会员
     * @param Request $request
     * @param RegLogic $regLogic
     * @return \think\response\Json|\think\response\View
     */
    public function addUser(Request $request, RegLogic $regLogic)
    {
        if ($request->isPost()) {
            $result = $this->validate($request->param(), 'app\wap\validate\Reg.adminAddUser');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }
            try {
                AdminLog::addLog('注册会员', $request->param());
                $regLogic->adminAddUser();

                return json(['code' => 1, 'msg' => '注册成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            return view('user/add_user');
        }
    }

    /**
     * 登录会员
     * @param Request $request
     * @param Users $userModel
     * @return array
     */
    public function userLogin(Request $request, Users $userModel)
    {
        if ($request->isPost()) {
            if (!$userId = $request->param('id', '', 'intval')) {
                return json()->data(['code' => -1, 'msg' => '参数错误']);
            }

            $userInfo = $userModel->getByUserId($userId);
            if (empty($userInfo)) {
                return json()->data(['code' => -1, 'msg' => '会员不存在']);
            }
            AdminLog::addLog('登陆' . $userInfo['account'] . '会员中心', $userInfo->toArray());

            $userInfo->setSession();

            return json()->data(['code' => 1, 'msg' => '登录成功']);
        } else {
            return json()->data([]);
        }
    }

    /**
     * 冻结会员
     * @param Request $request
     * @param UserLogic $userLogic
     * @return \think\response\Json
     */
    public function lockUser(Request $request, UserLogic $userLogic)
    {
        if ($request->isPost()) {

            try {
                $userLogic->lockUser($request->param('id', '', 'intval'), $request->param('note'));

                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            return json()->data([]);
        }
    }

    /**
     * 解除会员冻结
     * @param Request $request
     * @param UserLogic $userLogic
     * @return \think\response\Json
     */
    public function unLockUser(Request $request, UserLogic $userLogic)
    {
        if ($request->isPost()) {

            try {
                $userLogic->unLockUser($request->param('id', '', 'intval'));

                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            return json()->data([]);
        }
    }

    /**
     * 会员动态日志
     * @param Request $request
     * @param Where $where
     * @param UsersLog $userLogModel
     * @param Users $userModel
     * @return \think\response\Json|\think\response\View
     */
    public function userLogList(Request $request, Where $where, UsersLog $userLogModel, Users $userModel)
    {
        $userLogTypes = user_log_type();
        if ($request->isAjax()) {
            try {
                if ($account = $request->param('account', '', 'trim')) {
                    $userId = (int) $userModel->where('account', $account)->value('user_id');
                    $where['uid'] = $userId;
                }
                if ($kwd = $request->param('kwd', '', 'trim')) {
                    $where['note'] = ['like', '%' . $kwd . '%'];
                }
                if ($type = $request->param('type', '', 'intval')) {
                    $where['type'] = $type;
                }
                if ($time = $request->param('time', '', 'trim')) {
                    $times = explode(' - ', $time);
                    $startTime = strtotime($times[0]);
                    $endTime = strtotime($times[1]);
                    $where['add_time'] = ['between', [$startTime, $endTime]];
                }

                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');
                $list = $userLogModel->where($where)->limit($page * $pageSize, $pageSize)->order('id', 'desc')->select();

                $userIds = get_arr_column($list, 'uid');
                $users = $userModel->whereIn('user_id', $userIds)->column('account', 'user_id');

                $logList = [];
                foreach ($list as $k => $v) {
                    $arr = [
                        'id' => $v['id'],
                        'add_time' => date('Y-m-d H:i:s', $v['add_time']),
                        'note' => $v['note'],
                        'account' => isset($users[$v['uid']]) ? $users[$v['uid']] : '',
                        'ip' => $v['log_ip'],
                        'equipment' => $v['equipment'],
                        'type' => user_log_type($v['type'])
                    ];

                    $logList[] = $arr;
                }
                if (count($logList) == 0) {
                    $data = [
                        'code' => -1,
                        'msg' => '没有动态'
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $logList,
                        'count' => $userLogModel->where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询会员的动态日志失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取到动态日志，请联系管理员',
                ];
                return json()->data($data);
            }
        } else {
            return view('user/user_log_list', ['userLogTypes' => $userLogTypes]);
        }
    }

    /**
     * 会员银行卡列表
     * @param Request $request
     * @param Where $where
     * @param UserBankModel $userBankModel
     * @param Users $userModel
     * @return \think\response\Json|\think\response\View
     */
    public function userBankList(Request $request, Where $where, UserBankModel $userBankModel, Users $userModel)
    {
        if ($request->isAjax()) {
            try {
                if ($account = $request->param('account', '', 'trim')) {
                    $userId = (int) $userModel->where('account', $account)->value('user_id');
                    $where['uid'] = $userId;
                }
                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');

                $list = $userBankModel->where($where)->order('id', 'desc')->limit($page * $pageSize, $pageSize)->select();

                $userIds = array_unique(get_arr_column($list, 'uid'));

                $users = $userModel->whereIn('user_id', $userIds)->column('account', 'user_id');
                $bankList = [];
                $bankNames = Bank::getBankNames();
                foreach ($list as $v) {
                    $arr = [
                    'id' => $v['id'],
                    'account' => $users[$v['uid']] ?? '',
                    'bank_name' => $bankNames[$v['opening_id']] ?? '',
                    'bank_address' => $v['bank_address'],
                    'bank_account' => $v['bank_account'],
                    'bank_username' => $v['bank_name'],
                    'is_default' => $v['bank_default'] == 1 ? '是' : '否'
                    ];

                    $bankList[] = $arr;
                }

                if (count($bankList) == 0) {
                    $data = [
                        'code' => -1,
                        'msg' => '没有银行卡信息'
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $bankList,
                        'count' => $userBankModel->where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询银行卡信息失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取到银行卡信息，请联系管理员',
                ];
                return json()->data($data);
            }
        } else {

            return view('user/user_bank/user_bank_list');
        }
    }

    /**
     * 添加会员银行卡
     * @param Request $request
     * @param UserBankModel $userBankModel
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function addUserBank(Request $request, UserBankModel $userBankModel)
    {
        if ($request->isPost()) {
            $account = $request->param('account', '', 'trim');
            if ($account == '') {
                return json()->data(['code' => -1, 'msg' => '请输入会员账号']);
            }
            try {
                $userInfo = Users::where('account', $account)->field('user_id')->find();
                if (empty($userInfo)) {
                    return json()->data(['code' => -1, 'msg' => '会员不存在']);
                }
                $userBankModel->addBank($userInfo['user_id'], $request->param('opening_id'), $request->param('bank_account'), $request->param('bank_address'), $request->param('bank_name'), $request->param('bank_default'));

                AdminLog::addLog('添加会员银行卡信息', $request->param());

                return json()->data(['code' => 1, 'msg' => '添加成功']);
            } catch (\Exception $e) {
                Log::write('添加会员银行卡失败: ' . $e->getMessage(), 'error');

                return json()->data(['code' => -1, 'msg' => '添加失败']);
            }
        } else {
            $assignData = [
                'bankNames' => Bank::getCarryBank()
            ];

            return view('user/user_bank/add_user_bank_info', $assignData);
        }
    }

    /**
     * 编辑会员银行卡
     * @param Request $request
     * @param UserBankModel $userBankModel
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function editUserBank(Request $request, UserBankModel $userBankModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $userBankModel->getInfoById($id);
        if (empty($info)) {
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info->opening_id = $request->param('opening_id', '', 'intval');
                $info->bank_name = $request->param('bank_name', '', 'trim');
                $info->bank_account = $request->param('bank_account', '', 'trim');
                $info->bank_address = $request->param('bank_address', '', 'trim');
                $info->bank_default = $request->param('bank_default', '', 'intval');
                $info->save();

                AdminLog::addLog('修改会员银行卡信息', $request->param());

                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                Log::write('修改会员银行卡失败: ' . $e->getMessage(), 'error');

                return json()->data(['code' => -1, 'msg' => '修改失败']);
            }
        } else {
            $assignData = [
                'bankNames' => Bank::getCarryBank(),
                'info' => $info
            ];

            return view('user/user_bank/edit_user_bank_info', $assignData);
        }
    }

    /**
     * 删除会员银行卡
     * @param Request $request
     * @param UserBankModel $userBankModel
     * @return \think\Response|\think\response\Json
     */
    public function delUserBank(Request $request, UserBankModel $userBankModel)
    {
        if ($request->isPost()) {
            $id = $request->param('id', '', 'trim');
            if (empty($id)) {
                return json()->data(['code' => -1, 'msg' => '请选择要删除的数据']);
            }

            try {
                $userBankList = $userBankModel->whereIn('id', $id)->select()->toArray();
                $userBankModel->whereIn('id', $id)->delete();

                AdminLog::addLog('删除会员银行卡', $userBankList);

                return json()->data(['code' => 1, 'msg' => '删除成功']);
            } catch (\Exception $e) {
                return json()->data(['code' => -1, 'msg' => '删除失败']);
            }
        }
    }

    /**
     * 会员支付宝与微信收款方式列表
     * @param Request $request
     * @param Where $where
     * @param UserBankModel $userBankModel
     * @param Users $userModel
     * @return \think\response\Json|\think\response\View
     */
    public function userCodeList(Request $request, Users $userModel)
    {
        if ($request->isAjax()) {
            try {
                $where = new Where;
                if ($account = $request->param('account', '', 'trim')) {
                    $where['account'] = $account;
                }
                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');
                $list = $userModel->where($where)->order('user_id', 'desc')->limit($page * $pageSize, $pageSize)->select();
                $userList = [];
                foreach ($list as $v) {
                    $arr = [
                        'user_id' => $v['user_id'],
                        'account' => $v['account'],
                        'wx_code' => get_img_domain() . $v['wx_code'],
                        'zfb_code' => get_img_domain() . $v['zfb_code'],
                        'wx_name' => $v['wx_name'],
                        'zfb_name' => $v['zfb_name'],
                    ];
                    $userList[] = $arr;
                }
                if (count($userList) == 0) {
                    $data = [
                        'code' => -1,
                        'msg' => '暂无信息'
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $userList,
                        'count' => $userModel->where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询信息失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取到收款码信息，请联系管理员',
                ];
                return json()->data($data);
            }
        } else {
            return view('user/user_bank/user_code_list');
        }
    }

    /**
     * 修改收款码
     * @param Request $request
     * @param Users $userModel
     * @param UserLogic $userLogic
     * @return \think\response\Json|\think\response\View
     */
    public function editUserCode(Request $request, Users $userModel, UserLogic $userLogic)
    {
        $id = $request->param('id', '', 'intval');
        $info = $userModel->where('user_id', $id)->field('wx_code,zfb_code,wx_name,zfb_name')->find();
        if (empty($info)) {
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info->wx_code = $request->param('wx_code', '', 'trim');
                $info->zfb_code = $request->param('zfb_code', '', 'trim');
                $info->wx_name = $request->param('wx_name', '', 'trim');
                $info->zfb_name = $request->param('zfb_name', '', 'trim');
                $info->save();
                AdminLog::addLog('修改收款码方式', $request->param());
                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            $assignData = [
                'info' => $info
            ];
            return view('user/user_bank/edit_user_code', $assignData);
        }
    }

    /**
     * 留言记录
     * @param Request $request
     * @param Where $where
     * @param MessageModel $messageModel
     * @param Users $userModel
     * @return \think\response\Json|\think\response\View
     */
    public function messageLogList(Request $request, Where $where, MessageModel $messageModel, Users $userModel)
    {
        $messageCate = get_message_type();
        if ($request->isAjax()) {
            try {
                if ($account = $request->param('account', '', 'trim')) {
                    $userId = (int) $userModel->where('account', $account)->value('user_id');
                    $where['uid'] = $userId;
                }
                if ($status = $request->param('status', '', 'intval')) {
                    $where['status'] = $status;
                }
                if ($messageType = $request->param('message_type', '', 'intval')) {
                    $where['type'] = $messageType;
                }
                if ($time = $request->param('time', '', 'trim')) {
                    $times = explode(' - ', $time);
                    $startTime = strtotime($times[0]);
                    $endTime = strtotime($times[1]);
                    $where['add_time'] = ['between', [$startTime, $endTime]];
                }
                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');

                $list = $messageModel->where($where)->order('id', 'desc')->limit($page * $pageSize, $pageSize)->select();

                $userIds = get_arr_column($list, 'uid');

                $users = $userModel->whereIn('user_id', $userIds)->column('account', 'user_id');
                $logList = [];
                foreach ($list as $v) {
                    $arr = [
                    'id' => $v['id'],
                    'account' => $users[$v['uid']] ?? '',
                    'title' => $v['title'],
                    'type_msg' => $messageCate[$v['type']] ?? '',
                    'content' => $v['content'],
                    'add_time' => date('Y-m-d H:i:s', $v['add_time']),
                    'status' => $v['status']
                    ];
                    $statusMsg = '';
                    switch ($v['status']) {
                        case 1:
                            $statusMsg = '待回复';
                            break;
                        case 9:
                            $statusMsg = '已回复';
                            break;
                    }
                    $arr['status_msg'] = $statusMsg;

                    $logList[] = $arr;
                }

                if (count($logList) == 0) {
                    $data = [
                        'code' => -1,
                        'msg' => '没有留言记录'
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $logList,
                        'count' => $messageModel->where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询留言记录失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取到留言记录，请联系管理员',
                ];
                return json()->data($data);
            }
        } else {
            $assignInfo = [
                'messageCate' => $messageCate
            ];

            return view('user/message/log_list', $assignInfo);
        }
    }

    /**
     * 回复会员留言
     * @param Request $request
     * @param MessageModel $messageModel
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function replyMessage(Request $request, MessageModel $messageModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $messageModel->getMessageInfoById($id);
        if (empty($info)) {
            $this->error('未获取到留言信息');
        }
        if ($request->isPost()) {
            $replyContent = $request->param('reply_content', '', 'trim');
            if ($replyContent == '') {
                return json()->data(['code' => -1, 'msg' => '请输入回复内容']);
            }
            Db::startTrans();
            try {
                $info->status = 9;
                $info->reply = $replyContent;
                $info->reply_time = time();
                $info->save();

                AdminLog::addLog('回复会员留言', ['info' => $info->toArray(), 'reply_info' => $request->param()]);

                Db::commit();

                return json()->data(['code' => 1, 'msg' => '回复成功']);
            } catch (\Exception $e) {
                Db::rollback();
                Log::write('回复会员留言失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '回复失败']);
            }
        } else {
            $info['img'] = explode(',', $info['thumb']);
            $assignInfo = [
                'info' => $info,
                'messageCate' => get_message_type()
            ];

            return view('user/message/reply_message_info', $assignInfo);
        }
    }

    /**
     * 删除会员留言
     * @param Request $request
     * @param MessageModel $messageModel
     * @return \think\response\Json
     */
    public function delMessageLog(Request $request, messageModel $messageModel)
    {
        if ($request->isPost()) {
            $id = $request->param('id', '', 'trim');

            Db::startTrans();
            try {
                $messageList = $messageModel->whereIn('id', $id)->select();
                if (empty($messageList)) {
                    return json()->data(['code' => -1, 'msg' => '没有数据']);
                }
                $messageModel->whereIn('id', $id)->delete();

                AdminLog::addLog('删除会员留言', $messageList->toArray());

                Db::commit();
                return json()->data(['code' => 1, 'msg' => '删除成功']);
            } catch (\Exception $e) {
                Log::write('删除会员留言失败: ' . $e->getMessage(), 'error');
                Db::rollback();
                return json()->data(['code' => -1, 'msg' => '删除失败']);
            }
        }
    }

    /**
     * @param Request $request
     * @param Where $where
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function userAuthNameList(Request $request, Where $where)
    {
        if ($request->isAjax()) {
            try {
                if ($account = $request->param('account', '', 'trim')) {
                    $userId = (int) Users::where('account', $account)->value('user_id');
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
                $list = UserAuthName::where($where)
                                ->order('id', 'desc')
                                ->limit($page * $pageSize, $pageSize)->select();

                $logList = [];
                $userIds = array_unique(get_arr_column($list, 'uid'));

                $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');

                $getImgDomain = get_img_domain();
                $statusData = [1 => '待审核', 2 => '已拒绝', 9 => '己审核'];
                foreach ($list as $v) {
                    $arr[] = $v;
                    $arr['id'] = $v['id'];
                    $arr['uid'] = $users[$v['uid']] ?? '-';
                    $arr['username'] = $v['username'];
                    $arr['card_number'] = $v['card_number'];
                    $arr['card_just'] = $getImgDomain . $v['card_just'];
                    $arr['card_back'] = $getImgDomain . $v['card_back'];
                    $arr['hold_card'] = $getImgDomain . $v['hold_card'];
                    $arr['add_time'] = $v['add_time'] > 0 ? date('Y-m-d H:i:s', $v['add_time']) : '-';
                    $arr['status_msg'] = $statusData[$v['status']] ?? '-';
                    $arr['status'] = $v['status'];
                    $arr['refuse_time'] = $v['refuse_time'] > 0 ? date('Y-m-d H:i:s', $v['refuse_time']) : '-';
                    $arr['refuse_note'] = $v['refuse_note'] ?? '';
                    $arr['confirm_time'] = $v['confirm_time'] > 0 ? date('Y-m-d H:i:s', $v['confirm_time']) : '-';
                    $logList[] = $arr;
                }
                if (count($logList) == 0) {
                    $data = [
                        'code' => -1,
                        'msg' => '暂无记录'
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $logList,
                        'count' => UserAuthName::where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询实名记录失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取到信息，请联系管理员',
                ];
                return json()->data($data);
            }
        } else {
            return view('user/user_auth_name_list');
        }
    }

    /**
     * 确认操作
     * @param Request $request
     * @return \think\Response|Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function authNameConfirm(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param('id', 0, 'intval');

            $info = UserAuthName::where('id', $id)->find();
            if (empty($info) || $info['status'] != 1) {
                return json(['code' => -1, 'msg' => '此信息不支持操作，请刷新后重试']);
            }
            try {
                $info->status = 9;
                $info->confirm_time = time();
                $info->save();

                $authGiveMoney = zf_cache('security_info.auth_give_mid_num');

                if ($authGiveMoney > 0 && intval(zf_cache('security_info.auth_username_type')) == 1) {
                    $userAuthGiveNum = MoneyLog::where('uid', $userInfo['user_id'])->where('is_type', 150)->count();
                    if ($userAuthGiveNum <= 0) {
                        $authGiveMoney = zf_cache('security_info.auth_give_mid_num');
                        (new UsersMoney())->amountChange($userInfo['user_id'], 1, $authGiveMoney, 150, '实名认证', [
                            'come_uid' => $info['uid']
                        ]);
                    }

                    $tjrTotalConfigMoney = zf_cache('security_info.code_day_give_mid_num');
                    if ($tjrTotalConfigMoney > 0) {
                        $tjrTotalMoney = MoneyLog::where('uid', $userInfo['tjr_id'])->where('is_type', 151)->whereBetween('edit_time', Time::today())->sum('money');
                        if ($tjrTotalMoney < $tjrTotalConfigMoney) {
                            $authTjrGiveMoney = zf_cache('security_info.auth_give_mid_num');
                            (new UsersMoney())->amountChange($userInfo['tjr_id'], 1, $authTjrGiveMoney, 151, $userInfo['account'] . '实名认证', [
                                'come_uid' => $userInfo['user_id']
                            ]);
                        }
                    }
                }
                AdminLog::addLog('确认实名审核', $info->toArray());
                return json(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                Log::write('确认实名审核失败: ' . $e->getMessage(), 'error');
                return json(['code' => -1, 'msg' => '操作失败']);
            }
        }
    }

    /**
     * 批量审核
     * @param Request $request
     * @return \think\Response|\think\response\Json
     */
    public function authNameConfirmBatch(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param('id');
            if (!$id) {
                return json(['code' => -1, 'msg' => '请选择']);
            }
            Db::startTrans();
            try {
                $arrId = explode(',', $id);
                $carryList = UserAuthName::whereIn('id', $arrId)->where('status', 1)->select();
                if (count($carryList) <= 0) {
                    return json()->data(['code' => -1, 'msg' => '此信息不支持该操作']);
                }
                $data = $carryList->toArray();
                $num = 0;
                foreach ($carryList as $v) {
                    $v->confirm_time = time();
                    $v->status = 9;
                    $v->save();
                    $num++;
                }
                Db::commit();
                AdminLog::addLog('确认实名认证', $data);
                if ($num > 0) {
                    return json()->data(['code' => 1, 'msg' => '成功操作' . $num . '条数据']);
                } else {
                    return json()->data(['code' => -1, 'msg' => '没有任何数据发生变化']);
                }
            } catch (\Exception $e) {
                Db::rollback();
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        }
    }

    /**
     * 拒绝操作
     * @param Request $request
     * @return \think\Response|Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function authNameRefuse(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->param('id', 0, 'intval');

            $info = UserAuthName::where('id', $id)->find();
            if (empty($info) || $info['status'] != 1) {
                return json(['code' => -1, 'msg' => '此信息不支持操作，请刷新后重试']);
            }

            $refuseContent = $request->param('refuse_content', '', 'htmlspecialchars');
            if ($refuseContent == '') {
                return json(['code' => -1, 'msg' => '请输入拒绝理由']);
            }

            try {
                $info->status = 2;
                $info->refuse_note = $refuseContent;
                $info->refuse_time = time();
                $info->save();

                AdminLog::addLog('拒绝实名审核', $info->toArray());

                return json(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                Log::write('拒绝实名审核失败: ' . $e->getMessage(), 'error');
                return json(['code' => -1, 'msg' => '操作失败']);
            }
        }
    }

    /**
     * 编辑认证信息
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function editAuthInfo(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = UserAuthName::where('id', $id)->find();
        if (empty($info)) {
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info->username = $request->param('username', '', 'trim');
                $info->card_number = $request->param('card_number', '', 'trim');
                $info->status = $request->param('status', '', 'intval');
                $info->save();
                AdminLog::addLog('修改会员认证信息', $request->param());
                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                Log::write('修改会员认证信息失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            return view('user/auth/edit_info', [
                'info' => $info,
                'statusData' => UserAuthName::$statusData,
            ]);
        }
    }

}
