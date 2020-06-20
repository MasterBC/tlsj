<?php

namespace app\common\model\block;

use think\Model;
use think\facade\Cache;

class BlockTransform extends Model
{
    protected $name = 'block_transform';

    /**
     * 根据转换的货币查出信息
     * @param int $bid
     * @param int $toBid
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function transformInfo($bid = 1, $toBid = 2)
    {
        $where = [
            'bid' => $bid,
            'to_bid' => $toBid
        ];
        $cacheKey = 'get_block_transfrom_info_by_' . $bid . '_' . $toBid;
        return $this->where($where)->cache($cacheKey, null, 'block_transform')->find();
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
        return $this->where('id', (int)$id)->cache('get_block_transform_info_byid_' . $id, null, 'block_transform')->find();
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
        Cache::clear('block_transform');
    }
}