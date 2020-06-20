<?php

namespace app\common\server;

use org\ImgCompress;
use think\Exception;
use think\facade\Config;
use think\facade\Env;
use think\facade\Log;
use think\facade\Request;

class Upload
{
    //上传大小限制 单位(字节)
    public $uploadSize = [
        'image' => 3145728, // 图片
        'video' => 52428800 // 视频
    ];

    public $uploadDir = '';
    public $uploadDirName = '';

    /**
     * Upload constructor.
     *
     * @param string $dir 目录
     */
    public function __construct($dir = 'home')
    {
        $this->uploadDirName = 'uploads/' . $dir . '/';
        if (Config::get('oss.oss_upload') === true) {
            $this->uploadDir = Env::get('ROOT_PATH') . 'oss/' . Config::get('oss.oss_root_app_path') . '/' . $this->uploadDirName;
        } else {
            $this->uploadDir = $this->uploadDirName;
        }
    }

    /**
     * 根据文件上传文件
     *
     * @param string $field 上传的图片字段
     * @return array
     * @throws Exception
     */
    public function uploadImageFile($field)
    {
        $error = error_get_last();
        if (!empty($error)) {
            throw new Exception($error['message']);
        }
        $file = Request::file($field);

        $uploadFileInfo = $file->getInfo();
        if ($this->checkHex($uploadFileInfo['tmp_name']) === false) {
            return ['code' => -1, 'msg' => '上传非法图片'];
        }
        $uploadDir = $this->uploadDir;

        $info = $file->validate(['size' => $this->uploadSize['image'], 'ext' => 'jpg,png,gif,jpeg'])->move($uploadDir);

        if ($info) {
            $fileSrc = (str_replace('\\', '/', $info->getSaveName()));
            $localFileSrc = $uploadDir . $fileSrc;

            // 图片压缩
            (new ImgCompress($localFileSrc, 1))->compressImg($localFileSrc);
            if (Config::get('oss.oss_upload') === true) {
                $fileName = '/' . $this->uploadDirName . $fileSrc;
                try {
                    $fileName = $this->uploadToOss($fileName, $localFileSrc, $uploadFileInfo['type']);
                } catch (\Exception $e) {
                    Log::write('图片同步oss失败: ' . $e->getMessage(), 'error');
                    return [
                        'code' => -1,
                        'msg' => '上传失败'
                    ];
                }
            } else {
                $fileName = $localFileSrc;
            }
            return [
                'code' => 1,
                'msg' => '上传成功',
                'data' => [
                    'src' => $fileName
                ]
            ];
        } else {
            // 上传失败获取错误信息
            return ['code' => -1, 'msg' => $file->getError()];
        }
    }

    /**
     * 根据文件上传视频
     *
     * @param string $field 上传的图片字段
     * @return array
     * @throws Exception
     */
    public function uploadVideoFile($field)
    {
        $error = error_get_last();
        if (!empty($error)) {
            throw new Exception($error['message']);
        }
        $file = Request::file($field);
        $uploadFileInfo = $file->getInfo();

        $uploadDir = $this->uploadDir;
        $info = $file->validate(['size' => $this->uploadSize['video'], 'ext' => 'mp4,mkv'])->move($uploadDir);

        if ($info) {
            $fileSrc = (str_replace('\\', '/', $info->getSaveName()));
            $localFileSrc = $uploadDir . $fileSrc;

            if (Config::get('oss.oss_upload') === true) {
                $fileName = '/' . $this->uploadDirName . $fileSrc;
                try {
                    $fileName = $this->uploadToOss($fileName, $localFileSrc, $uploadFileInfo['type']);
                } catch (\Exception $e) {
                    Log::write('视频同步oss失败: ' . $e->getMessage(), 'error');
                    return [
                        'code' => -1,
                        'msg' => '上传失败'
                    ];
                }
            } else {
                $fileName = $localFileSrc;
            }
            return [
                'code' => 1,
                'msg' => '上传成功',
                'data' => [
                    'src' => $fileName
                ]
            ];
        } else {
            // 上传失败获取错误信息
            return ['code' => -1, 'msg' => $file->getError()];
        }
    }

