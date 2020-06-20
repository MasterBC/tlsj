<?php

namespace app\common\logic;

use app\common\model\Users;
use think\facade\Request;
use app\facade\Password;
use app\common\model\UsersLog;

class LevelLogic
{

    /**
     * 会员账号密码登录操作
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function dolevelUserAdd()
    {
        // 获取参数
        $username = Request::param('username', '', 'trim');
        $password = Request::param('password', '', 'trim');
        $verifyCode = Request::param('verify_code');

        // 查出该账号是否存在
        $userModel = new Users();
        $userInfo = $userModel->getUserByAccount($username, 1);

        // 判断会员是否存在
        if (!$userInfo) {
            exception('会员账号不存在');
        }
        // 判断会员是否已被冻结
        if ($userInfo['frozen'] != 1) {
            exception('账号已被冻结!');
        }
        if (!captcha_check($verifyCode, 'home_login')) {
            exception('验证码错误');
        }
        $userLog = new UsersLog();
        // 判断登录密码是否正确
        if (!Password::checkPassword($password, $userInfo)) {
            $userLog->addLog($userInfo['user_id'], 1, '密码错误 密码:' . $password);
            exception('用户名或者密码错误');
        } else {
            $userLog->addLog($userInfo['user_id'], 1, '登陆成功');
            $userInfo->setSession();
        }

        return true;
    }

}
