<?php

namespace app\wap\controller;

use think\facade\Log;
use app\common\model\Users;
use app\common\model\work\MoneyWebDay;

class Task extends Base
{
    public function h12(){
        try {
            Log::error('开始执行每日分红');
            $startTime = microtime(true);
            (new MoneyWebDay())->autoUserBonus();
            Log::error('----It takes ' . round(microtime(true) - $startTime, 3) . ' seconds');
            return 'success';
        } catch (\Exception $e) {
            Log::error('每日分红执行失败');
            return 'error';
        }
    }

    public function h0(){
        try {
            Log::error('开始赠送转盘劵');
            $startTime = microtime(true);
            (new Users())->autoDayGiveTurnNum();
            Log::error('----It takes ' . round(microtime(true) - $startTime, 3) . ' seconds');

            Log::error('重置摇一摇次数');
            $startTime = microtime(true);
            (new Users())->autoDayGiveShakeNum();
            Log::error('----It takes ' . round(microtime(true) - $startTime, 3) . ' seconds');
            return 'success';
        }catch (\Exception $e) {
            Log::error('有0点型子任务执行失败');
            return 'error';
        }
    }

    public function h20(){
        try {
            Log::error('重置视频次数');
            $startTime = microtime(true);
            (new Users())->autoDayGiveVideoNum();
            Log::error('----It takes ' . round(microtime(true) - $startTime, 3) . ' seconds');
            return 'success';
        }catch (\Exception $e) {
            Log::error('视频重置任务执行失败');
            return 'error';
        }
    }
}