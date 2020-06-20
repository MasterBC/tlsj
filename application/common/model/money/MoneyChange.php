<?php

namespace app\common\model\money;

use think\facade\Request;
use think\Model;
use think\helper\Time;
use think\facade\Cache;

class MoneyChange extends Model
{
    protected $name = 'money_change';


    /**
     * 根据id查询转账参数
     * @param $id
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getChangeInfoById($id)
    {
        return $this->where('id', (int)$id)->cache('get_money_change_info_byid_' . $id, null, 'money_change')->find();
    }

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
    public function getChangeMoneyInfo($mid = 0, $toMid = 0, $field = [])
    {
        $where = [
            'mid' => $mid
            , 'to_mid' => $toMid
            , 'status' => 1
        ];

        return $this->where($where)->field($field)->find();
    }

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
    public function getChangeMoneyField($field = [])
    {
        $where = [
            'status' => 1
        ];
        return $this->where($where)->column($field);
    }

    /**
     * 修改后操作
     */
    public function _afterUpdate()
    {
        $this->clearCache();
    }

    /**
     * 清除缓存
     */
    private function clearCache()
    {
        Cache::clear('money_change');
    }
}
