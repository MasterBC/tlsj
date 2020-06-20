<?php

namespace app\common\model;

use think\Model;
use think\facade\Request;
use think\facade\Cache;

class Help extends Model
{
    protected $name = 'help';

    /**
     * 获取帮助中心数据
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getHelpIndex()
    {
        $where = [
            'status' => 1
        ];
//        $p = intval(Request::param('p'));
//        $pSize = 8;
        $info = $this->where($where)->select();
        return $info;
    }

    /**
     * 根据ID 获取相应的数据
     * @param $id
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getHelpInfoById($id)
    {
        $where = [
            'id' => (int)$id
        ];

        return self::where($where)->cache('get_help_info_byid_' . $id)->find();
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
        if (isset($this->id)) {
            Cache::rm('get_help_info_byid_' . $this->id);
        }
    }
}