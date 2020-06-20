<?php

// +----------------------------------------------------------------------
// | 注册参数配置
// | 修改管理员id: 1
// | 修改日期: 2019-12-11 22:09:32
// | ip: 116.252.232.146
// +----------------------------------------------------------------------
/**
 * 原配置
   [
    
    'web_past_due_time'           => 60,
    
    'admin_past_due_time'         => 60,
    
    'web_pass_min'                => 6,
    
    'web_pass_max'                => 9,
    
    'auth_username_type'          => 9,
    
    'web_totao_level'             => 99998,
    
    'message_cate'                => '注册|提现',
    
    'sign_rand_arr_money'         => '0.12-1.18-0.19-0.20-0.21',
    
    'sign_total_hongbao_money'    => 10,
    
    'work_rand_arr_money'         => '0.12-1.18-0.19-0.20-0.21',
    
    'work_total_hongbao_money'    => 10,
    
    'random_rand_arr_money'       => '0.12-1.18-0.19-0.20-0.21',
    
    'random_total_hongbao_money'  => 5,
    
    'upgrade_rand_arr_money'      => '0.1-0.2-0.3-0.4-0.5',
    
    'upgrade_total_hongbao_money' => 50000,
    
    'upgrade_red_envelope'        => '1-2-3-4-5-6-7-8-9-10-11-12-13-14-15-16-17-18-19-20-21-22-23-24-25-26-27-28-29-30-31-32-33-34-35-36-37',
    
    'niuliang_zhinv_reward'       => 67,
    
    'bonus_config'                => '20-10',
    
    'video_day_total_num'         => 20,
    
    'shake_day_total_num'         => 20,
    
    'shake_rand_block_num'        => '0.12-1.18-0.19-0.20-0.21',
    
    'shake_rand_block_per'        => 5,
    
    'shake_rand_money_two_num'    => '0.12-1.18-0.19-0.20-0.21',
    
    'shake_rand_money_two_per'    => 5,
    
    'shake_rand_money_one_num'    => '0.12-1.18-0.19-0.20-0.21',
    
    'shake_rand_money_one_per'    => 5,
    
    'turn_day_give_num'           => 12,
    
    'turn_video_give_num'         => 3,
    
    'tjr_num_plus'                => 0.5,
    
    'sign_num_plus'               => 0.01,
    
    'close_num_plus'              => 10,
];
 */

return [
    // 会员未操作自动退出时间 单位: 分钟
    'web_past_due_time'           => 600,
    // 会员未操作自动退出时间 单位: 分钟
    'admin_past_due_time'         => 60,
    
    'web_pass_min'                => 6,
    
    'web_pass_max'                => 9,
    
    'auth_username_type'          => 9,
    // 全网产出限制数量
    'web_totao_level'             => 10000,
    // 留言分类 
    'message_cate'                => '注册|提现',
    
    'sign_rand_arr_money'         => '0.1',
    
    'sign_total_hongbao_money'    => 10,
    
    'work_rand_arr_money'         => '0.12-1.18-0.19-0.20-0.21',
    
    'work_total_hongbao_money'    => 10,
    
    'random_rand_arr_money'       => '0.12-1.18-0.19-0.20-0.21',
    
    'random_total_hongbao_money'  => 5,
    
    'upgrade_rand_arr_money'      => '0.1-0.2-0.3-0.4-0.5',
    
    'upgrade_total_hongbao_money' => 50000,
    
    'upgrade_red_envelope'        => '1-2-3-4-5-6-7-8-9-10-11-12-13-14-15-16-17-18-19-20-21-22-23-24-25-26-27-28-29-30-31-32-33-34-35-36-37',
    
    'niuliang_zhinv_reward'       => 67,
    
    'bonus_config'                => '20-10',
    
    'video_day_total_num'         => 20,
    
    'shake_day_total_num'         => 20,
    
    'shake_rand_block_num'        => '0.12-1.18-0.19-0.20-0.21',
    
    'shake_rand_block_per'        => 5,
    
    'shake_rand_money_two_num'    => '0.12-1.18-0.19-0.20-0.21',
    
    'shake_rand_money_two_per'    => 5,
    
    'shake_rand_money_one_num'    => '0.12-1.18-0.19-0.20-0.21',
    
    'shake_rand_money_one_per'    => 5,
    
    'turn_day_give_num'           => 12,
    
    'turn_video_give_num'         => 10,
    
    'tjr_num_plus'                => 0.5,
    
    'sign_num_plus'               => 0.01,
    
    'close_num_plus'              => 10,
];