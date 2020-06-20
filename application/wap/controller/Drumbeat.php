<?php

namespace app\wap\controller;

use app\common\model\money\MoneyLog;
use think\Db;
use think\Request;
use think\facade\Session;
use api\bxm\Server;
use app\common\model\Users;
use app\common\model\Ad;
use app\common\model\money\UsersMoney;
use app\wap\logic\ProductLogic;

class Drumbeat extends Base
{

    /**
     * 广告通知
     *
     * @param Request $request
     * @author gkdos
     * 2019-09-25 14:07:00
     */
    public function notify(Request $request, Server $server, ProductLogic $productLogic)
    {
        $data = $server->getNotifyData();
        if ($data['uid'] > 0) {
            $uid = intval($data['uid']);
            $userInfo = Users::where('user_id', $uid)->find();
            $productInfo = $productLogic->getUserBuyMaxProductInfo($userInfo);
            if ($productInfo['amount'] > 0) {
                $userMoneyModel = new UsersMoney();
                $userMoneyModel->amountChange($uid, 2, $productInfo['amount'], 177, '互动广告赠送', [
                    'come_uid' => $uid
                ]);
            }
        }
        file_put_contents(__DIR__ . '/drumbeat_notify.log', 'date: ' . date('Y-m-d H:i:s') . PHP_EOL .
            'data: ' . print_r($data, true) . PHP_EOL .
            '-------------------------------------------' . PHP_EOL
            , FILE_APPEND);

        return 'success';
    }

    /**
     * 浮标广告
     *
     * @param Request $request
     * @param Server $server
     * @return [type]
     * @author gkdos
     * 2019-09-23T19:53:48+0800
     */
    public function buoyAd(Request $request, Server $server)
    {
        if (empty($this->user)) {
            $this->error('请先登陆');
        }
        $res = $server->getBuoyAd($this->user['user_id']);

        $adInfo = (array)($res['returnValue'] ?? []);
        if (empty($adInfo)) {
            $this->error('加载失败，请刷新重试');
        }

        return redirect($adInfo['redirectUrl']);
    }

    /**
     * 查看视频信息
     *
     * @param Request $request
     * @param Server $server
     * @author gkdos
     * 2019-09-20T15:28:36+0800
     */
    public function videoAd(Request $request, Server $server)
    {
        if (empty($this->user)) {
            $this->error('请先登陆');
        }
        $type = $request->param('type', '', 'trim');
        $device = $this->get_device_type();
        $res = Ad::where(['state'=>1, 'type'=>$device])->limit(1)->orderRaw("rand()")->select();
        if (!count($res)){
            $this->error($device?'安卓':'IOS'.'类型广告尚未添加');
        }
        if (empty($res[0])) {
            $this->error('加载失败，请刷新重试');
        }
        Session::set('user_check_video_start', time());
        Session::set('user_video_id', $res[0]['id']);
        return view('drumbeat/vide_ad_info', [
            'adInfo' => $res[0],
            'type' => $type,
            'device_type' => $device,
        ]);
    }
    
    private function get_device_type()
    {
        $agent = strtolower(\think\facade\Request::header('user-agent'));
        $type = 1;
        if(strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
            $type = 0;
        }
        if(strpos($agent, 'android')) {
            $type = 1;
        }
        return $type;
    }

    /**
     * 视频播放结束
     *
     * @param Request $request
     * @author gkdos
     * 2019-09-20T17:15:09+0800
     */
    public function videoAdPlayEnd(Request $request)
    {
        if (!Session::has('user_check_video_start')) {
            $this->error('请先观看视频');
        }
        $startTime = Session::get('user_check_video_start');
        $id = Session::get('user_video_id');
        $adInfo = Ad::find($id);
        if (time() - $startTime < intval($adInfo['times'])) {
            $this->error('观看时长不够');
        }
        if ($this->user['video_num'] <= 0) {
            $this->error('今日广告次数已达上限', 'User/index');
        }
        $updateData = [
            'video_num' => Db::raw('video_num-1')
        ];
        $type = $request->param('type');
        if ($type == 'balance_bz') {
            //$productInfo = (new ProductLogic())->getUserBuyMaxProductInfo($this->user);
            $money_2 = MoneyLog::where(['uid'=>$this->user_id, 'mid'=>2, 'is_type'=>174])->whereTime('edit_time','-2 hours')->sum('money');
            (new UsersMoney())->amountChange($this->user_id, 2, $money_2, 177, '广告奖励', [
                'come_uid' => $this->user_id
            ]);
            $updateData['last_video_income'] = $money_2;
        } elseif ($type == 'offline_income') {
            (new UsersMoney())->amountChange($this->user_id, 2, $this->user['last_offline_income'] * 2, 177, '离线收益翻倍', [
                'come_uid' => $this->user_id
            ]);
            $updateData['last_video_income'] = $this->user['last_offline_income'] * 2;
        } elseif ($type == 'coin') {
        	$money_c = (new ProductLogic())->getUserBuyMaxProductInfo($this->user);
        	$money_c = $money_c['amount'];
            (new UsersMoney())->amountChange($this->user_id, 2, $money_c, 177, '观看广告奖励', [
                'come_uid' => $this->user_id
            ]);
            $updateData['last_video_income'] = $money_c;
        }
        Users::where('user_id', $this->user_id)->update($updateData);
        Session::delete('user_check_video_start');
        Session::delete('user_video_id');
        return redirect('User/index');
    }
}
