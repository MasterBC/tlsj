<?php

namespace app\wap\controller;

use app\common\model\branch\UsersBranch;
use app\common\model\branch\UsersBranchYj;
use app\common\model\grade\Level;
use app\common\model\Users;
use think\facade\Config;
use think\Request;

class Team extends Base
{

    public function teamIndex()
    {
        // 获取推广地址并加密
        $tgUrl = url("reg/index", ["code" => $this->user['reg_code']], false, true);
        $this->assign('tgUrl', $tgUrl);
        return view('team/qr_code');
    }

    public function qrCode(Request $request)
    {
        $url = $request->param('value', url('/', '', true, true), 'base64_decode');
        $url .= '?t=' . time() . rand(111, 999);
        $size = 4;
        \QRcode::png($url, false, QR_ECLEVEL_H, $size, 0, false, 0xFFFFFF, 0x000000);
        die;
        $user = $this->user;
        $UsersDataModel = new UsersData();
        $userDataInfo = $UsersDataModel->getTjrInfo($user['data_id'], 'head');
        $logo = $userData['head'] ? $userData['head'] : ''; //准备好的logo图片

        $logo = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $logo;

//        $logo = 'Public/upload/User/userHead/5bbc2ba9a7575.jpg';
        $QR = 'qrcode.png'; //已经生成的原始二维码图
        if ($logo !== FALSE) {
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR); //二维码图片宽度
            $QR_height = imagesy($QR); //二维码图片高度
            $logo_width = imagesx($logo); //logo图片宽度
            $logo_height = imagesy($logo); //logo图片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        }
        header('Content-Type:image/png');
        imagepng($QR);

        unlink('qrcode.png');
    }

    /**
     * 朋友圈分享海报
     */
    public function tjrFriendAdd()
    {
        $adData = [];
        $adList = Config::get('poster_info.poster_index');
        if (!empty($adList)) {
            $adData['list'] = $adList;
            $adData['first'] = current($adList);
            $adData['end'] = end($adList);
        }

        $this->assign('adData', $adData);
        $this->assign('get_img_domain', get_img_domain());
        $tgUrl = url("reg/index", ["code" => $this->user['reg_code']], false, true);
        $this->assign('tgUrl', $tgUrl);
        return view('team/tjr_frien');
    }

    /**
     * 下载海报
     *
     * @return void
     */
    public function downloadPoster(Request $request)
    {
        $image = $request->param('url');
        $image = urldecode($image);

        $config = Config::get('poster_info.poster_index');
        $images = get_arr_column($config, 'ad_img');
        if (!in_array($image, $images)) {
            $this->error('操作失败');
        }
        // if (!isset($config[$index])) {
        //     $this->error('操作失败');
        // }
        $ossConfig = Config::get('oss.');
        if (!empty($ossConfig) && $ossConfig['oss_upload'] === true) {
            $image = Env::get('ROOT_PATH') . 'oss/' . $image;
        }
        if (!file_exists($image)) {
            $this->error('操作失败');
        }

        $dir = Env::get('ROOT_PATH') . 'poster/' . $this->user['account'];
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $url = url("reg/index", ["code" => $this->user['reg_code']], false, true);
        $size = 3;
        $qrCodeUrl = $dir . '/qr_code.png';
        \QRcode::png($url, $qrCodeUrl, QR_ECLEVEL_H, $size, 0, false, 0xFFFFFF, 0x000000);


        $image = \think\Image::open($image);
        $content = $this->user['account'] . PHP_EOL . '邀请你加入' . Config::get('web_info.web_name');
        $font = Env::get('ROOT_PATH') . 'msyh.ttf';
        $posterUrl = $dir . '/poster.png';
        $qrCodeInfo = getimagesize($qrCodeUrl);
        // dump($qrCodeInfo);
        // die;
        $image->text($content, $font, 14, '#000000', [$image->width() * 0.1 + $qrCodeInfo[0] + 20, ($image->height() - $qrCodeInfo[1] / 2) - $image->height() * 0.11])
                ->water($qrCodeUrl, [$image->width() * 0.1, ($image->height() - $qrCodeInfo[1]) - $image->height() * 0.11 + 20])
                ->save($posterUrl);
        $download = new \think\response\Download($posterUrl);
        return $download->name('分享海报.png');
    }

    /**
     * 网络图
     * @return \think\response\View
     */
    public function jdNetworktu()
    {
        return view('jd_network_tu');
    }

