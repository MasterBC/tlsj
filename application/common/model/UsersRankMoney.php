<?php

namespace app\common\model;

use app\common\model\Common;
use think\facade\Cache;

class UsersRankMoney extends Common
{

    protected $name = 'users_rank_money';

    /**
     * 根据ID 获取信息
     * @param $id
     * @return array|null|\PDOStatement|string|Model
     */
    public static function getProductConfigInfoId($id)
    {
        try {
            return self::where('id', (int) $id)->cache('get_product_config_' . $id)->find();
        } catch (\Exception $e) {
            Log::write('查询失败: (id:' . $id . ')' . $e->getMessage(), 'error');
            return [];
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
    public function clearCache()
    {
        if (isset($this->id)) {
            Cache::rm('get_product_config_' . $this->id);
        }
    }

}
