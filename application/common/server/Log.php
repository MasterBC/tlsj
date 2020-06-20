<?php

namespace app\common\server;

use think\facade\Log as ThinkLog;

class Log
{
    /**
     * 自定义异常日志
     *
     * @param string $msg 错误信息
     * @param \Exception $exception 异常类
     * @return void
     */
    public static function exceptionWrite($msg, $exception)
    {
        $errorMsg = $msg . PHP_EOL;
        $errorMsg .= 'file: ' . $exception->getFile() . ' 第' . $exception->getLine() . '行' . PHP_EOL;
        $errorMsg .= 'error_msg: ' . $exception->getMessage() . PHP_EOL;

        $trace = 'trace: ' . PHP_EOL;
        $exceptionTrace = $exception->getTrace();
        $exceptionTrace = array_reverse($exceptionTrace);
        foreach ($exceptionTrace as $v) {
            $line = $v['line'] ?? 0;
            $file = $v['file'] ?? '';
            if ($file != '') {
                if ($line > 0) {
                    $trace .= 'file: ' . $v['file'] . ' 第' . $line . '行' . PHP_EOL;
                } else {
                    $trace .= 'file: ' . $v['file'] . PHP_EOL;
                }
            }
        }
        $errorMsg .= $trace;

        ThinkLog::write($errorMsg, 'error');
    }
}
