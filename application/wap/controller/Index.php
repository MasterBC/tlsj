<?php

namespace app\wap\controller;

use app\common\model\UsersData;
use think\Controller;
use think\Request;

class Index extends Controller
{
    /**
     * 跳转
     */
    public function index()
    {
        return redirect('/User/index');
    }

    public function stay()
    {
        return view('index/stay');
    }

    /**
     * 生成二维码
     *
     * @param Request $request
     * @author gkdos
     * 2019-08-17 11:22:52
     */
    public function qrCode(Request $request)
    {
        $url = $request->param('value', url('/', '', true, true), 'base64_decode');
        $size = 4;
        return \QRcode::png($url, false, QR_ECLEVEL_H, $size, 0, false, 0xFFFFFF, 0x000000);
    }

    public function test()
    {
        blockLogType();
    }
}
