<?php

namespace app\common\controller;

use think\facade\Log;
use think\facade\Request;

class Upload
{
    /**
     * 上传图片
     *
     * @return array|\think\response\Json
     */
    public function uploadImage()
    {
        $field = (Request::param('field') ? Request::param('field') : 'file');
        $dir = (Request::param('dir') ? Request::param('dir') : 'home');

        try {
            $upload = new \app\common\server\Upload($dir);
            $res = $upload->uploadImageFile($field);
            return $res;
        } catch (\Exception $e) {
            Log::write('上传失败： ' . $e->getMessage(), 'error');
            return json()->data(['code' => -1, 'msg' => '上传失败']);
        }
    }

    /**
     * 上传视频
     *
     * @return array|\think\response\Json
     */
    public function uploadVideo()
    {
        $field = (Request::param('field') ? Request::param('field') : 'file');
        $dir = (Request::param('dir') ? Request::param('dir') : 'home');
        try {
            $upload = new \app\common\server\Upload($dir);
            $res = $upload->uploadVideoFile($field);
            return $res;
        } catch (\Exception $e) {
            Log::write('上传失败： ' . $e->getMessage(), 'error');
            return json()->data(['code' => -1, 'msg' => '上传失败']);
        }
    }
}