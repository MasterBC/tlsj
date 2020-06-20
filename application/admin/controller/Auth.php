<?php

namespace app\admin\controller;

use app\common\logic\AdminUserLogic;
use app\common\model\AdminUser;
use app\common\model\auth\AuthGroup;
use app\common\model\auth\AuthGroupAccess;
use app\common\model\auth\AuthRule;
use think\Request;
use think\facade\Log;
use think\db\Where;
use app\common\model\AdminLog;

class Auth extends Base
{
    /**
     * 菜单列表
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function menuList(Request $request)
    {
        if ($request->isAjax()) {
            try {
                // 只显示目录和开放的
                $where = [
                    'is_menu' => 1,
                    'status' => 1
                ];
                $request->param('group_name') && $where['module_name'] = $request->param('group_name');
                $list = AuthRule::where($where)->order('sort', 'desc')->order('id', 'asc')->select();

                $list = get_column($list);

                $menu = [];
                foreach ($list as $v) {
                    switch ($v['module_name']) {
                        default:
                            $groupName = '后台';
                            break;
                        case 'seller':
                            $groupName = '商家后台';
                            break;
                    }
                    $arr = [
                        'id' => $v['id'],
                        'title' => str_repeat("&nbsp;&nbsp;&nbsp;|—", $v["level"]) . $v['title'],
                        'group_name' => $groupName,
                        'sort' => intval($v['sort'])
                    ];
                    $menu[] = $arr;
                }

                $data = [
                    'code' => 1,
                    'data' => $menu
                ];
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询菜单目录失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '没有获取到菜单目录'
                ];
                return json()->data($data);
            }
        } else {
            return view('auth/menu_list');
        }
    }

    /**
     * 修改菜单
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function editMenu(Request $request)
    {
        $id = $request->param('id', '0', 'intval');

        $info = AuthRule::getById($id);
        if (empty($info)) {
            $this->error('未获取到菜单信息');
        }
        if ($request->isAjax() && $request->isPost()) {

            $title = $request->param('title');
            $sort = $request->param('sort', '', 'intval');

            try {
                $info->title = $title;
                $info->sort = $sort;
                $info->save();

                AdminLog::addLog('修改菜单', $request->param());
                return json(['code' => 1, 'msg' => '更新成功']);
            } catch (\Exception $e) {
                Log::write('更新菜单失败: ' . $e->getMessage(), 'error');
                return json(['code' => -1, 'msg' => '更新失败']);
            }
        } else {
            $this->assign('info', $info);

            return view('auth/edit_menu');
        }
    }

    /**
     * 系统角色管理
     * @param Request $request
     * @param AuthGroup $authGroupModel
     * @return \think\response\Json|\think\response\View
     */
    public function userGroupList(Request $request, AuthGroup $authGroupModel)
    {
        if ($request->isAjax()) {
            $where = new Where;
            $where['status'] = 1;
            $request->param('group_name') && $where['module_name'] = ['like', '%' . $request->param('group_name') . '%'];
            $p = $request->get('p', '1', 'intval') - 1;
            $pNum = $request->get('p_num', '10', 'intval');

            $where['title'] = ['not in', $authGroupModel::$superAdminNames];
            $list = $authGroupModel->getGroupList($where, $p, $pNum);

            $groupList = [];
            foreach ($list as $v) {
                $groupName = '后台';
                if (strpos($v['module_name'], 'seller')) {
                    $groupName = '商家后台';
                }
                $arr = [
                    'id' => $v['id'],
                    'title' => $v['title'],
                    'description' => $v['description'],
                    'group_name' => $groupName
                ];
                $groupList[] = $arr;
            }

            $count = $authGroupModel->where($where)->count();
            if ($count == 0) {
                $data = [
                    'code' => -1,
                    'msg' => '没有查询到角色'
                ];
            } else {
                $data = [
                    'code' => 1,
                    'data' => $groupList,
                    'count' => $count
                ];
            }

            return json()->data($data);
        } else {
            return view('auth/user_group_list');
        }
    }

