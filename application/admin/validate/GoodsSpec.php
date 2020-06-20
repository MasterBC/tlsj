<?php

namespace app\admin\validate;

use think\Validate;
use app\common\model\goods\GoodsSpec as GoodsSpecModel;

class GoodsSpec extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'    =>    ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'status' => 'require|between:1,2',
        'name' => 'require|checkUnique:thinkphp',
        'spec_value' => 'require'
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
        'name.require' => '请输入规格名称',
        'spec_value.require' => '请输入规格项',
    ];

    /**
     * 检测规格名称是否唯一
     * @param $value
     * @param $rule
     * @param array $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkUnique($value, $rule, $data = [])
    {
        $goodsSpecModel = new GoodsSpecModel();


        $id = isset($data['id']) ? $data['id'] : 0;

        if (!$goodsSpecModel->checkNameUnique($value, $id)) {
            return '此规格名称已存在';
        }

        return true;
    }

}
