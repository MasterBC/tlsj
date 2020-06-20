<?php


/**
 * 递归菜单
 * @param $array
 * @param int $type
 * @param int $fid
 * @param int $level
 * @return array
 */
function get_column($array, $type = 1, $fid = 0, $level = 0)
{
    $column = [];
    if ($type == 2) {
        foreach ($array as $key => $vo) {
            if ($vo['pid'] == $fid) {
                $vo['level'] = $level;
                $column[$key] = $vo;
                $column [$key][$vo['id']] = get_column($array, $type, $vo['id'], $level + 1);
            }
        }
    } else {
        foreach ($array as $key => $vo) {
            if ($vo['pid'] == $fid) {
                $vo['level'] = $level;
                $column[] = $vo;
                $column = array_merge($column, get_column($array, $type, $vo['id'], $level + 1));
            }
        }
    }

    return $column;
}