    /**
     * 添加角色
     * @param Request $request
     * @param AuthGroup $authGroupModel
     * @param AuthRule $authRuleModel
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addUserGroup(Request $request, AuthGroup $authGroupModel, AuthRule $authRuleModel)
    {
        if ($request->isPost()) {
            try {
                $authGroupModel->addGroup();
                AdminLog::addLog('添加角色', $request->param());

                return json(['code' => 1, 'msg' => '添加成功']);
            } catch (\Exception $e) {
                Log::write('添加角色失败: ' . $e->getMessage(), 'error');
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            $menu = $authRuleModel->selectAllMenu();
            $menu = get_column($menu, 2);

            $this->assign('menu', $menu);

            return view('auth/add_user_group');
        }
    }


    /**
     * 编辑角色
     * @param Request $request
     * @param AuthGroup $authGroupModel
     * @param AuthRule $authRuleModel
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editUserGroup(Request $request, AuthGroup $authGroupModel, AuthRule $authRuleModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $authGroupModel->getById($id);
        if (empty($info)) {
            $this->error('未获取到角色信息');
        }
        if ($request->isPost()) {
            try {
                $info->editGroup();

                AdminLog::addLog('编辑角色', $request->param());

                return json(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                Log::write('修改角色失败: ' . $e->getMessage(), 'error');
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            $menu = $authRuleModel->selectAllMenu();
            $menu = get_column($menu, 2);
            $rulesArr = explode(',', $info['rules']);
            $this->assign('rulesArr', $rulesArr);

            $this->assign('menu', $menu);
            $this->assign('info', $info);

            return view('auth/edit_user_group');
        }
    }

    /**
     * 删除角色
     */
    public function delUserGroup(Request $request)
    {
        $id = $request->param('id', '', 'intval');

        $info = AuthGroup::where('id', $id)->find()->toArray();

        AuthGroup::destroy($id);
        AdminLog::addLog('删除角色', $info);

        return json(['code' => 1, 'msg' => '删除成功']);
    }

