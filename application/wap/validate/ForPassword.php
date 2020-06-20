<?php

namespace app\wap\validate;

use think\Validate;

class ForPassword extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'password' => 'require',
        'repassword' => 'require',
        'mobileCode' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     * @var array
     */
    protected $message = [
        'password.require' => '请输入新登录密码',
        'repassword.require' => '请输入确认新登录密码',
        'mobileCode.require' => '请输入手机验证码',
    ];
}
