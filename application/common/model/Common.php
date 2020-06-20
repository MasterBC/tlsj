<?php

namespace app\common\model;

use think\Model;

class Common extends Model
{

    /**
     * 根据id获取信息
     * @param int $id
     * @return Common|null
     * @throws \think\Exception\DbException
     */
    public function getInfoById($id = 0)
    {
        $info = self::get($id);
        return $info;
    }

}