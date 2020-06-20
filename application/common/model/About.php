<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;

class About extends Model
{
    protected $name = 'about';

    // 类型
    public static $aboutType = [
        '1' => '注册协议',
        '2' => '关于我们',
    ];

    /**
     * 根据id 查询数据
     * @param $id
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getAboutById($id)
    {
        $where = [
            'id' => (int)$id
        ];
        return self::where($where)->cache('get_about_info_byId_' . $id)->find();
    }

    /**
     * 获取注册协议
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getRegistrationAgreement($type = 1)
    {
        return self::where('type', $type)->where('status', 1)->cache('about_registration_agreement'.$type)->find();
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
            Cache::rm('get_about_info_byId_' . $this->id);
        }
        Cache::rm('about_registration_agreement');
    }
}