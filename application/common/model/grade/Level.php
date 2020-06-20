<?php

namespace app\common\model\grade;

use app\common\model\Common;
use think\db\Where;
use think\Model;
use think\facade\Cache;
use think\facade\Log;

class Level extends Common
{
    protected $name = 'level';

    /**获取等级数据
     * @param string $field 字段
     * @return array
     */
    public static function getLevelField($field = '')
    {
        $where = [
            'status' => 1
        ];
        return self::where($where)->column($field);
    }

    /**
     * 根据会员等级获取数据
     * @param string $levelId
     * @param string $field
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLevelFieldById($levelId= '',$field = '')
    {
         $where = new Where();
         if ($levelId == 0 ){
             $where = ['status' => 1];
             return $this->where($where)->column($field);
         }else{
             $where['level_id'] = ['egt',$levelId];
             $where['status'] = 1;
             return $this->where($where)->field($field)->select();
         }
    }

    /**
     * 获取等级信息
     * @param $id
     * @return array|null|\PDOStatement|string|Model
     */
    public static function getLevelInfoById($id)
    {
        try {
            return self::where('level_id', (int)$id)->cache('get_level_info_' . $id)->find();
        } catch (\Exception $e) {
            Log::write('查询等级信息失败: (id:' . $id . ')' . $e->getMessage(), 'error');
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
    public static function getLevelNames()
    {
        return self::cache('level_name_cn')->column('name_cn', 'level_id');
    }

    /**
     * 清除缓存
     */
    public function clearCache()
    {
        Cache::rm('level_name_cn');
        if (isset($this->level_id)) {
            Cache::rm('get_level_info_' . $this->level_id);
        }
    }
}