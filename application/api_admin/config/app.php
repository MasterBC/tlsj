<?php

return [
    'controller_auto_search' => true,
    'dispatch_success_tmpl' => config('app_path') . 'app/admin/view/public/dispatch_jump.tpl',
    'dispatch_error_tmpl' => __DIR__ . '/../view/public/dispatch_jump.tpl',

    // 异常处理
    'exception_handle' => '\app\api_admin\exception\Http',
];