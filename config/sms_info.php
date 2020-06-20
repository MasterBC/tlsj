<?php

// +----------------------------------------------------------------------
// | 短信参数配置
// | 修改管理员id: 1
// | 修改日期: 2019-12-31 10:45:02
// | ip: 112.231.149.173
// +----------------------------------------------------------------------
/**
 * 原配置
   [
    
    'user_send_sms_switch' => 1,
    
    'verif_code'           => 1,
    
    'sms_user'             => 'fanwenguang',
    
    'sms_key'              => 'd41d8cd98f00b204e980',
    
    'send_sms_time_out'    => 60,
    
    'sms_time_out'         => 60,
    
    'test_send_mobile'     => 18888888888,
];
 */

return [
    // 短信开关
    'user_send_sms_switch' => true,
    // 图形验证码开关
    'verif_code'           => true,
    // 短信接口用户名
    'sms_user'             => 'fanwenguang',
    // 短信接口key
    'sms_key'              => 'd41d8cd98f00b204e980',
    // 短信发送过期时间
    'send_sms_time_out'    => 60,
    // 短信验证有效时间
    'sms_time_out'         => 60,
    // 测试手机号
    'test_send_mobile'     => 15265726575,
];