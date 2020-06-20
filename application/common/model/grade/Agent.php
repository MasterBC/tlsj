<?php

namespace app\common\model\grade;

use think\facade\Cache;
use think\facade\Log;
use app\common\model\Common;

class Agent extends Common
{
    protected $name = 'agent';

    /**
     * 获取领导等级信息
     * @param $id
     * @return array|null|\PDOStatement|string|Model
     */
    public static function getAgentInfoById($id)
    {
        try {
            return self::where('id', (int)$id)->cache('get_agent_info_' . $id)->find();
        } catch (\Exception $e) {
            Log::write('查询报单等级信息失败: (id:' . $id . ')' . $e->getMessage(), 'error');
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
    public static function getAgentNames()
    {
        return self::cache('agent_name_cn')->column('name_cn', 'id');
    }

    /**
     * 清除缓存
     */
    public function clearCache()
    {
        Cache::rm('agent_name_cn');
        if (isset($this->id)) {
            Cache::rm('get_agent_info_' . $this->id);
        }
    }
}