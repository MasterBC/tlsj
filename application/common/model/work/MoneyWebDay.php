<?php

namespace app\common\model\work;

use think\Model;
use think\helper\Time;
use app\common\model\product\UsersProduct;
use app\common\model\money\UsersMoney;
use app\common\model\Users;
use app\common\server\bonus\Server;

class MoneyWebDay extends Model
{

    protected $name = 'money_web_day';

    /**
     * 获取今天的数据
     */
    public static function getWebdayMoney()
    {

        if (date('H') < 12) {
            return self::whereBetween('add_time', Time::Theday())->find();
        } else {
            return self::whereBetween('add_time', Time::yesterday())->find();
        }
    }

    /**
     * 用户每天的 分红龙分红
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function autoUserBonus()
    {
        // 获取昨日后台录入的分红数据
        $bonusList = self::whereBetween('add_time', Time::yesterday())->find();
        // 如果存在
        if ($bonusList) {
            // 则提取全网所有的分红龙数据
            $userProductList = UsersProduct::where('product_id', 45)->where('status', 1)->field('user_id,id')->select();
            // 算出全网已存在的分红龙数量
            $numProduct = count($userProductList);
            // 计算每只龙的收益 元/只
            $money = ($bonusList['zuo_money'] * 0.2) / $numProduct;
            // 取得所有 有分红龙的用户id
            $userIds = get_arr_column($userProductList, 'user_id');
            // 再取得他们的上下级关系 和 账号
            $userList = Users::whereIn('user_id', $userIds)->column('tjr_path,account', 'user_id');
            $bonusServer = new Server();
            // 循环每个分红龙
            foreach ($userProductList as $v) {
                // 取得这个龙的所有人信息
                $userInfo = $userList[$v['user_id']] ?? [];
                // 如果找不到龙的主人
                if (empty($userInfo)) {
                    // 那就下一个
                    continue;
                }
                // 如果每个龙的收益不为0
                if ($money > 0) {
                    // 根据每个龙的收益记录应该分得得收益
                    $bonusServer->recommendedAward($money, $userInfo);
                    // 发放收益
                    (new UsersMoney())->amountChange($v['user_id'], 3, $money, 178, $v['id'] . '收入', [
                        'come_uid' => $v['user_id']
                    ]);
                }
            }
            // 根据每个龙的收益,进行上下级关系的推荐奖金结算
            $bonusServer->clear();
            // 分红数据继续入库保存
            $bonusList->zuo_levle_money = $money;
            $bonusList->totao_level = $numProduct;
            $bonusList->total_money = self::where('id', '>', 0)->sum('zuo_money');
            // 剩余分红龙的数量 = 配置文件设置的总分红龙数量 - 全网已存在的分红龙数量
            $bonusList->stay_level = intval(zf_cache('security_info.web_totao_level')) - $numProduct;
            $bonusList->save();
        }
    }

}
