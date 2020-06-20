<?php

namespace app\wap\validate;

use think\Validate;

class Security extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'p_one' => 'require',
        'd_one' => 'require',
        'p_two' => 'require',
        'd_two' => 'require',
        'p_three' => 'require',
        'd_three' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'p_one.require' => '请输入问题一',
        'd_one.require' => '请输入问题答案一',
        'p_two.require' => '请输入问题二',
        'd_two.require' => '请输入问题答案二',
        'p_three.require' => '请输入问题三',
        'd_three.require' => '请输入问题答案三',
    ];
}
