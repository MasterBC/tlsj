<?php

namespace app\admin\validate;

use think\Validate;

class SharesRise extends Validate
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
        'trade_num' => 'require|number',
        'now_price' => 'require|regex:zip',
        'time_num' => 'require',
        'status' => 'require',
        'note' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'sid.require' => '请选择股票名称',
        'trade_num.require' => '请设置交易数量',
        'stage.number' => '交易数量输入有误',
        'now_price.require' => '请输入价格',
        'now_price.regex' => '价格输入错误',
        'status.require' => '请选择发行状态',
        'time_num.require' => '请选择执行时间',
        'note.require' => '备注不能为空'
    ];

}
