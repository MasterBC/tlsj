<?php
declare (strict_types=1);

namespace app\api\behavior;

use think\Response;

class AddAccessLog
{
    /**
     * 添加访问记录
     * @param Response $response
     */
    public function run(Response $response)
    {
//        (new \app\api\model\AccessLog())->addLog(json_decode($response->getContent(), true));
        (new \app\api\service\AccessLog())->addLog(json_decode($response->getContent(), true));
    }
}
