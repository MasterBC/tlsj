<?php

namespace app\common\model\money;

use think\Model;
use think\facade\Cache;

class MoneyTransform extends Model
{
    protected $name = 'money_transform';

    /**
     * 获取钱包所有转换参数
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMoneyTransformInfo()
    {
        $where = [
            'status' => 1
        ];
        return $this->where($where)->select();
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
    public function getMoneyTransformInfoById($mid = 0, $toMid = 0, $field = [])
    {
        $where = [
            'mid' => $mid
            , 'to_mid' => $toMid
            , 'status' => 1
        ];

        return $this->where($where)->field($field)->find();
    }

    /**
     * 根据id查询转换参数
     * @param $id
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTransformInfoById($id)
    {
        return $this->where('id', (int)$id)->cache('get_money_transform_info_byid_' . $id, null, 'money_transform')->find();
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
        Cache::clear('money_transform');
    }
}