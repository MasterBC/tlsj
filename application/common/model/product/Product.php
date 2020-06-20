<?php

namespace app\common\model\product;

use app\common\model\Common;
use think\facade\Cache;

class Product extends Common
{

    protected $name = 'product';
    // 状态
    public static $statusData = [
        '1' => '启用',
        '2' => '关闭',
    ];
    public static $amountTypeData = [
        '1' => 'k',
        '2' => 'm',
        '3' => 'b',
        '4' => 't',
        '5' => 'aa',
        '6' => '1aa',
    ];

    /**
     * 获取产品名称
     *
     * @return array
     * @author gkdos
     * 2019-09-19T22:06:55+0800
     */
    public static function getProductNames()
    {
        return self::cache('product_names')->column('product_name', 'id');
    }

    public static function getAmountTypeData($num, $type)
    {

        switch ($type) {
            case 1:
                return $num * 1000;
                break;
            case 2:
                return $num * 1000000;
                break;
            case 3:
                return $num * 1000000000;
                break;
            case 4:
                return $num * 1000000000000;
                break;
            case 5:
                return $num * 1000000000000000;
                break;
            case 6:
                return $num * 1000000000000000000;
                break;
        }
    }

    /**
     * 根据ID 获取信息
     * @param $id
     * @return array|null|\PDOStatement|string|Model
     */

    /**
     * 根据id获取产品信息
     *
     * @param  int $id 产品id
     * @return array
     * @author gkdos
     * 2019-09-20T17:40:28+0800
     */
    public static function getProductInfoById($id)
    {
        try {
            return self::where('id', (int) $id)->cache('get_product_info_by_id_' . $id)->find();
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
        Cache::rm('product_names');
        if (isset($this->id)) {
            Cache::rm('get_product_info_by_id_' . $this->id);
        }
    }

}
