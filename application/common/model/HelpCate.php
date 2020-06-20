<?php

namespace app\common\Model;

use think\Model;
use think\facade\Cache;

class HelpCate extends Model
{
    protected $name = 'help_cate';

    /**
     * 根据ID 获取帮助中心分类数据
     * @param int $id id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getHelpCateById($id)
    {
        $where = [
            'cate_id' => (int)$id
        ];
        return self::where($where)->cache('get_help_cate_info_byid_' . $id)->find();
    }

    /**
     * 获取分类名称
     * @return array
     */
    public static function getHelpCateNames()
    {
        return self::where('status', 1)->cache('help_cate_name')->column('title', 'cate_id');
    }

    /**
     * 添加后操作
     */
    public function _afterInsert()
    {
        $this->clearCache();
    }

    /**
     * 修改后操作
     */
    public function _afterUpdate()
    {
        $this->clearCache();
    }

    /**
     * 删除后操作
     */
    public function _afterDelete()
    {
        $this->clearCache();
    }

    /**
     * 清除缓存
     */
    public function clearCache()
    {
        Cache::rm('help_cate_name');
        if (isset($this->cate_id)) {
            Cache::rm('get_help_cate_info_byid_' . $this->cate_id);
        }
    }
}