<?php

namespace app\wap\Validate;

use think\Validate;
class AddMessage extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'title' => 'require',
        'type' => 'require',
        'content' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'title.require' => '请输入留言标题',
        'type.require' => '请选择留言类型',
        'content.require' => '请输入内容',
    ];
}