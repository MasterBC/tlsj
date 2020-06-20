<?php

namespace app\common\model;

use think\Model;

class Country extends Model
{
    protected $name = 'country';

    /**
     * 写入数据
     * @param array $data
     * @return int|string
     */
    public function addCountry($data)
    {
        $info = [
            'code' => $data['code'],
            'name_cn' => $data['name_cn'],
            'name_en' => $data['name_en'],
            'area_code' => $data['area_code'],
        ];

        return $this->insertGetId($info);
    }
}
