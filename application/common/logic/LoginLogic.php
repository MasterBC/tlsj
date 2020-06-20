<?php

namespace app\common\logic;

use app\common\model\Users;
use app\common\model\UsersData;
use think\facade\Request;
use app\facade\Password;
use think\facade\Session;
use app\common\model\UsersLog;

class LoginLogic
{

    /**
     * 会员账号密码登录操作
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doLoginAccount()
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

    /**
     * 会员手机号登录操作
     * @return bool
     * @throws \Exception
     */
    public function doLoginMobile()
    {
        // 获取参数
        $username = Request::param('mobile');
        $mobileCode = Request::param('mobile_code');

        if (!check_mobile($username)) {
            exception('请输入正确的手机号账号');
        }

        $userDataInfo = UsersData::where('mobile', $username)->field('id')->find();

        if (!$userDataInfo) {
            exception('手机号不存在');
        }

        $userInfo = Users::where('data_id', $userDataInfo['id'])->find();

        // 判断会员是否存在
        if (!$userInfo) {
            exception('网络错误，请刷新后重试');
        }

        // 判断会员是否已被冻结
        if ($userInfo['frozen'] != 1) {
            exception('手机号已被冻结!');
        }

        // 验证短信验证码
        $validateSmsCode = validate_sms_code_verify($username, $mobileCode, sid());

        $userLog = new UsersLog();
        if ($validateSmsCode['code'] != 1) {
            $userLog->addLog($userInfo['user_id'] . '手机登录 手机号' . $username, 1, '验证码错误 验证码:' . $mobileCode . '提示错误：' . $validateSmsCode['msg']);
            exception($validateSmsCode['msg']);
        } else {
            $userLog->addLog($userInfo['user_id'] . '手机登录 手机号' . $username, 1, '登陆成功');
            $userInfo->setSession();
        }

        return true;
    }

}
