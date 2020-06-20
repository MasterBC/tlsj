<?php

/**
 * 获取等级名称
 * @param $id
 * @return string
 */
function get_leader_name($id = 0)
{
    $data = \app\common\model\grade\Leader::getLeaderNames();

    $data[0] = $data;
    return isset($data[intval($id)]) ? $data[intval($id)] : '';
}