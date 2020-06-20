<?php

namespace app\common\logic;

use think\facade\Config;
use think\facade\Env;
use think\facade\Request;
use think\Console;

class WebsiteLogic
{
    public $fileName; // 文件名


    /**
     * 获取配置
     * @param string $key 键值
     * @return mixed
     */
    public function getData($key = '')
    {
        if ($key) {
            return Config::get($key);
        } else {
            return Config::get($this->fileName . '.');
        }
    }

    /**
     * 保存参数
     * @param array $params 要保存的参数
     * @param string $fileDesc 文件说明
     * @return bool
     */
    public function setConfig($params, $fileDesc = '')
    {

        if (isset($params['description'])) {
            $description = $params['description'];
            unset($params['description']);
            $paramStr = $this->formatParams($params, $description);
        } else {
            $paramStr = $this->formatParams($params);
        }

        $date = date('Y-m-d H:i:s');
        $ip = Request::ip();
        $adminId = (new \app\common\model\AdminUser())->getAdminUserId();
        $oldConfig = $this->formatParams(Config::get($this->fileName . '.'));

        $str = <<<php
<?php

// +----------------------------------------------------------------------
// | {$fileDesc}
// | 修改管理员id: {$adminId}
// | 修改日期: {$date}
// | ip: {$ip}
// +----------------------------------------------------------------------
/**
 * 原配置
   {$oldConfig}
 */

return {$paramStr}
php;

        $fileName = Env::get('CONFIG_PATH') . $this->fileName . '.php';
        $res = file_put_contents($fileName, $str);

//        Console::call('optimize:config');

        return $res;
    }

    /**
     * 格式化参数
     * @param array $param 参数
     * @param array $description 参数描述
     * @param int $level 层数
     * @return string 格式化好的字符
     */
    private function formatParams($param, $description = [], $level = 1)
    {
        $keys = array_keys($param);
        $longestKeyLength = 0;
        foreach ($keys as $v) {
            $longestKeyLength = max($longestKeyLength, strlen($v));
        }
        $str = "[\n";
        foreach ($param as $k => $v) {
            // 空格
            $space = str_repeat("    ", $level);
            if (is_array($v)) {
                $str .= $space . "'{$k}' => " . $this->formatParams($v, $description, $level + 1);
            } else {
                // 用于填充的空格
                $fillInSpaces = str_repeat(" ", $longestKeyLength - strlen($k));
                // 参数说明
                $str .= $space . (isset($description[$k]) ? '// ' . $description[$k] : '');
                $str .= "\n";
                if (is_numeric($v) || in_array($v, ['true', 'false'])) {
                    $str .= $space . "'{$k}'{$fillInSpaces} => {$v},";
                } else {
                    $str .= $space . "'{$k}'{$fillInSpaces} => " . var_export($v, true) . ",";
                }
            }
            $str .= "\n";
        }
        $str .= str_repeat("    ", $level - 1) . "]" . ($level == 1 ? ';' : ',');

        return $str;
    }
}