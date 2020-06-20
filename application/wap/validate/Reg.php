<?php

namespace app\wap\validate;

use think\Validate;

class Reg extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
         'tjrAccount' => 'require',
        'mobile' => 'checkMobile:thinkphp',
        'email' => 'checkEmail:thinkphp',
        'password' => 'require|checkPass:thinkphp',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'tjrAccount.require' => '请输入推荐码',
        'account.require' => '请输入账号',
        'mobile.require' => '请输入手机账号',
        'password.require' => '请输入密码',
        'mobileCode.require' => '请输入手机验证码',
        'verify_code.require' => '请输入图形验证码'
    ];

    // 后台添加会员验证场景
    public function sceneAdminAddUser()
    {
        return $this->only(['tjrAccount', 'account', 'mobile', 'email', 'password'])
            ->remove('mobile', 'require');
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
     * 验证验证码
     * @param string $value 验证码
     * @return bool|string
     */
    public function checkVercode($value)
    {
        return captcha_check($value, 'reg') ? true : '验证码错误';
    }
}
