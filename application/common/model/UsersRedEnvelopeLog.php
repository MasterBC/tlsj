<?php

namespace app\common\model;

use think\facade\Config;
use think\helper\Time;

class UsersRedEnvelopeLog extends Common
{
    protected $name = 'users_red_envelope_log';

    // 状态
    public static $status = [
        1 => '待领取',
        2 => '已领取'
    ];

    // 红包类型
    public static $redEnvelopeType = [
        'random' => '随机红包',
        'upgrade' => '升级红包',
        'sign' => '签到红包'
    ];

    /**
     * 添加红包领取记录
     *
     * @param string $type 类型
     * @param int $userId 会员id
     * @param float $amount 红包金额
     * @param array $otherData 其他信息
     * @param int $status 状态
     * @param int $productId 产品id
     * @author gkdos
     * 2019-09-24T10:33:47+0800
     */
    public static function addLog($type, $userId, $amount, $otherData = [], $status = 1, $productId = 0)
    {
        $data = [
            'red_envelope_type' => $type,
            'uid' => $userId,
            'amount' => $amount,
            'other_data' => json_encode($otherData, JSON_UNESCAPED_UNICODE),
            'add_time' => time(),
            'status' => $status,
            'product_id' => $productId
        ];

        return self::insertGetId($data);
    }

    /**
     * 用户随机红包状态
     *
     * @param int $userId 会员id
     * @return int 1 待领取 2已领取 3不能领取
     * @author gkdos
     * 2019-09-24T11:19:28+0800
     */
    public static function getUserRandomRedEnvelopeStatus($userId)
    {
        $count = self::where('uid', $userId)->where('red_envelope_type', 'random')->count();
        if ($count == 0) {
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * 用户今日签到红包状态
     *
     * @param int $userId 会员id
     * @return void
     * @author gkdos
     */
    public static function getUserTodaySignRedEnvelopeStatus($userId)
    {
        $count = self::where('uid', $userId)->where('red_envelope_type', 'sign')->whereBetween('add_time', Time::today())->count();
        if ($count == 0) {
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * 发放升级红包
     *
     * @param int $userId 会员id
     * @param int $productId 产品id
     * @return int|string
     * @author gkdos
     * 2019-09-30 17:25:23
     */
    public static function issueAnUpgradeRedEnvelope($userId, $productId)
    {
        $count = self::where('uid', $userId)->where('product_id', $productId)->count();
        if ($count == 0) {
            $levelConfig = explode('-', Config::get('security_info.upgrade_red_envelope'));
            if (in_array($productId, $levelConfig)) {
                $arr = explode('-', Config::get('security_info.upgrade_rand_arr_money'));
                $money = $arr[array_rand($arr)] ?? 0;
                if ($money > 0) {
                    return self::addLog('upgrade', $userId, $money, ['product_id' => $productId], 1, $productId);
                }
            }
        }
        return 0;
    }

    /**
     * 获取会员待领取的升级红包
     *
     * @param int $userId 会员id
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author gkdos
     * 2019-09-30 17:36:14
     */
    public static function getUserPendingUpgradeRedEnvelopeInfo($userId)
    {
        return self::where('uid', $userId)->where('status', 1)->order('id', 'asc')->find();
    }
}
