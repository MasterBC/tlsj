<?php

namespace app\wap\validate;

use think\Validate;

class ChangeBlock extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'toAccount' => 'require',
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
        'toAccount.require' => '请输入对方的钱包地址或者手机号',
        'num.require' => '请输入转出数量',
        'secpwd.require' => '请输入二级密码',
    ];
}
