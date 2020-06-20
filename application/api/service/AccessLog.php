<?php

namespace app\api\service;

use org\AesSecurity;
use think\facade\Request;

class AccessLog
{

    private $encryptKey = 'pBnGRyhdQleDkmF6';
    private $logDir = ''; // 日志存放目录

    public function __construct()
    {
        $this->logDir = __DIR__ . '/../access_log/' . date('Ym');

        $this->checkDir();
    }

    /**
     * 检测目录
     * @return bool
     */
    public function checkDir()
    {
        try {
            if (!is_dir($this->logDir)) {
                mkdir($this->logDir, 0777, true);
            }
        } catch (\Exception $e) {
            file_put_contents(__DIR__ . '/error.log', date('Y-m-d H:i:s') . "---访问日志目录创建失败：目录：" . $this->logDir . ', 错误：' . $e->getMessage() . PHP_EOL, FILE_APPEND);
            try {
                if (is_dir($this->logDir)) {
                    return true;
                } else {
                    return false;
                }
            } catch (\Exception $e) {
                file_put_contents(__DIR__ . '/error.log', date('Y-m-d H:i:s') . "---访问日志目录验证失败：目录：" . $this->logDir . ', 错误：' . $e->getMessage() . PHP_EOL, FILE_APPEND);
                return false;
            }
        }
    }

    /**
     * 加密数据
     * @param array $data 要加密的数据
     * @return string 加密后的数据
     */
    public function encryptedData($data)
    {
        return AesSecurity::encrypt(is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data, $this->encryptKey);
    }

    /**
     * 添加访问日志
     * @param array $responseInfo 响应信息
     */
    public function addLog($responseInfo = [])
    {
        try {
            $version = '';
            $arr = explode('.', Request::controller());
            if (count($arr) > 1) {
                $version = $arr[0] ?? '';
                $controller = $arr[1] ?? '';
            } else {
                $controller = $arr[0];
            }
            $action = Request::action();
            $responseInfo['data'] = $this->encryptedData($responseInfo['data'] ?? []);
            $requestInfo = $this->encryptedData(Request::param());
            $headerInfo = Request::header();
            isset($headerInfo['token']) && $headerInfo['token'] = $this->encryptedData($headerInfo['token']);
            $tokenServer = new Token();
            $userInfo = $tokenServer->getUserInfo();
            if ($userInfo) {
                $userInfo = $this->encryptedData($userInfo);
            }
            $data = [
                'add_time' => date('H:i:s'),
                'user_info' => $userInfo,
                'ip' => get_ip(),
                'version' => $version,
                'controller' => $controller,
                'action' => $action,
                'method' => Request::method(),
                'request_info' => $requestInfo,
                'header_info' => $headerInfo,
                'response_info' => $responseInfo
            ];
            file_put_contents($this->logDir . '/' . date('d') . '.log', json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
        } catch (\Exception $e) {
            file_put_contents(__DIR__ . '/error.log', date('Y-m-d H:i:s') . "---添加API访问记录失败: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        }
    }
}