<?php

namespace org;

use app\common\server\OSS;
use think\Db;
use think\Exception;
use think\facade\App;
use think\facade\Config;
use think\facade\Env;
use app\common\server\Log;
use think\response\Download;

class DbManage
{

    // aes加密密钥
    const AES_KEY = 'tSaxrM3bpDIJjNVx';

    // 备份文件描述文件夹
    private $backDescriptionDir;

    // 数据库备份文件目录
    public $backDir;

    // 备份文件保存的份数
    public $backupReservations = 10;

    // 临时目录
    public $tempDir;

    public function __construct()
    {
        $this->backDir = Env::get('ROOT_PATH') . 'database_backup/';

        $this->tempDir = Env::get('ROOT_PATH') . 'temp/' . md5(microtime(true)) . '/';

        $this->backDescriptionDir = $this->backDir . 'back_description/';

        $this->checkDir();
    }

    /**
     * 检测目录
     * @throws Exception
     */
    public function checkDir()
    {
        try {
            if (!is_dir($this->backDescriptionDir)) {
                mkdir($this->backDescriptionDir, 0777, true);
            }
        } catch (\Exception $e) {
            Log::exceptionWrite('数据库备份目录创建失败', $e);
            throw new Exception('备份目录创建失败');
        }
    }

    /**
     * 获取数据表信息
     * @param $tableName
     * @return array|mixed
     */
    public function getTableInfo($tableName)
    {
        $schema = Config::get('database.database') . '.' . $tableName;
        $cacheFile = App::getRuntimePath() . 'schema' . DIRECTORY_SEPARATOR . $schema . '.php';
        if (is_file($cacheFile)) {
            $info = include $cacheFile;
        } else {
            $result = Db::query('SHOW COLUMNS FROM ' . $tableName);
            $info = [];

            foreach ($result as $key => $val) {
                $val = array_change_key_case($val);
                $info[$val['field']] = [
                    'name' => $val['field'],
                    'type' => $val['type'],
                    'notnull' => (bool)('' === $val['null']), // not null is empty, null is yes
                    'default' => $val['default'],
                    'primary' => (strtolower($val['key']) == 'pri'),
                    'autoinc' => (strtolower($val['extra']) == 'auto_increment'),
                ];
            }
        }
        return $info;
    }

    /**
     * 获取主键
     * @param $tableInfo
     * @return string
     */
    public function getPk($tableInfo)
    {
        foreach ($tableInfo as $v) {
            if ($v['primary'] === true) {
                return $v['name'];
            }
        }
        return '';
    }

    /**
     * 导入数据库文件
     *
     * @param string $file 要导入的文件名
     * @param int $type 文件类型
     * @param $file
     * @throws Exception
     */
    public function import($fileName, $type = 1)
    {
        if ($type == 2) {
            $fileName = $this->decrypt($fileName);
        }
        $file = $this->backDir . $fileName;
        if (!file_exists($file)) {
            throw new Exception('此文件不存在');
        }
        // dump(pathinfo($file));
        $type = pathinfo($file)['extension'] ?? '';
        switch ($type) {
            case 'gz':
                $files = $this->gzDecompression($file);
                foreach ($files as $k => $v) {
                    $files[$k] = $this->tempDir . $v;
                }
                break;
            case 'zip':
                $files = $this->zipDecompression($file);
                foreach ($files as $k => $v) {
                    $files[$k] = $this->tempDir . $v;
                }
                break;
            case 'sql':
                $files = [
                    $file
                ];
                break;
            default:
                throw new Exception('不支持此文件');
                break;
        }
        $this->back('', $fileName . '还原前备份');
        $config = Config::get('database.');
        // $config['params'] = [
        //     \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        //     \PDO::ATTR_PERSISTENT => true
        // ];
        // $config[constant('PDO::ATTR_PERSISTENT')] = true;
        // Db::init($config);
        $dsn = "{$config['type']}:host={$config['hostname']};dbname={$config['database']}";
        $dbh = new \PDO($dsn, $config['username'], $config['password']); //初始化一个PDO对象
        foreach ($files as $v) {
            $type = pathinfo($v)['extension'] ?? '';
            if ($type == 'sql') {
                $str = file_get_contents($v);
                // $sqlComments = '@(([\'"]).*?[^\\\]\2)|((?:\#|--).*?$|/\*(?:[^/*]|/(?!\*)|\*(?!/)|(?R))*\*\/)\s*|(?<=;)\s+@ms';

                // $sqlData = explode(";" . PHP_EOL, $str);
                $sqlData = preg_split("/;[\r\n,\n]+/s", $str);

                foreach ($sqlData as $sql) {
                    // $sql = trim(preg_replace($sqlComments, '$1', $sql));
                    if (strlen($sql) > 2) {
                        $sql .= ';';
                        // file_put_contents(__DIR__.'/test.sql', $sql.PHP_EOL, FILE_APPEND);
                        // dump($sql);
                        // $startTime = microtime(true);
                        $dbh->exec($sql);
                        // echo 'Query OK, ' . $res . ' rows affected (' . (number_format(microtime(true) - $startTime, 2)) . ' sec)' . PHP_EOL;
                        // dump($res);
                    }
                }
                // dump($arr);
            }
        }

        return true;
    }

