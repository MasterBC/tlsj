<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;

class NewsCate extends Model
{
    protected $name = 'news_cate';

    /**
     * 新闻分类
     * @param array $field
     * @return array
     */
    public static function getNewsCateField($field = [])
    {
        $where = [
            'status' => 1
        ];
        return self::where($where)->column($field);
    }

    /**
     * 根据id 查询数据
     * @param $id
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getNewsCateById($id)
    {
        $where = [
            'cate_id' => (int)$id
        ];
        return self::where($where)->cache('news_cate_info_byId_' . $id)->find();
    }

    /**
     * 获取分类名称
     * @return array
     */
    public static function getNewsCateNames()
    {
        return self::where('status', 1)->cache('news_cate_names')->column('title', 'cate_id');
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
        Cache::rm('news_cate_names');
        if (isset($this->cate_id)) {
            Cache::rm('news_cate_info_byId_' . $this->cate_id);
        }
    }
}