<?php
namespace app\wap\validate;

use think\Validate;

class AddAddress extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $regex = [ 'zip' => "^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\\d{8}$"];
    protected $rule = [
        'username' => 'require',
        'mobile' => 'require|regex:zip',
        'address' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'username.require' => '收货人不能为空',
        'mobile.require' => '手机号码不能为空',
        'mobile.regex' => '手机号码格式错误',
        'address.require' => '请输入详细地址',
    ];
}