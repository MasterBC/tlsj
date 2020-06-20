<?php

namespace app\common\logic;

use app\common\model\block\BlockCrowd;
use app\common\model\block\BlockCrowdUser;
use app\common\model\block\UsersBlock;
use app\common\model\money\UsersMoney;
use think\facade\Request;
use app\facade\Password;

class BlockCrowdLogic
{
    /**
     * 购买众筹操作
     * @param $userInfo
     * @param $bid
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doPurchase($userInfo, $bid)
    {
        $mid = 1;
        $id = Request::param('id', '', 'intval');
        $num = Request::param('num', '', 'intval');
        $secpwd = Request::param('secpwd');
        // 判断二级密码是否正确
        if (!Password::checkPayPassword($secpwd, $userInfo)) {
            exception('二级密码错误');
        }

        // 查出已经购买可多少
        $blockCrowd = BlockCrowd::where('id', $id)->field('id,web_taotal,user_taotal,yg_num,now_price')->find();
        $blockCrowdAllNum = BlockCrowdUser::where('cid', $id)->sum('num');

        if (($num + $blockCrowdAllNum) > $blockCrowd['web_taotal']) {
            exception('众筹总额不足');
        }

        // 判断自己还能购买多少
        $blockCrowdUserNum = BlockCrowdUser::where('cid', $id)->where('uid', $userInfo['user_id'])->sum('num');

        if ($num > $blockCrowd['user_taotal']) {
            exception('每个ID限购'.$blockCrowd['user_taotal']);
        }

        if ($blockCrowdUserNum >= $blockCrowd['user_taotal']) {
            exception('你的购买已上限');
        }

        if (($num + $blockCrowdUserNum) > $blockCrowd['user_taotal']) {
            exception('你最多还可以购买' . ($blockCrowd['user_taotal'] - $blockCrowdUserNum));
        }

        // 获取用户余额
        $userMoney = get_money_amount($userInfo['user_id'], $mid, 1);

        if (($blockCrowd['now_price'] * $num) > $userMoney) {
            exception(get_money_info($mid)['name_cn'].'余额不足');
        }

        $data['cid'] = $id;
        $data['bid'] = $bid;
        $data['uid'] = $userInfo['user_id'];
        $data['zf_time'] = time();
        $data['num'] = $num;
        $data['price'] = $blockCrowd['now_price'];
        $data['total'] = ($blockCrowd['now_price'] * $num);
        $data['status'] = 9;

        BlockCrowdUser::insert($data);
        BlockCrowd::where('id', $id)->update(['yg_num' => $blockCrowd['yg_num'] + $num]);

        $userBlockModel = new UsersBlock();
        $usersMoneyModel = new UsersMoney();

        // 减去扣除的奖金金额
        $userBlockModel->amountChange($userInfo['user_id'], $bid, $num, 109, '购买众筹');
        $usersMoneyModel->amountChange($userInfo['user_id'], $bid, '-'.($blockCrowd['now_price'] * $num), 106, '购买众筹');
        return true;
    }
}