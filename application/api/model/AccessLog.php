<?php

namespace app\api\model;

use app\api\service\Token;
use org\AesSecurity;
use think\Db;
use think\facade\Config;
use think\facade\Log;
use think\facade\Request;
use think\Model;

class AccessLog extends Model
{

    private $encryptKey = 'pBnGRyhdQleDkmF6';

    protected $name = 'api_access_log';

    public function initialize()
    {
        parent::initialize();
//        $this->checkTableIsExist();
    }

    /**
     * 加密数据
     * @param array $data 要加密的数据
     * @return string 加密后的数据
     */
    public function encryptedData($data)
    {
        return AesSecurity::encrypt(json_encode($data), $this->encryptKey);
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
            $tokenServer = new Token();
            $userInfo = $tokenServer->getUserInfo();
            if ($userInfo) {
                $userInfo = $this->encryptedData($userInfo);
            }
            $this->insert([
                'user_info' => $userInfo,
                'ip' => get_ip(),
                'version' => $version,
                'controller' => $controller,
                'action' => $action,
                'method' => Request::method(),
                'add_time' => date('Y-m-d H:i:s'),
                'request_info' => $requestInfo,
                'header_info' => json_encode($headerInfo, JSON_UNESCAPED_UNICODE),
                'response_info' => json_encode($responseInfo, JSON_UNESCAPED_UNICODE),
            ]);
        } catch (\Exception $e) {
            Log::write('添加API访问记录失败: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * 检测表是否存在
     */
    public function checkTableIsExist()
    {
        $tableName = Config::get('database.prefix') . $this->name;

        $isTable = Db::query('SHOW TABLES LIKE ' . "'" . $tableName . "'");
        // 不存在就创建
        if (!$isTable) {
            $createSql = <<<sql
CREATE TABLE `{$tableName}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_info` longtext DEFAULT NULL,
  `version` varchar(10) DEFAULT NULL,
  `controller` varchar(20) DEFAULT NULL,
  `action` varchar(20) DEFAULT NULL,
  `method` varchar(6) DEFAULT NULL,
  `request_info` longtext DEFAULT NULL,
  `header_info` longtext DEFAULT NULL,
  `response_info` longtext DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `add_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
sql;
            Db::execute($createSql);
        }
    }
}