    /**
     * 删除目录
     *
     * @param string $dir 要删除的目录
     * @return boolean
     */
    private function delDir($dir)
    {
        if (!$handle = @opendir($dir)) {
            return false;
        }
        while (false !== ($file = readdir($handle))) {
            if ($file !== "." && $file !== "..") {
                $file = $dir . '/' . $file;
                if (is_dir($file)) {
                    $this->delDir($file);
                } else {
                    @unlink($file);
                }
            }
        }
        @rmdir($dir);

        return true;
    }

    /**
     * aes数据加密
     *
     * @param string $input 要加密的数据
     * @return string
     */
    private function encrypt($input)
    {
        $data = openssl_encrypt($input, 'AES-128-ECB', self::AES_KEY, OPENSSL_RAW_DATA);
        $data = base64_encode($data);
        return $data;
    }

    /**
     * aes解密
     *
     * @param string $sStr aes的加密数据
     * @return string
     */
    private function decrypt($sStr)
    {
        $decrypted = openssl_decrypt(base64_decode($sStr), 'AES-128-ECB', self::AES_KEY, OPENSSL_RAW_DATA);
        return $decrypted;
    }

    /**
     * 获取备份文件列表
     *
     * @return array
     */
    public function getBackupList()
    {
        $filePath = opendir($this->backDir);

        $fileList = [];
        while (false !== ($filename = readdir($filePath))) {
            if ($filename != "." && $filename != ".." && $filename) {
                if (!is_dir($this->backDir . $filename)) {
                    $fileList[] = $filename;
                }
            }
        }

        foreach ($fileList as $k => $v) {
            $fileDescription = $this->getFileDescription($v);
            $fileList[$k] = [
                'fileName' => $v,
                'fileTime' => $this->getFileTime($v),
                'fileSize' => $this->getFileSize($v),
                'operationFileName' => $this->encrypt($v),
                'description' => $fileDescription
            ];
        }

        //        $count = count($fileList);
        //        for ($i = 0; $i < $count; $i++) {
        //            for ($j = $count - 1; $j > $i; $j--) {
        //                if ($fileList[$j]['fileTime'] > $fileList[$j - 1]['fileTime']) {
        //                    $temp = $fileList[$j];
        //                    $fileList[$j] = $fileList[$j - 1];
        //                    $fileList[$j - 1] = $temp;
        //                }
        //            }
        //        }

        return $fileList;
    }

