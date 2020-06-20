<?php

namespace app\wap\validate;

use think\Validate;

class ForSecpwd extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'secpwd' => 'require',
        'resecpwd' => 'require',
        'mobileCode' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     * @var array
     */
    protected $message = [
        'secpwd.require' => '请输入二级密码',
        'resecpwd.require' => '请确认密码',
        'mobileCode.require' => '请输入手机验证码',
    ];
}
