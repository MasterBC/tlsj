<?php

namespace app\common\model\grade;

use app\common\model\Common;
use think\facade\Cache;
use think\facade\Log;

class Leader extends Common
{
    protected $name = 'leader';

    /**
     * 获取领导等级信息
     * @param $id
     * @return array|null|\PDOStatement|string|Model
     */
    public static function getLeaderInfoById($id)
    {
        try {
            return self::where('id', (int)$id)->cache('get_leader_info_' . $id)->find();
        } catch (\Exception $e) {
            Log::write('查询领导等级信息失败: (id:' . $id . ')' . $e->getMessage(), 'error');
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
     * 获取等级名称
     * @return array
     */
    public static function getLeaderNames()
    {
        return self::cache('leader_name_cn')->column('name_cn', 'id');
    }

    /**
     * 清除缓存
     */
    public function clearCache()
    {
        Cache::rm('leader_name_cn');
        if (isset($this->id)) {
            Cache::rm('get_leader_info_' . $this->id);
        }
    }
}