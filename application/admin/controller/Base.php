<?php

namespace app\admin\controller;

use app\common\controller\Common;

class Base extends Common
{
    protected $middleware = [
        'checkAdminLogin',
        'checkAuth'
    ];

    public function initialize()
    {
        parent::initialize();
    }
}
