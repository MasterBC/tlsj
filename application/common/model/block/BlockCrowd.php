<?php

namespace app\common\model\block;

use think\Model;
use think\facade\Log;
use think\facade\Cache;

class BlockCrowd extends Model
{

    public static $status = [
        '1' => '准备中',
        '2' => '进行中',
        '3' => '已结束'
    ];


    protected $name = 'block_crowd';

    /**
     * 根据id获取众筹信息
     * @param $id
     * @param array|string $field
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getCrowdInfoById($id, $field = [])
    {

        return self::where('id', (int)$id)->field($field)->find();

    }
}
