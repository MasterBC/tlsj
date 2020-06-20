<?php

namespace app\http\middleware;

use think\facade\Session;
use app\common\model\AdminUser;

class checkAdminLogin
{
    public function handle($request, \Closure $next)
    {
        if (strtolower($request->controller()) == 'login') {
            if (Session::has(AdminUser::SESSION_NAME)) {
                return Session::has('redirect_url') ? redirect()->restore() : redirect('index/index');
            }
        } else {
            $outTime = ((int)zf_cache('security_info.admin_past_due_time') <= 0 ? 10 : (int)zf_cache('security_info.admin_past_due_time')) * 60;
            if (!Session::has(AdminUser::SESSION_NAME) || time() - Session::get(AdminUser::SESSION_NAME . '_past_due_time') > $outTime) {
                Session::has(AdminUser::SESSION_NAME) && Session::delete(AdminUser::SESSION_NAME);
                if ($request->isAjax()) {
                    return json()->data(['code' => 1001, 'msg' => '请重新登陆']);
                } else {
                    return $request->isGet() ? redirect('login/index')->remember() : redirect('login/index');
                }
            } else {
                Session::set(AdminUser::SESSION_NAME . '_past_due_time', time());
            }
        }

        return $next($request);
    }
}
