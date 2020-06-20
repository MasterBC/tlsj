<?php

namespace app\common\controller;

use think\captcha\Captcha;
use think\facade\Request;

class Index
{

    /**
     * 验证码
     * @return \think\Response
     */
    public function verify()
    {
        $type = Request::get('type') ? Request::get('type') : 'login';

        $config = [
            'fontSize' => 35,
            'useCurve' => FALSE,
            'useNoise' => FALSE,
            'length' => 4,
            'fontttf'  => '5.ttf',
            'codeSet' => '0123456789'
        ];

        $captcha = new Captcha($config);

        return $captcha->entry($type);
    }
}