<?php
namespace app\wap\validate;

use think\Validate;

class AddBank extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $regex = [ 'zip' => '/^([1-9]{1})(\d{14}|\d{18})$/'];
    protected $rule = [
        'bank_name' => 'require',
        'bank_account' => 'require|regex:zip',
        'opening_id' => 'require',
        'bank_address' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'bank_name.require' => '用户名不能为空',
        'bank_account.require' => '银行账户号码不能为空',
        'bank_account.regex' => '银行账户号码格式错误',
        'opening_id.require' => '请选择开户银行',
        'bank_address.require' => '开户银行支行不能为空',
    ];
}