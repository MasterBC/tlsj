<?php
declare (strict_types=1);

namespace app\common\behavior;

use app\common\model\Users;
use think\facade\Cache;
use think\facade\Request;
use think\facade\Session;
use think\Response;

class AntiCcAttackEnd
{
    /**
     * 添加访问记录
     */
    public function run()
    {
    }
}
