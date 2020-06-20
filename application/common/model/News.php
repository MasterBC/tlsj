<?php

namespace app\common\model;

use think\facade\Request;
use think\db\Where;
use think\facade\Cache;

class News extends Common
{
    protected $name = 'news';

    /**
     * 获取新闻数据
     * @param $data
     * @param string $where 状态
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNewsLog()
    {
        $where = new Where();
        $where['status'] = 1;
        Request::param('cate_id') && $where['cate_id'] = Request::param('cate_id');
        $p = Request::param('p', 0, 'intval');
        $pSize = Request::param('size', 8, 'intval');
        $result = $this->where($where)->limit($p * $pSize, $pSize)->select();
        return $result;
    }

    /**
     * 根据id 查询数据
     * @param $id
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getNewsById($id)
    {
        $where = [
            'id' => (int)$id
        ];
        return self::where($where)->cache('news_info_byId_' . $id)->find();
    }

    /**
     * 获取上一篇数据
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPrevNewsInfo()
    {
        $info = $this->where('id', '<', $this->id)->cache('get_prev_news_info_byId_' . $this->id, null, 'prev_news')->order('id desc')->limit(1)->find();

        return $info;
    }

    /**
     * 获取下一篇数据
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNextNewsInfo()
    {
        $info = $this->where('id', '>', $this->id)->cache('get_next_news_info_byId_' . $this->id, null, 'next_news')->order('id desc')->limit(1)->find();

        return $info;
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
        Cache::clear('prev_news');
        Cache::clear('next_news');
        if (isset($this->id)) {
            Cache::rm('news_info_byId_' . $this->id);
        }
    }
}