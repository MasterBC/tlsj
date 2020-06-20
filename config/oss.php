<?php

// +----------------------------------------------------------------------
// | oss设置
// +----------------------------------------------------------------------
return [

    // oss上传开关
    'oss_upload' => false,
    // 本站上传的文件夹
    'oss_root_app_path' => '',
    // 用于访问的域名
    'oss_url' => '',
    'public' => [
        // 城市名称
        'city' => '深圳',
        // 经典网络 or VPC
        'network_type' => '经典网络',
        // $AccessKeyId
        'access_key_id' => '',
        // $AccessKeySecret
        'access_key_secret' => '',
        // Bucket
        'oss_root_path' => 'zfuwl'
    ],
    'private' => [
        // 城市名称
        'city' => '深圳',
        // 经典网络 or VPC
        'network_type' => '经典网络',
        // $AccessKeyId
        'access_key_id' => '',
        // $AccessKeySecret
        'access_key_secret' => '',
        // Bucket
        'oss_root_path' => ''
    ],
];