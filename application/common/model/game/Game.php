<?php

namespace app\common\model\game;

use app\common\model\Common;
use think\facade\Cache;

class Game extends Common
{

    protected $name = 'game';
    // 状态
    public static $statusData = [
        '1' => '启用',
        '2' => '关闭',
    ];
    // 状态
    public static $isTypeData = [
        '1' => '小编推荐',
        '2' => '休闲游戏',
    ];

    /**
     * 获取产品名称
     *
     * @return array
     * @author gkdos
     * 2019-09-19T22:06:55+0800
     */
    public static function getGameNames()
    {
        return self::cache('game_names')->column('gram_name', 'id');
    }

    /**
     * 根据id获取产品信息
     *
     * @param  int $id 产品id
     * @return array
     * @author gkdos
     * 2019-09-20T17:40:28+0800
     */
    public static function getGameInfoById($id)
    {
        try {
            return self::where('id', (int) $id)->cache('get_gram_info_by_id_' . $id)->find();
        } catch (\Exception $e) {
            Log::write('查询失败: (id:' . $id . ')' . $e->getMessage(), 'error');
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
     * 清除缓存
     */
    public function clearCache()
    {
        Cache::rm('gram_names');
        if (isset($this->id)) {
            Cache::rm('get_gram_info_by_id_' . $this->id);
        }
    }

}
