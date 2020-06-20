<?php

namespace app\admin\validate;

use think\Validate;

class Goods extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'status' => 'require|between:1,2',
        'is_top' => 'require|between:1,2',
        'is_new' => 'require|between:1,2',
        'is_hot' => 'require|between:1,2',
        'stock' => 'require|number',
        'cat_id' => 'require|number',
        'goods_name' => 'require',
        'shop_price' => 'require',
        'content' => 'require',
        'picture' => 'require'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'    =>    '错误信息'
     *
     * @var array
     */
    protected $message = [
        'status.require' => '请选择状态',
        'status.between' => '状态选择错误',
        'is_top.require' => '请选择推荐状态',
        'is_top.between' => '推荐状态选择错误',
        'is_new.require' => '请选择新品状态',
        'is_new.between' => '新品状态选择错误',
        'is_hot.require' => '请选择热卖状态',
        'is_hot.between' => '热卖状态选择错误',
        'stock.require' => '请输入库存',
        'stock.number' => '库存格式错误',
        'cat_id.require' => '请选择分类',
        'cat_id.number' => '分类格式错误',
        'goods_name.require' => '请输入商品名称',
        'shop_price.require' => '请输入商品价格',
        'content.require' => '请输入商品详情',
        'picture.require' => '请上传商品主图',
    ];

}