    /**
     * 下载文件
     *
     * @param string $operationFileName 操作文件名
     * @return Download|boolean
     */
    public function downloadFile($operationFileName)
    {
        $fileName = $this->decrypt($operationFileName);
        if ($fileName === false) {
            return false;
        }

        if (file_exists($this->backDir . $fileName)) {
            try {
                $download = new \think\response\Download($this->backDir . $fileName);

                $description = $this->getFileDescription($fileName);
                $description = strip_tags($description);
                $description = str_replace(["\n\r", "\r", "\n"], '_', $description);

                return $download->name($description . ' - ' . $fileName);
            } catch (\Exception $e) {
                Log::exceptionWrite('下载文件失败', $e);

                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 删除备份文件
     *
     * @param string $fileName 要删除的文件名
     * @param int $type 文件名类型 2需要解密
     * @return boolean
     */
    public function delBackFile($fileName, $type = 1)
    {
        if ($type == 2) {
            $fileName = $this->decrypt($fileName);
        }
        $file = $this->backDir . $fileName;
        if (file_exists($file)) {
            $this->delFileDescription($fileName);
            unlink($file);

            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取文件的修改时间
     *
     * @param string $file 文件名称
     * @return string
     */
    private function getFileTime($file)
    {
        $file = filemtime($this->backDir . $file);
        $time = date("Y-m-d H:i:s", $file);
        return $time;
    }

    /**
     * 获取文件大小
     *
     * @param string $file 文件名称
     * @return string
     */
    private function getFileSize($file)
    {
        $perms = stat($this->backDir . $file);
        $size = $perms['size'];
        // 单位自动转换函数
        $kb = 1024;         // Kilobyte
        $mb = 1024 * $kb;   // Megabyte
        $gb = 1024 * $mb;   // Gigabyte
        $tb = 1024 * $gb;   // Terabyte

        if ($size < $kb) {
            return $size . " B";
        } else if ($size < $mb) {
            return round($size / $kb, 2) . " KB";
        } else if ($size < $gb) {
            return round($size / $mb, 2) . " MB";
        } else if ($size < $tb) {
            return round($size / $gb, 2) . " GB";
        } else {
            return round($size / $tb, 2) . " TB";
        }
    }

    /**
     * 设置文件描述
     *
     * @param string $fileName 备份文件名
     * @param string $fileDescription 文件描述
     * @return void
     */
    private function setFileDescription($fileName, $fileDescription)
    {
        file_put_contents($this->backDescriptionDir . $fileName . '.desc.info', $fileDescription);
    }

    /**
     * 获取文件描述
     *
     * @param string $fileName 文件名
     * @return string
     */
    private function getFileDescription($fileName)
    {
        try {
            $description = file_get_contents($this->backDescriptionDir . $fileName . '.desc.info');

            return $description;
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * 删除文件描述
     *
     * @param string $fileName 文件名称
     */
    private function delFileDescription($fileName)
    {
        $file = $this->backDescriptionDir . $fileName . '.desc.info';
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * 备份数据库
     *
     * @param string $file 备份的文件名
     * @param string $description 文件描述
     * @return boolean
     */
    public function back($file = '', $description = '')
    {
        try {

            if ($file) {
                $backFileName = $file . ".sql";
            } else {
                $backFileName = date('Ymd_His') . ".sql";
            }
            if ($description == '') {
                $description = date('Y-m-d H:i:s') . '备份';
            }
            $toFileName = $this->backDir . $backFileName;

            $fp = fopen($toFileName, "w");
            // 数据库中有哪些表
            $tables = Db::query('SHOW TABLES ');
            $tableList = array();
            foreach ($tables as $v) {
                foreach ($v as $vv) {
                    $tableList[] = $vv;
                }
            }
            $limitNum = 10000;
            $listPageNum = 500;

            // 导出数据表
            foreach ($tableList as $val) {
                $res = Db::query('show create table ' . $val);
                $structureData = '';
                foreach ($res as $v) {
                    $structureData = $v['Create Table'];
                }
                $info = "--" . PHP_EOL;
                $info .= "-- 表的结构 `{$val}`" . PHP_EOL;
                $info .= "--" . PHP_EOL . PHP_EOL;
                $info .= "DROP TABLE IF EXISTS `" . $val . "`;" . PHP_EOL;
                $sqlStr = $info . $structureData . ";" . PHP_EOL;
                //追加到文件
                fwrite($fp, $sqlStr);


                $info = PHP_EOL . "--" . PHP_EOL;
                $info .= "-- 转存表中的数据 `{$val}`" . PHP_EOL;
                $info .= "--" . PHP_EOL . PHP_EOL;
                fwrite($fp, $info);
                $info = "LOCK TABLES `{$val}` WRITE;" . PHP_EOL;
                fwrite($fp, $info);
                $info = "";

                $listNum = $limitNum;
                $page = 0;
                $field = 'INSERT INTO `' . $val . '` ';
                $tableColumn = $this->getTableInfo($val);
                $arr = [];
                foreach ($tableColumn as $k => $v) {
                    $arr[] = '`' . $k . '`';
                }
                $field .= '(' . implode(',', $arr) . ') ';;
                $pk = $this->getPk($tableColumn);

                $currentVal = 0;
                while ($listNum == $limitNum) {
                    if ($pk != '') {
                        $list = Db::query('select * from ' . $val . ' where ' . $pk . ' > ' . $currentVal . '  limit ' . $limitNum);
                        $pkVals = get_arr_column($list, $pk);
                        $pkVals[] = 0;
                        $currentVal = max($pkVals);
                    } else {
                        $list = Db::query('select * from ' . $val . ' limit ' . ($limitNum * $page) . ',' . $limitNum);
                    }
                    $page++;
                    $listNum = count($list);
                    if ($listNum > 0) {

                        $listPage = ceil($listNum / $listPageNum);
                        $listPage = $listPage <= 0 ? 1 : $listPage;
                        for ($i = 1; $i <= $listPage; $i++) {
                            $info .= $field . 'VALUES ';
                            foreach ($list as $k => $v) {
                                if ($k >= ($i - 1) * $listPageNum && $k < $i * $listPageNum) {
                                    $info .= '(';
                                    foreach ($tableColumn as $k2 => $v2) {
                                        $fieldVal = $v[$k2];
                                        $fieldType = $v2['type'] ?? '';
                                        if (strstr($fieldType, 'char') != false || strstr($fieldType, 'text') != false || strstr($fieldType, 'date') != false) {
                                            $fieldVal = str_replace(["\r\n", "\r", "\n", '"'], ['\n', '\n', '\n', '\"'], var_export($fieldVal, true));
                                        } else {
                                            if (is_numeric($v2['default'])) {
                                                $defaultVal = $v2['default'];
                                            } else {
                                                $defaultVal = var_export($v2['default'], true);
                                            }
                                            $fieldVal = ((empty($fieldVal) && !is_numeric($fieldVal)) ? $defaultVal : $fieldVal);
                                        }
                                        $info .= $fieldVal . ",";
                                    }
                                    $info = rtrim($info, ',');
                                    $info .= '),';
                                }
                            }
                            $info = rtrim($info, ',');
                            $info .= ";" . PHP_EOL;
                            fwrite($fp, $info);
                            $info = "";
                        }
                    }
                }
                fwrite($fp, "UNLOCK TABLES;" . PHP_EOL . PHP_EOL);
            }

            fclose($fp);

            $toFileName = $this->compressedIntoZip($toFileName);
            //        $this->uploadToOss($backFileName, $toFileName);

            $arr = explode('/', $toFileName);
            $fileName = $arr[count($arr) - 1];
            $this->setFileDescription($fileName, $description);
            $this->deleteRedundantBackupFiles();

            return true;
        } catch (\Exception $e) {
            Log::exceptionWrite('数据库备份失败', $e);
            return false;
        }
    }

    /**
     * zip解压缩
     *
     * @param string $filename 压缩的文件名
     * @return array
     * @throws Exception
     */
    private function zipDecompression($filename)
    {
        $dir = $this->tempDir;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true); //创建目录保存解压内容
        }

        $files = [];
        if (file_exists($filename)) {
            $resource = zip_open($filename);
            while ($zip = zip_read($resource)) {
                if (zip_entry_open($resource, $zip)) {
                    $file_content = zip_entry_name($zip); //获得文件名，mac压缩成zip，解压需要过滤资源库隐藏文件
                    $file_name = substr($file_content, strrpos($file_content, '/') + 1);
                    if (!is_dir($file_name) && $file_name) {
                        $save_path = $dir . '/' . $file_name;
                        if (file_exists($save_path)) {
                            throw new Exception('文件夹内已存在文件' . $file_name);
                        } else {
                            $files[] = $file_name;
                            $file_size = zip_entry_filesize($zip);
                            $file = zip_entry_read($zip, $file_size);
                            file_put_contents($save_path, $file);
                            zip_entry_close($zip);
                        }
                    }
                }
            }

            zip_close($resource);
        }

        return $files;
    }

    /**
     * gz解压缩
     *
     * @param string $filename 压缩的文件名
     * @return array
     */
    private function gzDecompression($filename)
    {
        $dir = $this->tempDir;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true); //创建目录保存解压内容
        }
        $buffer_size = 4096; // read 4kb at a time
        $file_name = 'temp.sql';
        $out_file_name = $dir . '/' . $file_name;
        // $out_file_name = str_replace('.gz', '', $filename);
        $file = gzopen($filename, 'rb');
        $out_file = fopen($out_file_name, 'wb');
        $str = '';
        while (!gzeof($file)) {
            fwrite($out_file, gzread($file, $buffer_size));
        }
        fclose($out_file);
        gzclose($file);

        return [$file_name];
    }

    /**
     * 将备份文件压缩成gz
     * @param string $fileName 文件名
     * @param int $level 压缩级别
     * @return string 压缩后的文件名
     */
    private function compressedIntoGz($fileName, $level = 9)
    {
        try {
            $gzName = $fileName . '.gz';
            $mode = 'wb' . $level;
            $error = false;
            if ($fp_out = gzopen($gzName, $mode)) {
                if ($fp_in = fopen($fileName, 'rb')) {
                    while (!feof($fp_in))
                        gzwrite($fp_out, fread($fp_in, 1024 * 512));
                    fclose($fp_in);
                } else {
                    $error = true;
                }
                gzclose($fp_out);
            } else {
                $error = true;
            }
            if ($error)
                return false;

            unlink($fileName); // 压缩成功后删除原文件
            $fileName = $gzName;
        } catch (\Exception $e) {
            Log::exceptionWrite('数据库文件压缩gz失败', $e);
        }
        return $fileName;
    }

    /**
     * 将备份文件压缩成zip
     * @param string $fileName 文件名
     * @return string 压缩后的文件名
     */
    private function compressedIntoZip($fileName)
    {
        if (extension_loaded('zip')) {
            try {
                $zipName = $fileName . '.zip';
                $zip = new \ZipArchive;
                if ($zip->open($zipName, \ZIPARCHIVE::OVERWRITE | \ZIPARCHIVE::CREATE) !== TRUE) {
                    throw new Exception('无法打开文件，或者文件创建失败');
                }
                $zip->addFile($fileName, basename($fileName));
                $zip->close(); //关闭

                if (!file_exists($zipName)) {
                    throw new Exception('无法找到文件');
                }
                unlink($fileName); // 压缩成功后删除原文件
                $fileName = $zipName;
            } catch (\Exception $e) {
                Log::exceptionWrite('数据库文件压缩zip失败', $e);
            }
        }

        return $fileName;
    }

    /**
     * 上传至oss
     * @param string $fileName oss文件名
     * @param string $localFileSrc 本地文件路径
     * @return string oss路径
     */
    public function uploadToOss($fileName, $localFileSrc)
    {
        $fileName = Config::get('oss.oss_root_app_path') . $fileName;
        OSS::publicUploadToPrivate($fileName, $localFileSrc);
        return $fileName;
    }

    /**
     * 删除多余的备份文件
     * @return int 删除的文件数量
     */
    private function deleteRedundantBackupFiles()
    {
        $files = [];
        $i = 0;
        $backFileNum = count(scandir($this->backDir)) - 2;
        if ($backFileNum > $this->backupReservations) {
            if ($dh = opendir($this->backDir)) {
                while (false !== ($file = readdir($dh))) {
                    if ($file != "." && $file != "..") {
                        $fullPath = $this->backDir . "/" . $file;
                        if (!is_dir($fullPath)) {
                            $fileDate = filemtime($fullPath);
                            $files[$fileDate] = $file;
                        }
                    }
                }
            }
            closedir($dh);
            ksort($files);
            $delNum = $backFileNum - $this->backupReservations;
            if ($delNum > 0) {
                foreach ($files as $k => $v) {
                    try {
                        if ($i < $delNum) {
                            $this->delBackFile($v);
                            //                            unlink($this->backDir . '/' . $v);
                        }
                        $i++;
                    } catch (\Exception $e) {
                        Log::exceptionWrite('删除备份文件' . $v . '失败 ', $e);
                    }
                }
            }
        }

        return $i;
    }

    public function __destruct()
    {
        if (is_dir($this->tempDir)) {
            $this->delDir($this->tempDir);
        }
    }
}
