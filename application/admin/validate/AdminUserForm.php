<?php

namespace app\admin\validate;

use think\Validate;
use app\common\model\AdminUser;

class AdminUserForm extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'username' => 'require|checkUsername:thinkphp',
        'mobile' => 'checkMobile:thinkphp',
        'email' => 'checkEmail:thinkphp',
        'password' => 'require|checkPass:thinkphp'
    ];

    /**
     * edit 验证场景定义
     */
    public function sceneEdit()
    {
        return $this->only(['mobile', 'email', 'password'])->remove('password', 'require');
    }

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'username.require' => '请输入用户名',
        'password.require' => '请输入密码'
    ];

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
        if (!check_pass($value)) {
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
        if (!check_mail($value)) {
            return '邮箱格式错误';
        }
        return true;
    }


    /**
     * 验证用户名是否存在
     * @param $value
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkUsername($value)
    {

        $adminUser = new AdminUser();

        $userInfo = $adminUser->getUserByUsername($value);

        if ($userInfo) {
            return '此用户名已存在';
        }
        return true;
    }
}
