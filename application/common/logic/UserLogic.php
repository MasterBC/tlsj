<?php

namespace app\common\logic;

use app\common\model\Users;
use think\facade\Validate;
use app\common\model\money\UsersMoney;
use think\facade\Request;
use app\facade\Password;
use app\common\model\UsersLog;
use app\common\model\AdminLog;

class UserLogic
{

    /**
     * 会员修改登录密码
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doSaveLoginPassword()
    {
        // 接收传值
        $userId = Request::param('user_id');
        $oldpassword = Request::param('oldpassword');
        $password = Request::param('password');
        $repassword = Request::param('repassword');

        // 判断原密码是否正确
        $userModel = new Users();
        // 获取密码盐
        $userInfo = $userModel->getUserByUserId($userId, ['password', 'pass_salt']);
        $passwordEncryp = Password::getPassword($oldpassword, $userInfo['pass_salt']);
        if ($passwordEncryp != $userInfo['password']) {
            exception('原密码验证失败');
        }

        // 判断两次密码是否一致
        if ($password != $repassword) {
            exception('两次密码不一致');
        }

        // 加密密码
        $confirmPassword = Password::getPassword($repassword, $userInfo['pass_salt']);
        $updateWhere = [
            'password' => $confirmPassword,
        ];

        $update = $userModel->updateUserInfo($userId, $updateWhere);

        if (!$update) {
            exception('操作失败');
        }

        return true;
    }

    /**
     * 找回密码
     * @return bool
     * @throws \Exception
     */
    public function ForLoginPassword($userInfo)
    {
        // 接收传值
        $userId = Request::param('user_id');
        $password = Request::param('password');
        $repassword = Request::param('repassword');
        $mobileCode = Request::param('mobileCode');

        // 判断两次密码是否一致
        if ($password != $repassword) {
            exception('两次密码不一致');
        }

        // 判断手机验证码
        $validateSmsCode = validate_sms_code_verify($userInfo['mobile'], $mobileCode, sid());

        if ($validateSmsCode['code'] != 1) {
            exception($validateSmsCode['msg']);
        }

        // 设置密码操作
        $UsersModel = new Users();
        // 获取密码盐
        $salt = Password::getPasswordSalt();
        $confirmPassword = Password::getPassword($repassword, $salt);
        $updateWhere = [
            'password' => $confirmPassword,
            'pass_salt' => $salt
        ];

        $update = $UsersModel->updateUserInfo($userInfo['user_id'], $updateWhere);

        if (!$update) {
            exception('操作失败');
        }

        return true;
    }

    /**
     * 修改二级密码
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doSaveSecpwd($userId)
    {
        // 接收传值
        $oldSecpwd = Request::param('oldsecpwd');
        $secpwd = Request::param('secpwd');
        $resecpwd = Request::param('resecpwd');

        $UsersModel = new Users();
        // 判断原密码是否正确 获取密码盐
        $userInfo = $UsersModel->getUserByUserId($userId, ['secpwd', 'pass_salt']);

        // 判断是否是第一次设置二级密码
        if ($userInfo['secpwd'] != '') {
            $secpwdEncryp = Password::getPassword($oldSecpwd, $userInfo['pass_salt']);
            if ($secpwdEncryp != $userInfo['secpwd']) {
                exception('原二级密码验证失败');
            }
        }

        if ($secpwd != $resecpwd) {
            exception('两次密码不一致');
        }

        // 获取密码加密
        $secpwd = Password::getPassword($secpwd, $userInfo['pass_salt']);

        if ($userInfo['secpwd'] == $secpwd) {
            exception('原密码和修改密码不能一样');
        }

        $updateWhere = [
            'secpwd' => $secpwd,
        ];

        $update = $UsersModel->updateUserInfo($userId, $updateWhere);

        if (!$update) {
            exception('操作失败');
        }

        return true;
    }

    /**
     * 找回二级密码
     * @param $userId 用户的id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doForSecpwd($userId, $userInfo)
    {
        // 接收传值
        $secpwd = Request::param('secpwd');
        $resecpwd = Request::param('resecpwd');
        $mobileCode = Request::param('mobileCode');

        if ($secpwd != $resecpwd) {
            exception('两次密码不一致');
        }

        // 判断手机验证码
        $validateSmsCode = validate_sms_code_verify($userInfo['mobile'], $mobileCode, sid());
        if ($validateSmsCode['code'] != 1) {
            exception($validateSmsCode['msg']);
        }

        $UsersModel = new Users();
        // 判断原密码是否正确 获取密码盐
        // 获取密码加密
        $secpwd = Password::getPassword($secpwd, $userInfo['pass_salt']);

        $updateWhere = [
            'secpwd' => $secpwd,
        ];

        $update = $UsersModel->updateUserInfo($userId, $updateWhere);

        if (!$update) {
            exception('操作失败');
        }

        return true;
    }

    /**
     * 冻结会员
     * @param $userId
     * @param string $note
     * @throws \Exception
     */
    public function lockUser($userId, $note = '')
    {
        $userModel = new Users();

        $userInfo = $userModel->field('user_id,frozen,account')->getByUserId($userId);

        if (empty($userInfo)) {
            exception('未获取到会员信息');
        }
        if ($userInfo['frozen'] != 1) {
            exception('请勿重复提交');
        }

        $userInfo->frozen = 2;
        $userInfo->lock_time = time();
        $userInfo->save();
        AdminLog::addLog('冻结会员' . $userInfo['account'], $userInfo->toArray());

        $userLogModel = new UsersLog();
        $userLogModel->addLog($userId, 2, $note);
    }

