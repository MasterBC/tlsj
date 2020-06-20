<?php

namespace app\admin\validate;

use think\Validate;

class SharesCrowd extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $regex = [ 'zip'=> "/^[1-9]\d*\.\d*|0\.\d*[1-9]\d*$/"];
    protected $rule = [
        'sid' => 'require',
        'now_price' => 'require|regex:zip',
        'web_total' => 'require|number',
        'user_total' => 'require|number',
        'status' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'sid.require' => '请选择股票名称',
        'now_price.require' => '请输入单价',
        'now_price.regex' => '单价输入错误',
        'web_total.require' => '请输入总数量',
        'web_total.number' => '总数量格式输入错误',
        'user_total.require' => '请输入限购数量',
        'user_total.number' => '限购格式输入错误',
        'status.require' => '请选择发行状态'
    ];

}
