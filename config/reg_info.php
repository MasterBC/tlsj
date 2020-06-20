<?php

// +----------------------------------------------------------------------
// | 注册参数配置
// | 修改管理员id: 1
// | 修改日期: 2019-12-22 00:21:27
// | ip: 144.0.140.246
// +----------------------------------------------------------------------
/**
 * 原配置
   [
    
    'default_account'         => 18888888888,
    
    'default_login_pass'      => 888888,
    
    'default_pay_pass'        => 999999,
    
    'default_activate_state'  => 1,
    
    'default_reg_user_id'     => 1,
    
    'reg_give_mid'            => 2,
    
    'reg_give_num'            => 100000,
    
    'register_phone_switch'   => 1,
    
    'same_phone_register_num' => 1,
    
    'register_sms_switch'     => false,
];
 */

return [
    // 平台默认账号
    'default_account'         => 18888888888,
    // 平台默认登录密码
    'default_login_pass'      => 888888,
    // 会员注册ID默认起值值
    'default_pay_pass'        => 999999,
    // 平台默认激活状态 1激活 2未激活
    'default_activate_state'  => 1,
    
    'default_reg_user_id'     => 1,
    
    'reg_give_mid'            => 2,
    
    'reg_give_num'            => 10000,
    // 注册启用手机号
    'register_phone_switch'   => true,
    // 同一个手机号允许注册数量
    'same_phone_register_num' => 1,
    // 注册短信验证
    'register_sms_switch'     => false,
];