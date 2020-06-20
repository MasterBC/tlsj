<?php

namespace org;

use think\Db;
use think\Exception;
use think\facade\Config;
use think\facade\Env;
use think\facade\Log;

class DbManage
{
    // 数据库备份文件目录
    public $backDir;

    // 备份文件保存的份数
    public $backupReservations = 10;

    public function __construct()
    {
        $this->backDir = Env::get('ROOT_PATH') . 'database_backup/';

        $this->checkDir();
    }

    /**
     * 检测目录
     * @throws Exception
     */
    public function checkDir()
    {
        try {
            if (!is_dir($this->backDir)) {
                mkdir($this->backDir, 0777, true);
            }
        } catch (\Exception $e) {
            Log::write('数据库备份目录创建失败: ' . $e->getMessage(), 'error');
            throw new Exception('备份目录创建失败');
        }
    }

    public function back()
    {
        $toFileName = $this->backDir . Config::get('database.database') . '_' . date('Ymd_His') . ".sql";

        $fp = fopen($toFileName,"w");
        //数据库中有哪些表
        $tables = Db::query('SHOW TABLES ');
        $tableList = array();
        foreach ($tables as $v) {
            foreach ($v as $vv) {
                $tableList[] = $vv;
            }
        }

        //将每个表的表结构导出到文件
        foreach ($tableList as $val) {
            $res = Db::query('show create table ' . $val);
            $structureData = '';
            foreach ($res as $v) {
                $structureData = $v['Create Table'];
            }
            $info = "--\r\n";
            $info .= "-- 表的结构 `{$val}`\r\n";
            $info .= "--\r\n\r\n";
            $info .= "DROP TABLE IF EXISTS `" . $val . "`;\r\n";
            $sqlStr = $info . $structureData . ";\r\n\r\n";
            //追加到文件
            fwrite($fp, $sqlStr);
        }

        $limitNum = 5000;
        $listPageNum = 500;

        // 导出数据
        foreach ($tableList as $val) {
            $info = "\r\n--\r\n";
            $info .= "-- 转存表中的数据 `{$val}`\r\n";
            $info .= "--\r\n\r\n";
            fwrite($fp, $info);
            $info = "";

            $listNum = $limitNum;
            $page = 0;
            while ($listNum == $limitNum) {
                $list = Db::table($val)->limit($limitNum * $page, $limitNum)->select();
                $page++;
                $listNum = count($list);
                if ($listNum > 0) {
                    $field = 'INSERT INTO `' . $val . '` (';
                    foreach ($list[0] as $k => $v) {
                        $field .= '`' . $k . '`,';
                    }
                    $field = rtrim($field, ',');

                    $listPage = ceil($listNum / $listPageNum);
                    $listPage = $listPage <= 0 ? 1 : $listPage;
                    for ($i = 1; $i <= $listPage; $i++) {
                        $info .= $field . ') VALUES';
                        foreach ($list as $k => $v) {
                            if ($k >= ($i - 1) * $listPageNum && $k < $i * $listPageNum) {
                                $info .= '(';
                                foreach ($v as $k2 => $v2) {
                                    $fieldVal = str_replace('"', '\"', $v[$k2]);
                                    $fieldVal = str_replace(["\r\n", "\r", "\n"], '\n', $fieldVal);
                                    $info .= "'" . $fieldVal . "',";
                                }
                                $info = rtrim($info, ',');
                                $info .= '),';
                            }
                        }
                        $info = rtrim($info, ',');
                        $info .= ";\r\n";
                        fwrite($fp, $info);
                        $info = "";
                    }
                }
            }
        }
        fclose($fp);

        $this->compressedIntoZip($toFileName);

        $this->deleteRedundantBackupFiles();
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
                $zip->close();//关闭

                if (!file_exists($zipName)) {
                    throw new Exception('无法找到文件');
                }
                unlink($fileName); // 压缩成功后删除原文件
                $fileName = $zipName;
            } catch (\Exception $e) {
                Log::write('数据库文件压缩zip失败： ' . $e->getMessage(), 'error');
            }
        }

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
                            unlink($this->backDir . '/' . $v);
                        }
                        $i++;
                    } catch (\Exception $e) {
                        Log::write('删除备份文件' . $v . '失败： ' . $e->getMessage(), 'error');
                    }
                }
            }
        }

        return $i;
    }
}