    /**
     * 通过base64上传图片
     *
     * @param string $base64 base64字符串
     * @return array
     */
    public function uploadImageBase64($base64)
    {
        $base64Image = str_replace(' ', '+', $base64); //post的数据里面，加号会被替换为空格，需要重新替换回来，如果不是post的数据，则注释掉这一行

        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64Image, $result)) {
            $imgLen = strlen($base64Image);
            $fileSize = ($imgLen - ($imgLen / 8) * 2);
            if ($fileSize > $this->uploadSize['image']) {
                return ['code' => -1, 'msg' => '图片太大'];
            }

            $imageName = md5(uniqid()) . '.' . $result[2];

            $uploadDir = $this->uploadDir . date('Ymd') . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $localFileSrc = $imageFile = $uploadDir . $imageName;

            //服务器文件存储路径
            if (file_put_contents($imageFile, base64_decode(str_replace($result[1], '', $base64Image)))) {
                if ($this->checkHex($imageFile) === false) {
                    unlink($imageFile);
                    return ['code' => -1, 'msg' => '上传非法图片'];
                }
                if ($this->isImage($imageFile) === false) {
                    return ['code' => -1, 'msg' => '此图片已损坏'];
                }
                // 图片压缩
                (new ImgCompress($localFileSrc, 1))->compressImg($localFileSrc);
                if (Config::get('oss.oss_upload') === true) {
                    $fileName = '/' . $this->uploadDirName . date('Ymd') . '/' . $imageName;
                    try {
                        $fileName = $this->uploadToOss($fileName, $localFileSrc);
                    } catch (\Exception $e) {
                        Log::write('图片同步oss失败: ' . $e->getMessage(), 'error');
                        return ['code' => -1, 'msg' => '上传失败'];
                    }
                } else {
                    $fileName = $localFileSrc;
                }
                return ['code' => 1, 'msg' => '上传成功', 'data' => ['src' => $fileName]];
            } else {
                return ['code' => -1, 'msg' => '上传失败'];
            }
        } else {
            return ['code' => -1, 'msg' => '数据解析失败'];
        }
    }

    /**
     * 检测是否是图片
     *
     * @param string $filename 图片地址
     * @return bool|int
     */
    private function isImage($filename)
    {
        try {
            $types = '.gif|.jpeg|.png|.bmp'; //定义检查的图片类型
            if (file_exists($filename)) {
                if (($info = @getimagesize($filename))) {
                    return false;
                }

                $ext = image_type_to_extension($info['2']);
                return stripos($types, $ext);
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 16进制检测
     *
     * @param string $fileSrc 文件地址
     * @return bool
     */
    private function checkHex($fileSrc)
    {
        $resource = fopen($fileSrc, 'rb');
        $fileSize = filesize($fileSrc);
        fseek($resource, 0);
        //把文件指针移到文件的开头
        if ($fileSize > 512) { // 若文件大于521B文件取头和尾
            $hexCode = bin2hex(fread($resource, 512));
            fseek($resource, $fileSize - 512);
            //把文件指针移到文件尾部
            $hexCode .= bin2hex(fread($resource, 512));
        } else { // 取全部
            $hexCode = bin2hex(fread($resource, $fileSize));
        }
        fclose($resource);
        /* 匹配16进制中的 <% ( ) %> */
        /* 匹配16进制中的 <? ( ) ?> */
        /* 匹配16进制中的 <script | /script> 大小写亦可*/

        /* 核心  整个类检测木马脚本的核心在这里  通过匹配十六进制代码检测是否存在木马脚本*/

        if (preg_match("/(3c25.*?28.*?29.*?253e)|(3c3f.*?28.*?29.*?3f3e)|(3C534352495054)|(2F5343524950543E)|(3C736372697074)|(2F7363726970743E)/is", $hexCode))
            return false;
        return true;
    }

    /**
     * 上传至oss
     *
     * @param string $fileName oss文件名
     * @param string $localFileSrc 本地文件路径
     * @param string $fileType 文件类型
     * @return string oss路径
     * @throws \Exception
     */
    public function uploadToOss($fileName, $localFileSrc, $fileType = '')
    {
        $fileName = Config::get('oss.oss_root_app_path') . $fileName;
        OSS::publicUploadToPublic($fileName, $localFileSrc, [
            'ContentType' => $fileType ? $fileType : mime_content_type($localFileSrc)
        ]);
        return $fileName;
    }

    /**
     * 删除文件
     *
     * @param string $fileName 文件路径
     */
    public static function deleteFile($fileName)
    {
        if (Config::get('oss.oss_upload') === true) {
            OSS::publicDeleteObject(Config::get('oss.public.oss_root_path'), $fileName);
        } else {
            if (file_exists($fileName)) {
                unlink($fileName);
            }
        }
    }

    /**
     * 上传本地目录至oss
     *
     * @param string $dir 本地目录
     * @param string $ignoreDir 上传忽略路径
     * @author gkdos
     * 2019-08-08 17:43:47
     */
    public function uploadDirToOss($dir, $ignoreDir)
    {
        echo "正在索引文件";
        echo PHP_EOL;
        $files = $this->indexFile($dir);
        echo "共发现" . count($files) . "个文件, 预计上传耗时" . (count($files) * 0.1) . '秒';
        echo PHP_EOL;
        echo "开始上传";
        echo PHP_EOL;
        $startTime = microtime(true);
        foreach ($files as $v) {
            $localFileSrc = $v;
            $fileName = str_replace($ignoreDir, '', $localFileSrc);
            try {
                OSS::publicUploadToPublic($fileName, $localFileSrc, [
                    'ContentType' => mime_content_type($localFileSrc)
                ]);
                echo $localFileSrc . '上传成功';
                echo PHP_EOL;
            } catch (\Exception $e) {
                echo $localFileSrc . '上传失败' . $e->getMessage();
                echo PHP_EOL;
            }
        }
        echo "上传耗时: " . (round(microtime($startTime) - $startTime, 2)) . "秒";
        echo PHP_EOL;
    }

    public function exportOssFile($folder, $localDir)
    {
        echo "正在导出oss上的" . $folder . "文件夹";
        echo PHP_EOL;
        $ossFileList = OSS::getAllObjectKeyWithPrefix(Config::get('oss.public.oss_root_path'), $folder);
        echo "检测到" . (count($ossFileList)) . '个文件';
        echo PHP_EOL;
        echo "正在导出";
        echo PHP_EOL;
        $startTime = microtime(true);
        foreach ($ossFileList as $k => $v) {
            try {
                $dir = dirname($localDir . $v);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                copy(Config::get('oss.oss_url') . $v, $localDir . $v);
                echo $v . '导出成功';
                echo PHP_EOL;
            } catch (\Exception $e) {
                echo $v . '导出失败' . $e->getMessage();
                echo PHP_EOL;
            }
        }
        echo "导出耗时: " . (round(microtime($startTime) - $startTime, 2)) . "秒";
        echo PHP_EOL;
    }


    /**
     * 索引文件
     *
     * @param string $path 路径
     * @param array $files 文件
     * @return array
     * @author gkdos
     * 2019-08-08 17:36:25
     */
    private function indexFile($path, $files = [])
    {
        //判断是否是文件夹
        if (is_dir($path)) {
            //判断是否打开成功
            if ($handle = opendir($path)) {
                //读取文件
                while ($file = readdir($handle)) {
                    //判断是否是文件夹
                    if ($file != '.' && $file != '..') {
                        if (is_dir($path . '/' . $file)) {
                            $files = array_merge($files, $this->indexFile($path . '/' . $file));
                        } else {
                            $file = $path . '/' . $file;
                            $files[] = $file;
                        }
                    }
                }

                //关闭文件夹
                closedir($handle);
            }
        }

        return $files;
    }
}
