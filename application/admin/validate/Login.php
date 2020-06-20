<?php

namespace app\admin\validate;

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
        'vercode' => 'require|checkVercode:thinkphp'
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
        'vercode.require' => '请输入验证码'
    ];

    /**
     * 验证验证码
     * @param string $value 验证码
     * @return bool|string
     */
    public function checkVercode($value)
    {
        return captcha_check($value, 'admin_login') ? true : '验证码错误';
    }
}