    /**
     * 解除会员冻结
     * @param $userId
     * @param string $note
     * @throws \Exception
     */
    public function unLockUser($userId, $note = '解除冻结')
    {
        $userModel = new Users();

        $userInfo = $userModel->field('account,user_id,frozen')->getByUserId($userId);

        if (empty($userInfo)) {
            exception('未获取到会员信息');
        }
        if ($userInfo['frozen'] == 1) {
            exception('请勿重复提交');
        }

        $userInfo->frozen = 1;
        $userInfo->unlock_time = time();
        $userInfo->save();
        AdminLog::addLog('解除冻结会员' . $userInfo['account'], $userInfo->toArray());

        $userLogModel = new UsersLog();
        $userLogModel->addLog($userId, 3, $note);
    }

    public function adminEditUserData($userInfo)
    {
        $nickname = Request::param('nickname', '', 'trim');
        $mobile = Request::param('mobile', '', 'trim');
        $is_hold_dividend_product = Request::param('is_hold_dividend_product', '2', 'intval');
        $is_zilvu = Request::param('is_zilvu', '', 'intval');
        $is_niuqi = Request::param('is_niuqi', '', 'intval');
        $email = Request::param('email', '', 'trim');
        $password = Request::param('password');
        $secpwd = Request::param('secpwd');
        if ($nickname) {
            $userInfo->nickname = $nickname;
        }
        if ($email) {
            $userInfo->email = $email;
        }
        if ($mobile) {
            $userInfo->mobile = $mobile;
        }
        if ($is_hold_dividend_product) {
            $userInfo->is_hold_dividend_product = $is_hold_dividend_product;
        }
        if ($is_zilvu) {
            $userInfo->is_zilvu = $is_zilvu;
        }
        if ($is_niuqi) {
            $userInfo->is_niuqi = $is_niuqi;
        }
        if ($password) {
            $password = Password::getPassword($password, $userInfo->getPasswordSalt());
            $userInfo->password = $password;
        }
        if ($secpwd) {
            $secpwd = Password::getPassword($secpwd, $userInfo->getPasswordSalt());
            $userInfo->secpwd = $secpwd;
        }

        $userInfo->save();
    }

    /**
     * 绑定微信
     * @param $userDataInfo
     * @throws \Exception
     */
    public function bindWechat($userInfo)
    {
        $validate = Validate::make([
                    'wx_name' => 'require',
                    'wx_code' => 'require'
                        ], [
                    'wx_name.require' => '请输入微信账号',
                    'wx_code.require' => '请上传微信收款码'
        ]);
        if (!$validate->check(Request::param())) {
            exception($validate->getError());
        }
        $userInfo->wx_name = Request::param('wx_name', '', 'strip_tags');
        $userInfo->wx_code = Request::param('wx_code', '', 'strip_tags');
        $userInfo->save();
    }

    /**
     * 绑定支付宝
     * @param $userInfo
     * @throws \Exception
     */
    public function bindAlipay($userInfo)
    {
        $validate = Validate::make([
                    'zfb_name' => 'require',
                    'zfb_code' => 'require'
                        ], [
                    'zfb_name.require' => '请输入支付宝账号',
                    'zfb_code.require' => '请上传支付宝收款码'
        ]);
        if (!$validate->check(Request::param())) {
            exception($validate->getError());
        }
        $userInfo->zfb_name = Request::param('zfb_name', '', 'strip_tags');
        $userInfo->zfb_code = Request::param('zfb_code', '', 'strip_tags');
        $userInfo->save();
    }

}
