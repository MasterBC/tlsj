<?php

/**
 * 获取会员动态类型
 * @param int $type
 * @return mixed|string
 */
function user_log_type($type = 0)
{
    $data = [
        '1' => '登录',
        '2' => '冻结',
        '3' => '解冻'
    ];

    $data[0] = $data;

    return isset($data[$type]) ? $data[$type] : '其他';
}

/**
 * 获取留言类型
 * @param $id
 * @return array
 */
function get_message_type($id = 0)
{
    $messageCate = explode('|', zf_cache('security_info.message_cate'));
    $data = [];
    foreach ($messageCate as $k => $v) {
        $data[$k + 1] = $v;
    }
    $data[0] = $data;

    return isset($data[$id]) ? $data[$id] : '';
}