<?php

namespace app\wap\validate;

use think\Validate;

class Forgot extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'account' => 'require',
        'mobile' => 'require',
        'password' => 'require',
        'repassword' => 'require',
        'mobileCode' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'account.require' => '请输入账号',
        'mobile.require' => '请输入手机号',
        'password.require' => '请输入密码',
        'mobileCode.require' => '请输入手机验证码',
    ];
}
