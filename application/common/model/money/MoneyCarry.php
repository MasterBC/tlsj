<?php

namespace app\common\model\money;

use think\facade\Request;
use think\Model;
use think\helper\Time;

class MoneyCarry extends Model
{
    protected $name = 'money_carry';

    /**
     * 根据钱包id查出数据
     * @param int $mid
     * @param int $toMid
     * @param array $field
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMoneyCarryInfoById($mid, $field = [])
    {
        $where = [
            'mid' => $mid
            ,'status' => 1
        ];

        return $this->where($where)->field($field)->find();
    }
}