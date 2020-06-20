<?php

namespace app\wap\controller;

class Test extends Base
{
//    我的钱包 s
    public function mywallet()
    {
        return view('test/mywallet');
    }
//    我的钱包 e

//    零钱记录 s
    public function loosechang()
    {
        return view('test/loosechang');
    }
//    零钱记录 e

//   提现说明 s
    public function alinstructions()
    {
        return view('test/alinstructions');
    }
    //   提现说明 e


    //  分红详情 s
    public function participro()
    {
        return view('test/participro');
    }
    //   分红详情 e

    //  隐私 s
    public function privacyys()
    {
        return view('test/privacyys');
    }
    //   隐私 e


    //  设置社交信息 s
    public function socialcan()
    {
        return view('test/socialcan');
    }
    //   设置社交信息 e   turntable

    //  设置社交信息 s
    public function turntable()
    {
        return view('test/turntable');
    }
    //   设置社交信息 e   advertising.html


    //  视频广告 s
    public function advertising()
    {
        return view('test/advertising');
    }
    //   视频广告 e



    //  收益说明
    public function benefitsthat()
    {
        return view('test/benefitsthat');
    }
}