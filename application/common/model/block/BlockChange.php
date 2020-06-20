<?php

namespace app\common\model\block;

use think\Model;
use think\facade\Cache;

class BlockChange extends Model
{
    protected $name = 'block_change';

    /**
     * 根据货币id查出数据
     * @param int $bid
     * @param int $toBid
     * @param int $status
     * @param array $field
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getChangeBlockInfo($bid, $toBid, $status = 1, $field = [])
    {
        $where = [
            'bid' => $bid,
            'to_bid' => $toBid,
            'status' => $status
        ];

        if (is_array($field)) {
            $cacheKey = implode('_', $field);
        } else {
            $cacheKey = str_replace(',', '_', $field);
        }
        $cacheKey .= $bid . '_' . $toBid . '_' . $status;

        return $this->where($where)->field($field)->cache($cacheKey, null, 'block_change')->find();
    }

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
        return $this->where('id', (int)$id)->cache('get_block_change_info_byid_' . $id, null, 'block_change')->find();
    }


    /**
     * 查出货币转账参数
     * @param int $id
     * @param array $field
     * @param int $type
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getChangeBlockData($id = 0, $field = [], $type = 1)
    {
        $id && $where['id'] = $id;
        $where['status'] = 1;
        switch ($type) {
            case 1:
                return $this->where($where)->field($field)->select();
                break;
            case 2:
                return $this->where($where)->field($field)->find();
                break;
        }
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
        Cache::clear('block_change');
    }
}