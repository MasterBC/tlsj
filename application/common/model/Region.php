<?php

namespace app\common\model;

use think\Model;
use think\facade\Cache;

class Region extends Model
{
    protected $name = 'region';

    /**
     * 写入数据
     * @param array $data
     * @return int|string
     */
    public function addRegion($data)
    {
        $info = [
            'name_cn' => $data['name_cn'],
            'level' => $data['level'],
            'parent_id' => $data['parent_id'],
            'status' => $data['status'],
        ];

        return $this->insertGetId($info);
    }

    /**
     * 获取地址
     * @return mixed
     */
    public function getRegionName()
    {
        $regionName = Cache::remember('get_region_name', function () {
            return $this->column('name_cn', 'id');
        });

        return $regionName;
    }
}
