<?php

namespace app\wap\validate;

use think\Validate;

class TransformBlock extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'num' => 'require',
        'secpwd' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'num.require' => '请输入转出数量',
        'secpwd.require' => '请输入二级密码',
    ];
}
