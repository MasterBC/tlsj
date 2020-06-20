<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;

class Bonus extends Model
{
    protected $name = 'bonus';

    /**
     * 获取数据字段
     * @param $field
     * @return array
     */
    public function getBonusInfoField($field = [])
    {
        $where = [
            'status' => 1
        ];
        return $this->where($where)->column($field);
    }

    /**
     * 获取奖金信息
     * @param $id
     * @return array|null|\PDOStatement|string|Model
     */
    public static function getInfoById($id)
    {
        try {
            return self::where('id', (int)$id)->cache('get_bonus_info_' . $id)->find();
        } catch (\Exception $e) {
            Log::write('查询奖金信息失败: (id:' . $id . ')' . $e->getMessage(), 'error');
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
     * 获取单个奖金名称
     * @param int $id
     * @return string
     */
    public static function getBonusNameById($id = 0)
    {
        return self::getBonusNames()[$id] ?? '';
    }

    /**
     * 获取奖金名称
     * @return array
     */
    public static function getBonusNames()
    {
        return self::cache('bonus_name_cn')->column('name_cn', 'id');
    }

    /**
     * 清除缓存
     */
    public function clearCache()
    {
        Cache::rm('bonus_name_cn');
        if (isset($this->id)) {
            Cache::rm('get_bonus_info_' . $this->id);
        }
    }

}