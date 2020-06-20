<?php

namespace app\http\middleware;

use think\facade\Session;
use app\common\model\Users;

class CheckWapLogin
{
    public function handle($request, \Closure $next)
    {
        if ((is_array(zf_cache('login_info.open_start_time')) && max(zf_cache('login_info.open_start_time'))) || (is_array(zf_cache('login_info.open_out_time')) && max(zf_cache('login_info.open_out_time')))) {
            $startTime = (float)zf_cache('login_info.open_start_time.' . date('w'));
            $outTime = (float)zf_cache('login_info.open_out_time.' . date('w'));
            if ($startTime > date('H') || $outTime < date('H')) {
                return redirect('Error/webClose');
            }
        }
        $userInfo = (new Users)->getSessionUserInfo();
        $avoid_lock_user_id = intval(zf_cache('login_info.avoid_lock_user_id')) > 0 ? intval(zf_cache('login_info.avoid_lock_user_id')) : 1;
        if (zf_cache('login_info.web_kg') === false && $userInfo['user_id'] != $avoid_lock_user_id) {
            return redirect('Error/webClose');
        }
        $notCheckLoginControllers = [
            'reg', 'smscode', 'shop', 'goods', 'drumbeat', 'task'
        ];
        if (!in_array(strtolower($request->controller()), $notCheckLoginControllers)) {
            if (strtolower($request->controller()) == 'login') {
                if (Users::checkLogin()) {
                    return Session::has('redirect_url') ? redirect()->restore() : redirect('/User/index');
                }
            } else {
                if (!Users::checkLogin()) {
                    if ($request->isAjax()) {
                        return json()->data(['code' => 1001, 'msg' => '请重新登陆']);
                    } else {
                        return $request->isGet() ? redirect('login/accountindex')->remember() : redirect('login/accountindex');
                    }
                }
            }
        }

        return $next($request);
    }
}
