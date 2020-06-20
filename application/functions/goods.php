<?php

/**
 * 生成商品缩略图
 * @param string $imgUrl 图片地址
 * @param string $key 图片key
 * @param int $goodsId 商品id
 * @param float $width 宽度
 * @param float $height 高度
 * @return string 缩略图地址
 */
function get_sub_image($imgUrl, $key, $goodsId, $width, $height)
{
    try {
        $dir = \app\common\model\goods\Goods::$thumbDir . $goodsId . '/';
        // 判断是否是oss
        if (zf_cache('oss.oss_upload') === true) {
            $ossDir = \think\facade\Config::get('oss.oss_root_app_path') . '/' . $dir;
            $imgUrl = \think\facade\Env::get('ROOT_PATH') . 'oss/' . $imgUrl;
            $path = \think\facade\Env::get('ROOT_PATH') . 'oss/' . $ossDir;
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $goodsThumbName = "goods_sub_thumb_{$goodsId}_{$key}_{$width}_{$height}";
            //这个缩略图 已经生成过这个比例的图片就直接返回了
            if (file_exists($path . $goodsThumbName . '.jpg'))
                return $ossDir . $goodsThumbName . '.jpg';
            if (file_exists($path . $goodsThumbName . '.jpeg'))
                return $ossDir . $goodsThumbName . '.jpeg';
            if (file_exists($path . $goodsThumbName . '.gif'))
                return $ossDir . $goodsThumbName . '.gif';
            if (file_exists($path . $goodsThumbName . '.png'))
                return $ossDir . $goodsThumbName . '.png';

            $originalImg = $imgUrl; //相对路径
            if (!file_exists($originalImg))
                return '';

            $image = \think\Image::open($originalImg);

            $goodsThumbName = $goodsThumbName . '.' . $image->type();

            $image->thumb($width, $height, 6)->save($path . $goodsThumbName, NULL, 100); //按照原图的比例生成一个最大为$width*$height的缩略图并保存

            try {
                $uploadServer = new \app\common\server\Upload();
                $uploadServer->uploadToOss('/'.$dir . $goodsThumbName, $path . $goodsThumbName);
            } catch (\Exception $e) {
                \think\facade\Log::write('商品缩略图同步oss失败: ' . $e->getMessage(), 'error');

                return $imgUrl;
            }
            return ltrim($dir . $goodsThumbName, '/');
        } else {
            $path = $dir;
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $goodsThumbName = "goods_sub_thumb_{$goodsId}_{$key}_{$width}_{$height}";
            //这个缩略图 已经生成过这个比例的图片就直接返回了
            if (file_exists($path . $goodsThumbName . '.jpg'))
                return $path . $goodsThumbName . '.jpg';
            if (file_exists($path . $goodsThumbName . '.jpeg'))
                return $path . $goodsThumbName . '.jpeg';
            if (file_exists($path . $goodsThumbName . '.gif'))
                return $path . $goodsThumbName . '.gif';
            if (file_exists($path . $goodsThumbName . '.png'))
                return $path . $goodsThumbName . '.png';

            $originalImg = $imgUrl; //相对路径
            if (!file_exists($originalImg))
                return '';

            $image = \think\Image::open($originalImg);

            $goodsThumbName = $goodsThumbName . '.' . $image->type();

            $image->thumb($width, $height, 6)->save($path . $goodsThumbName, NULL, 100); //按照原图的比例生成一个最大为$width*$height的缩略图并保存

            return $path . $goodsThumbName;
        }

    } catch (\Exception $e) {
        \think\facade\Log::write('商品缩略图生成失败: ' . $e->getMessage(), 'error');
        return $imgUrl;
    }
}