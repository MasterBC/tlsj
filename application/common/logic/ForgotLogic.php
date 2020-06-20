<?php

namespace app\common\logic;

use app\common\model\Users;
use app\common\model\UsersData;
use think\facade\Request;
use app\facade\Password;

class ForgotLogic
{
    /**
     * 会员找回操作
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doForgot()
    {
        // 接收传值
        $account = Request::param('account');
        $mobile = Request::param('mobile');
        $password = Request::param('password');
        $repassword = Request::param('repassword');
        $mobileCode = Request::param('mobileCode');
        $userModel = new Users();
        $userDataModel = new UsersData();
        $userInfo = $userModel->getUserByAccount($account);
        // 查出该账号是否存在
        if (!$userInfo) {
            exception('会员不存在');
        }

        // 查出用户信息
        $userDataInfo = $userDataModel->getUserDataInfo($userInfo['data_id'], 1);

        if ($mobile != $userDataInfo['mobile']) {
            exception('手机号不正确');
        }

        // 判断两次密码是否一致
        if ($password != $repassword) {
            exception('两次密码不一致');
        }

        // 判断手机验证码
        $validateSmsCode= validate_sms_code_verify($mobile, $mobileCode, sid());

        if ($validateSmsCode['code'] != 1) {
            exception($validateSmsCode['msg']);
        }

        // 验证码邮箱验证码
//        $validateSmsCode = validate_smtp_email_code_verify($mobile, $mobileCode, sid());

//        if ($validateSmsCode['code'] != 1) {
//            exception($validateSmsCode['msg']);
//        }

        // 获取密码加密
        $salt = Password::getPasswordSalt();
        $password = Password::getPassword($repassword, $salt);
        $updateData = [
            'password' => $password,
            'pass_salt' => $salt
        ];

        $updateInfo = $userModel->updateUserInfo($userInfo['user_id'], $updateData);

        if (!$updateInfo) {
            exception('操作失败');
        }

        return true;
    }
}