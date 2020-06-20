<?php

namespace app\common\logic;

use think\facade\App;
use think\facade\Env;
use think\Console;

class CacheLogic
{

    /**
     * 清除模板缓存
     */
    public function clearTempCache()
    {
        del_dir(App::getRuntimePath() . 'temp');
    }

    /**
     * 清除数据缓存
     */
    public function clearDataCache()
    {
        del_dir(App::getRuntimePath() . 'cache');
    }

    /**
     * 清除数据库缓存
     */
    public function clearDbCache()
    {
        Console::call('optimize:schema');
    }

    /**
     * 清除配置缓存
     */
    public function clearConfigCache()
    {
//        Console::call('optimize:config');
        $this->refushCaceh();
    }

    /**
     * 清除缓存
     */
    public function clearAllCache()
    {
        $this->clearTempCache();
        $this->clearDataCache();
        $this->clearDbCache();
        $this->clearConfigCache();
    }

    /**
     * 刷新缓存
     */
    public function refushCaceh()
    {
        // 自定义函数缓存
        $path = Env::get('APP_PATH') . '/functions/';
        $content = '<?php' . PHP_EOL;
        $files = [];
        $handler = opendir($path);
        while (($filename = readdir($handler)) !== false) {//务必使用!==，防止目录下出现类似文件名“0”等情况
            $explodeFileName = explode('.', $filename);
            if (count($explodeFileName) > 1) {
                if ($filename != "." && $filename != ".." && $filename != 'init.php' && $filename != 'load.php' && $explodeFileName[1] == 'php') {
                    $files[] = $filename;
                }
            }
        }
        foreach ($files as $value) {
            $content .= substr(php_strip_whitespace($path . $value), 6);
        }

        file_put_contents($path . 'init.php', $content);
    }
}