    /**
     * 管理员列表
     * @param Request $request
     * @param Where $where
     * @param AdminUser $adminUserModel
     * @return \think\response\Json|\think\response\View
     */
    public function adminUserList(Request $request, Where $where, AdminUser $adminUserModel)
    {
        if ($request->isAjax()) {
            try {
                // 不显示当前登陆的管理员
                $where['admin_id'] = ['neq', $adminUserModel->getAdminUserId()];
                $request->param('id') && $where['admin_id'] = $request->param('id', '', 'intval');
                $request->param('username') && $where['user_name'] = ['like', '%' . $request->param('username') . '%'];
                $request->param('email') && $where['email'] = ['like', '%' . $request->param('email') . '%'];

                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');

                $list = $adminUserModel->where($where)->limit($page * $pageSize, $pageSize)->select();
                $userList = [];
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['admin_id'],
                        'username' => $v['user_name'],
                        'email' => $v['email'],
                        'mobile' => $v['mobile'],
                        'last_time' => ($v['last_login'] ? date('Y-m-d H:i:s', $v['last_login']) : '暂未登陆')
                    ];
                    $userList[] = $arr;
                }

                $count = $adminUserModel->where($where)->count();
                if ($count == 0) {
                    $data = [
                        'code' => -1,
                        'msg' => '没有查询到管理员'
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $userList,
                        'count' => $count
                    ];
                }


                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询管理员列表失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '没有查询到管理员'
                ];
                return json()->data($data);
            }
        } else {
            return view('auth/admin_user_list');
        }
    }

    /**
     * 添加管理员
     * @param Request $request
     * @param AdminUserLogic $adminLogic
     * @return \think\response\Json|\think\response\View
     */
    public function addAdminUser(Request $request, AdminUserLogic $adminLogic)
    {
        if ($request->isPost()) {
            $result = $this->validate($request->post(), 'app\admin\validate\AdminUserForm');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }

            try {

                $adminLogic->addAdminUser();
                AdminLog::addLog('添加管理员', $request->param());

                return json()->data(['code' => 1, 'msg' => '添加成功']);
            } catch (\Exception $e) {
                Log::write('添加管理员失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }

        } else {
            return view('auth/add_admin_user');
        }
    }

    /**
     * 编辑管理员
     * @param Request $request
     * @param AdminUser $adminUserModel
     * @param AdminUserLogic $adminLogic
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editAdminUser(Request $request, AdminUser $adminUserModel, AdminUserLogic $adminLogic)
    {
        $id = $request->param('id', '', 'intval');
        $info = $adminUserModel->getUserById($id);
        if (empty($info)) {
            $this->error('未获取到管理员信息');
        }
        if ($request->isPost()) {
            $result = $this->validate($request->post(), 'app\admin\validate\AdminUserForm.edit');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }
            try {
                $adminLogic->editAdminUser($info);

                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                Log::write('修改管理员失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            $this->assign('info', $info);
            return view('auth/edit_admin_user');
        }
    }

    /**
     * 删除管理员
     * @param Request $request
     * @return \think\response\Json
     */
    public function delAdminUser(Request $request)
    {
        $id = $request->param('id', '', 'intval');

        try {
            $data = AdminUser::where(['admin_id' => $id])->find()->toArray();
            AdminUser::where(['admin_id' => $id])->delete();

            AdminLog::addLog('删除管理员', $data);

            return json(['code' => 1, 'msg' => '删除成功']);
        } catch (\Exception $e) {
            Log::write('删除管理员失败: ' . $e->getMessage(), 'error');
            return json(['code' => -1, 'msg' => '删除失败']);
        }
    }

    /**
     * 分配角色
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function giveRole(Request $request)
    {
        $adminId = $request->param('id', '0', 'intval');

        if ($request->isPost()) {
            try {
                if ($groupIds = $request->param('group_id')) {
                    AuthGroupAccess::where('uid', $adminId)->delete();
                    $data = [];
                    foreach ($groupIds as $k => $v) {
                        $arr = [
                            'uid' => $adminId,
                            'group_id' => $v,
                            'module_name' => $request->module()
                        ];
                        $data[] = $arr;
                    }
                    AuthGroupAccess::insertAll($data);
                    AdminLog::addLog('分配角色', $request->param());
                }
                return json(['code' => 1, 'msg' => '分配成功']);
            } catch (\Exception $e) {
                Log::write('分配角色失败: ' . $e->getMessage(), 'error');
                return json(['code' => -1, 'msg' => '分配失败']);
            }
        } else {

            $list = AuthGroup::with(['profile' => function ($query) {
                $request = new Request();
                $adminId = $request->param('id', '0', 'intval');
                $query->where(['uid' => $adminId]);
            }])->where(['status' => 1])->select();

            $this->assign('list', $list);
            $this->assign('adminId', $adminId);

            return view('auth/give_role_info');
        }

    }


    /**
     * 管理员操作日志
     * @param Request $request
     * @param Where $where
     * @param AdminLog $adminLogModel
     * @param AdminUser $adminUserModel
     * @return \think\response\Json|\think\response\View
     */
    public function adminLogList(Request $request, Where $where, AdminLog $adminLogModel, AdminUser $adminUserModel)
    {
        if ($request->isAjax()) {
            try {
                if ($username = $request->param('username', '', 'trim')) {
                    $adminId = (int)$adminUserModel->where('user_name', $username)->value('admin_id');
                    $where['admin_id'] = $adminId;
                }
                if ($time = $request->param('time', '', 'trim')) {
                    $times = explode(' - ', $time);
                    $startTime = strtotime($times[0]);
                    $endTime = strtotime($times[1]);
                    $where['add_time'] = ['between', [$startTime, $endTime]];
                }
                if ($type = $request->param('type', '', 'trim')) {
                    if($type == 'console') {
                        $where['admin_id'] = $adminUserModel->getAdminUserId();
                    }
                }

                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');
                $list = $adminLogModel->where($where)->limit($page * $pageSize, $pageSize)->order('id', 'desc')->select();

                $adminIds = get_arr_column($list, 'admin_id');
                $adminUsers = $adminUserModel->whereIn('admin_id', $adminIds)->column('user_name', 'admin_id');

                $logList = [];
                foreach ($list as $k => $v) {
                    $arr = [
                        'id' => $v['id'],
                        'add_time' => date('Y-m-d H:i:s', $v['add_time']),
                        'username' => isset($adminUsers[$v['admin_id']]) ? $adminUsers[$v['admin_id']] : '',
                        'ip' => $v['log_ip'],
                        'equipment' => $v['equipment'],
                        'note' => $v['note']
                    ];

                    $logList[] = $arr;
                }
                if (count($logList) == 0) {
                    $data = [
                        'code' => -1,
                        'msg' => '没有操作记录'
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $logList,
                        'count' => $adminLogModel->where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询管理员操作日志失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取到操作记录，请联系管理员',
                ];
                return json()->data($data);
            }

        } else {
            return view('auth/admin_log_list');
        }

    }
}
