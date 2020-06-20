<?php

namespace app\common\controller;

use think\Controller;

class Common extends Controller
{

    protected function initialize()
    {

        self::filterParams();
    }

    /**
     * 空操作跳转到404
     * @return mixed
     */
    public function _empty()
    {
        return view('error/404');
    }

    /**
     * 参数过滤
     */
    private static function filterParams()
    {
        filter_params($_GET);
        filter_params($_POST);
        filter_params($_REQUEST);
    }
}
