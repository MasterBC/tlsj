<?php

namespace app\api\controller;

use think\Controller;

class Index extends Controller
{

    public function document()
    {
        return view('index/document');
    }
}