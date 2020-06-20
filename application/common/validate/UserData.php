<?php

namespace app\common\validate;

use think\Validate;

class UserData extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'username' => 'require|max:20',
        'email' => 'checkEmail:thinkphp',
        'mobile' => 'checkMobile:thinkphp',
        'password' => 'checkPass:thinkphp',
        'secpwd' => 'checkPayPass:thinkphp',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'username.require' => '用户名不能为空',
        'username.max' => '用户名称过长',
        'email.require' => '邮箱不能为空',
    ];

    // 后台修改会员信息验证场景
    public function sceneAdminSaveUserData()
    {
        return $this->only(['email', 'username', 'mobile', 'email', 'password', 'secpwd'])
            ->remove('username', 'require');
    }


    /**
     * 检测手机号格式
     * @param $value
     * @return bool|string
     */
    public function checkMobile($value)
    {
        if (!check_mobile($value)) {
            return '手机号格式错误';
        }
        return true;
    }


    /**
     * 检测邮箱格式
     * @param $value
     * @return bool|string
     */
    public function checkEmail($value)
    {
        if ($value != '' && !check_mail($value)) {
            return '邮箱格式错误';
        }
        return true;
    }

    /**
     * 检测密码格式
     * @param $value
     * @return bool|string
     */
    public function checkPass($value)
    {
        if ($value != '' && !check_pass($value)) {
            return '密码格式错误';
        }
        return true;
    }

    /**
     * 检测二级密码格式
     * @param $value
     * @return bool|string
     */
    public function checkPayPass($value)
    {
        if ($value != '' && !check_pass($value)) {
            return '二级密码格式错误';
        }
        return true;
    }
}
