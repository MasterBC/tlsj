<?php

namespace app\wap\controller;

use think\facade\Config;

class Error
{
    public function _empty()
    {
        return view('error/404');
    }

    /**
     * 后台关闭站点提示
     * @return \think\response\Redirect|\think\response\View
     */
    public function webClose()
    {
        if ((is_array(zf_cache('login_info.open_start_time')) && max(zf_cache('login_info.open_start_time'))) || (is_array(zf_cache('login_info.open_out_time')) && max(zf_cache('login_info.open_out_time')))) {
            $startTime = (float)zf_cache('login_info.open_start_time.' . date('w'));
            $outTime = (float)zf_cache('login_info.open_out_time.' . date('w'));
            if ($startTime < date('H') && $outTime >= date('H') && zf_cache('login_info.web_kg') === true) {
                return redirect('/');
            }
        } elseif (zf_cache('login_info.web_kg') === true) {
            return redirect('/');
        }
        return view('error/web_close', [
            'msg' => Config::get('login_info.web_close_content')
        ]);
    }
}