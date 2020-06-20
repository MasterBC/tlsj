<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

//新闻中心
//Route::get('news','wap/news/index');

// 验证码
Route::get('qrCode', 'Index/qrCode');
Route::get('verify', 'Common/Index/verify');
Route::post('uploadImg', 'Common/Upload/uploadImage');
Route::post('uploadVideo', 'Common/Upload/uploadVideo');
Route::post('sendSms/[:type]', 'SmsCode/sendSmsRegCode')->pattern(['type' => '\d+']);
Route::post('sendSmtp', 'SmsCode/sendEmailRegCode');
/*Route::group('api', function(){
//    Route::get(':version', 'api/:version.Apiver/read');

    Route::group(':version/user', function () {
        Route::get('getuserinfo', 'api/user/getuserinfo');
    })->prefix('user/');

})->prefix('api/');*/


//Route::get('hello/:name', 'index/hello');
//Route::get('/', 'index/index');

// 后台访问
/*Route::group('zfadmin', function(){
    Route::get('/', 'index/index');

    Route::group('index', function () {
        Route::get('/', 'admin/index/index');
        Route::get('index', 'admin/index/index');
        Route::get('welcome', 'admin/index/welcome');
    })->prefix('index/');

    Route::group('user', function () {
        Route::get('/', 'admin/user/index');
    })->prefix('user/');

})->prefix('admin/');*/

// 404
//Route::miss('Error/index');

return [

];
