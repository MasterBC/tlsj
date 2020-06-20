<?php

/**
 * 获取等级名称
 * @param $id
 * @return string
 */
function get_level_name($id = 0)
{
    $data = \app\common\model\grade\Level::getLevelNames();

    $data[0] = $data;
    return isset($data[intval($id)]) ? $data[intval($id)] : '';
}

/**
 * 获取等级信息
 * @param $levelId
 * @return mixed
 */
function get_level_info($levelId)
{
    return \app\common\model\grade\Level::getLevelInfoById($levelId);
}