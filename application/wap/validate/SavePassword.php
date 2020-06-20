<?php

namespace app\wap\validate;

use think\Validate;

class SavePassword extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'oldpassword' => 'require',
        'password' => 'require',
        'repassword' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'oldpassword.require' => '请输入原登录密码',
        'password.require' => '请输入新登录密码',
        'repassword.require' => '请确认登录密码',
    ];
}
