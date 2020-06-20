<?php

namespace app\api_admin\exception;

use think\exception\Handle;
use think\exception\HttpException;
use think\facade\Log;

class Http extends Handle
{

    public function render(\Exception $e)
    {
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
        }

        if (!isset($statusCode)) {
            $statusCode = 500;
        }

        $result = [
            'code' => $statusCode,
            'msg' => $e->getMessage(),
            'time' => $_SERVER['REQUEST_TIME'],
        ];
//        (new \app\api\model\AccessLog())->addLog($result);
        Log::write($e->getMessage(), 'error');
        return json($result, $statusCode);
    }

}
