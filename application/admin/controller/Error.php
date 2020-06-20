<?php

namespace app\admin\controller;

class Error
{
    public function permissionDenied()
    {
        return view('error/401');
    }

    public function index()
    {
        return view('error/404');
    }

    public function _empty()
    {
        return view('error/404');
    }
}