    /**
     * 获取网络图数据
     * @param Request $request
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getNetwork(Request $request)
    {
        $account = $request->param('account', $this->user['account'], 'trim');
        $account = ($account == '' ? $this->user['account'] : $account);
        $user = Users::where('account', $account)->field('user_id,tjr_path')->find();
        if (empty($user)) {
            return json()->data(['code' => -1, 'msg' => '此会员不存在']);
        }
        $branch = Users::alias('u')->join('UsersBranch ub', 'ub.uid=u.user_id')->where('user_id', $user['user_id'])->field('id,account,activate,nickname,jdr_id,ub.path,level')->find();

        # 限制只能查看自己下面的 s
        $userBranch = UsersBranch::getBranchInfoByUid($this->user_id);
        $count = substr_count($userBranch['path'], ',') + 7;
        $paths = explode(',', $branch['path']);
        if ((!in_array($userBranch['id'], $paths) && $branch['id'] != $userBranch['id'])) {
            return json()->data(['code' => -1, 'msg' => '会员不存在']);
        }
//        unset($branch['path']);
        # 限制只能查看自己下面的 e
        $branch['left_yj'] = UsersBranchYj::countTotalYjByBranchId($branch['id'], 1);
        $branch['right_yj'] = UsersBranchYj::countTotalYjByBranchId($branch['id'], 2);
        $branch['jdrAccount'] = Users::alias('u')->join('UsersBranch ub', 'ub.uid=u.user_id')->where('id', $branch['jdr_id'])->value('u.account');

        $branch['team_num_a'] = UsersBranch::whereLike('path', $branch['path'] . ',' . $branch['id'] . '%')->where(['position' => 1])->count();
        $branch['team_num_b'] = UsersBranch::whereLike('path', $branch['path'] . ',' . $branch['id'] . '%')->where(['position' => 2])->count();

        $level = Level::getLevelInfoById($branch['level']);

        $branch['color'] = $level['color'] ?? '';
        $jdrs = Users::alias('u')->join('UsersBranch ub', 'ub.uid=u.user_id')->where('jdr_id', $branch['id'])->column('position,id,jdr_id,account,activate,nickname,level,u.user_id,ub.path');
        foreach ($jdrs as $k => $v) {
            $level = Level::getLevelInfoById($v['level']);
            $jdrs[$k]['color'] = $level['color'] ?? '';
            $jdrs[$k]['team_num_a'] = UsersBranch::whereLike('path', $v['path'] . ',' . $v['id'] . '%')->where(['position' => 1])->count();
            $jdrs[$k]['team_num_b'] = UsersBranch::whereLike('path', $v['path'] . ',' . $v['id'] . '%')->where(['position' => 2])->count();
        }

        foreach ($jdrs as $k => $v) {
            $jdrs[$k]['list'] = Users::alias('u')->join('UsersBranch ub', 'ub.uid=u.user_id')->where('jdr_id', $v['id'])->column('position,id,jdr_id,account,activate,nickname,level,user_id,ub.path');

            foreach ($jdrs[$k]['list'] as $key => $val) {
                $level = Level::getLevelInfoById($val['level']);
                $jdrs[$k]['list'][$key]['color'] = $level['color'] ?? '';
                $jdrs[$k]['list'][$key]['team_num_a'] = UsersBranch::whereLike('path', $val['path'] . ',' . $val['id'] . '%')->where(['position' => 1])->count();
                $jdrs[$k]['list'][$key]['team_num_b'] = UsersBranch::whereLike('path', $val['path'] . ',' . $val['id'] . '%')->where(['position' => 2])->count();
            }
        }
        $branch['list'] = $jdrs;
        return json()->data($branch);
    }

    /**
     * 我的徒弟
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function firstTjrList(Request $request)
    {
        if ($request->isAjax()) {
            $page = $request->param('p', 0, 'intval');
            $page = max($page, 0);
            $pageSize = 10;
            $teamUserList = Users::where('tjr_path', 'like', $this->user['tjr_path'] . ',' . $this->user['user_id'] . '%')
                    ->where('activate', 1)
                    ->having('(length(`tjr_path`) - length(replace(`tjr_path`,",", "")) - '.(strlen($this->user['tjr_path'])-strlen(str_replace(',','', $this->user['tjr_path']))).') = 1')
                    ->field('account,mobile,level,reg_time,tjr_path,user_id')
                    ->limit($page * $pageSize, $pageSize)
                    ->select();
            return view('team/tjr/first_tjr_ajax', [
                'teamUserList' => $teamUserList
            ]);
        } else {
            return view('team/tjr/first_tjr');
        }
    }

    /**
     * 我的徒孙
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function secondTjrList(Request $request)
    {
        if ($request->isAjax()) {
            $page = $request->param('p', 0, 'intval');
            $page = max($page, 0);
            $pageSize = 10;
            $teamUserList = Users::where('tjr_path', 'like', $this->user['tjr_path'] . ',' . $this->user['user_id'] . '%')
                    ->where('activate', 1)
                    ->having('(length(`tjr_path`) - length(replace(`tjr_path`,",", "")) - '.(strlen($this->user['tjr_path'])-strlen(str_replace(',','', $this->user['tjr_path']))).') = 2')
                    ->field('account,mobile,level,reg_time,tjr_path,user_id')
                    ->limit($page * $pageSize, $pageSize)
                    ->select();
            // $tjrInfo = $usersModel->where('second_tjr', $this->user_id)->limit(($p * $pSize) . ',' . $pSize)->field('account,mobile,level,reg_time')->select();
            return view('team/tjr/second_tjr_ajax', [
                'teamUserList' => $teamUserList
            ]);
        } else {
            return view('team/tjr/second_tjr');
        }
    }

    /**
     * 未激活会员列表
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function notActivateTjrList(Request $request)
    {
        if ($request->isAjax()) {
            $page = $request->param('p', 0, 'intval');
            $page = max($page, 0);
            $pageSize = 10;
            $teamUserList = Users::where('tjr_id', $this->user_id)
                    ->where('activate', '<>', 1)
                    ->field('account,mobile,level,reg_time,user_id')
                    ->limit($page * $pageSize, $pageSize)
                    ->select();
            return view('team/tjr/no_activate_ajax', [
                'teamUserList' => $teamUserList
            ]);
        } else {
            return view('team/tjr/no_activate');
        }
    }

    /**
     * 获取算力
     * @param Request $request
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAjaxUserTjrData(Request $request)
    {
        if ($request->isAjax()) {
            $userOwnedMoney = $usersMoneyModel->getUserOwnedMoney($this->user_id);
            $webDayMoney = $moneyWebDayModel->getWebdayMoney();
            return json(['code' => 1, 'userOwnedMoneyAll' => $userOwnedMoney, 'webDayMoney' => $webDayMoney]);
        }
    }

}
