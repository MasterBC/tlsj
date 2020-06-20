<?php

/**
 * 获取奖金名称
 * @param $id
 * @return string
 */
function get_bonus_name($id = 0)
{
    $data = \app\common\model\Bonus::getBonusNames();

    $data[0] = $data;
    return isset($data[intval($id)]) ? $data[intval($id)] : '';
}