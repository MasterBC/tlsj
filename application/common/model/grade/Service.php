<?php

namespace app\common\model\grade;

use think\facade\Cache;
use think\facade\Log;
use app\common\model\Common;

class Service extends Common
{
    protected $name = 'service';

    /**
     * 获取领导等级信息
     * @param $id
     * @return array|null|\PDOStatement|string|Model
     */
    public static function getServiceInfoById($id)
    {
        try {
            return self::where('id', (int)$id)->cache('get_service_info_' . $id)->find();
        } catch (\Exception $e) {
            Log::write('查询代理等级信息失败: (id:' . $id . ')' . $e->getMessage(), 'error');
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
    public static function getServiceNames()
    {
        return self::cache('service_name_cn')->column('name_cn', 'id');
    }

    /**
     * 清除缓存
     */
    public function clearCache()
    {
        Cache::rm('service_name_cn');
        if (isset($this->id)) {
            Cache::rm('get_service_info_' . $this->id);
        }
    }
}