<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // 默认控制器名
    'default_controller'     => 'User',
    'default_action'         => 'index',
    'dispatch_success_tmpl' =>  __DIR__.'/../view/default/public/dispatch_jump.tpl',
    'dispatch_error_tmpl' => __DIR__.'/../view/default/public/dispatch_jump.tpl'
];