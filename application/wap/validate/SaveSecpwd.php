<?php

namespace app\wap\validate;

use think\Validate;

class SaveSecpwd extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'oldsecpwd' => 'require',
        'secpwd' => 'require',
        'resecpwd' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     * @var array
     */
    protected $message = [
        'oldsecpwd.require' => '请输入原二级密码',
        'secpwd.require' => '请输入二级密码',
        'resecpwd.require' => '请确认密码',
    ];

    /**
     * edit 验证场景定义
     * @return SaveSecpwd
     */
    public function sceneEdit()
    {
        return $this->only(['secpwd', 'resecpwd']);
    }
}
