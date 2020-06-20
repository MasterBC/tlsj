<?php

namespace app\http\middleware;

use think\auth\Auth;
use Session;
use app\common\model\AdminUser;

class checkAuth
{
    public function handle($request, \Closure $next)
    {
        $module = strtolower($request->module());
        $controller = $request->controller();
        $action = $request->action();
        if(strtolower($controller) != 'index') {
            if($module == 'admin') {
                $adminId = (new AdminUser())->getAdminUserId();
            } elseif($module == 'seller') {
                $adminId = Session::get('seller_id');
            }

            // 获取auth实例
            $auth = Auth::instance();

            if(!$auth->check($controller.'/'.$action, $adminId)) {
                return $request->isAjax() ? json(['code' => -1, 'msg' => '没有权限']) : redirect('error/permissionDenied');
            }
        }


        return $next($request);
    }
}
