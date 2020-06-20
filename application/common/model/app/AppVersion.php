<?php

namespace app\common\model\app;

use app\api\service\Token;
use org\AesSecurity;
use think\Db;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Log;
use think\facade\Request;
use think\Model;

class AppVersion extends Model
{
    protected $name = 'app_version';

    /**
     * 获取最后一个版本信息
     * @param string $system 系统 android ios
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getLastVersion($system)
    {
        return self::where('system', $system)->cache('get_app_last_version_' . $system, null, 'app_version_info')->order('id', 'desc')->find();
    }

    /**
     * 根据id获取版本信息
     * @param $id
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getVersionInfoById($id)
    {
        return self::where('id', $id)->cache('get_app_version_info_' . $id)->find();
    }


    /**
     * 修改后操作
     */
    public function _afterUpdate()
    {
        $this->clearCache();
    }

    /**
     * 清除缓存
     */
    public function clearCache()
    {
        Cache::clear('app_version_info');
        if (isset($this->id)) {
            Cache::rm('get_app_version_info_' . $this->id);
        }
    }
}