<?php

namespace app\wap\validate;

use think\Validate;

class Login extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'username' => 'require',
        'password' => 'require',
        'mobile' => 'require|checkMobile:thinkphp',
        'mobile_code' => 'require',
        'email' => 'require|checkMail:thinkphp',
        'email_code' => 'require',
        'verify_code' => 'require|checkVercode:thinkphp'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'username.require' => '请输入账号',
        'password.require' => '请输入密码',
        'mobile.require' => '请输入手机账号',
        'mobile_code.require' => '请输入手机验证码',
        'email.require' => '请输入邮箱账号',
        'email_code.require' => '请输入邮箱验证码',
        'verify_code.require' => '请输入验证码'
    ];

    /**
     * 会员账号登录验证规则
     * @return Login
     */
    public function sceneAccount()
    {
        return $this->only(['username', 'password'])
            ->remove('mobile|mobile_code|email|email_code');
    }

    /**
     * 手机号登录验证规则
     * @return Login
     */
    public function sceneMobile()
    {
        return $this->only(['mobile', 'mobile_code'])
            ->remove('username|password|verify_code|email|email_code', 'require');
    }

    /**
     * 验证手机号是否正确
     * @param $value
     * @return bool|string
     */
    public function checkMobile($value)
    {
        return check_mobile($value) ? true : '手机号不正确';
    }

    /**
     * 验证邮箱号是否正确
     * @param $value
     * @return bool|string
     */
    public function checkMail($value)
    {
        return check_mail($value) ? true : '邮箱号不正确';
    }

    /**
     * 验证验证码
     * @param string $value 验证码
     * @return bool|string
     */
    public function checkVercode($value)
    {
        return captcha_check($value, 'home_login') ? true : '验证码错误';
    }
}
