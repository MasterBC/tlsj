<?php

namespace app\common\logic;

use app\common\model\AdminUser;
use think\facade\Request;
use app\facade\Password;
use think\facade\Session;
use think\facade\Cache;
use app\common\model\AdminLog;
use think\facade\Log;

class AdminUserLogic
{

    /**
     * 管理员登陆
     * @return bool
     * @throws \Exception
     */
    public function doLogin()
    {
        try {
            $adminUser = new AdminUser();
            $username = Request::param('username');
            $password = Request::param('password');
            $adminUser = $adminUser->getUserByUsername($username);
        } catch (\Exception $e) {
            Log::write('登录查询会员失败：' . $e->getMessage(), 'error');
            exception('登陆失败');
        }
        if (!$adminUser) {
            exception('用户名或者密码错误');
        }
        if (!Password::checkPassword($password, $adminUser)) {
            AdminLog::addLog('密码错误', ['password' => $password], $adminUser['admin_id']);
            exception('用户名或者密码错误');
        }
        $adminUser->last_login = time();
        $adminUser->last_ip = Request::ip();
        $adminUser->save();

        $this->setSession($adminUser);

        $this->delCache();
        AdminLog::addLog('登陆成功', [], $adminUser['admin_id']);

        return true;
    }

    /**
     * 设置管理员session
     * @param $info
     */
    public function setSession($info)
    {
        unset($info['password'], $info['pass_salt']);
        Session::set(AdminUser::SESSION_NAME, $info);
        Session::set(AdminUser::SESSION_NAME . '_past_due_time', time());
    }

    /**
     * 添加管理员账号
     * @return mixed
     */
    public function addAdminUser()
    {
        $salt = Password::getPasswordSalt();
        $data = [
            'user_name' => Request::param('username'),
            'add_time' => time(),
            'password' => Password::getPassword(Request::param('password'), $salt),
            'pass_salt' => $salt
        ];
        Request::param('mobile') && $data['mobile'] = Request::param('mobile');
        Request::param('email') && $data['email'] = Request::param('email');

        return AdminUser::insert($data);
    }

    /**
     * 编辑管理员
     * @param $userInfoModel
     * @return mixed
     */
    public function editAdminUser($userInfoModel)
    {
        if ($password = Request::param('password')) {
            $salt = Password::getPasswordSalt();
            $userInfoModel->pass_salt = $salt;
            $userInfoModel->password = Password::getPassword($password, $userInfoModel->pass_salt);
        }
        if ($mobile = Request::param('mobile')) {
            $userInfoModel->mobile = $mobile;
        }
        if ($email = Request::param('email')) {
            $userInfoModel->email = $email;
        }

        $res = $userInfoModel->save();
        $userModel = new AdminUser();
        if ($userInfoModel->getId() == $userModel->getAdminUserId()) {
            $this->setSession($userInfoModel);
        }
        AdminLog::addLog('编辑管理员信息', Request::param());

        return $res;
    }

    /**
     * 获取管理员权限
     * @return mixed
     */
    public function getUserRules()
    {
        $adminUserModel = new AdminUser();
        $cacheKey = 'user:menu:' . $adminUserModel->getAdminUserId();
        $model = new \app\common\model\auth\AuthGroupAccess();
        $menu = Cache::remember($cacheKey, function () use ($adminUserModel, $model) {

            $leftMenus = $model->getUserRules($adminUserModel->getAdminUserId());

            return $leftMenus;
        });
        return $menu;
    }


    /**
     * 清除缓存
     */
    public function delCache()
    {
        $adminUserModel = new AdminUser();
        // 清除菜单目录缓存
        $cacheKey = 'user:menu:' . $adminUserModel->getAdminUserId();
        Cache::rm($cacheKey);
        // 清除权限规则缓存
        $cacheKey = Request::module() . ':user:rules:' . $adminUserModel->getAdminUserId();
        Cache::rm($cacheKey);
    }

}