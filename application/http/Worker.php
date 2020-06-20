<?php

namespace app\http;

use app\common\model\money\UsersMoney;
use app\common\model\product\Product;
use app\common\model\product\UsersProduct;
use app\common\model\Users;
use think\worker\Server;
use Workerman\Lib\Timer;

class Worker extends Server
{
    public static $user = [];
    public static $works = [];
    protected $socket = 'http://0.0.0.0:7373';

    public function onMessage($connection, $data)
    {
        $workId = $connection->id;
        echo "onMessage work_id:" . $workId . PHP_EOL;

        $data = json_decode($data, true);

        if ($data['type'] != 'login') {
            if ($this->checkSession($workId, $data['session_id']) === false) {
                return $connection->send(json_encode([
                    'type' => 'logout'
                ]));
            }
        }
        switch ($data['type']) {
            case 'login':
                $userId = $data['uid'];
                if (isset(self::$works[$userId])) {
                    return self::$works[$userId]->send(json_encode([
                        'type' => 'logout'
                    ]));
                }
                $userInfo = Users::where('user_id', $userId)->find();
                if ($userInfo) {
                    if ($userInfo['session_id'] != $data['session_id']) {
                        return $connection->send(json_encode([
                            'type' => 'logout'
                        ]));
                    }
                    $offlineTime = time() - ($userInfo['game_online_time'] > 0 ? $userInfo['game_online_time'] : time());
                    $offlineIncome = 0;
                    if ($offlineTime > 60) {
                        $userProductList = UsersProduct::where('user_id', $userInfo['user_id'])
                            ->where('status', 1)
                            ->select();
                        $productList = Product::whereIn('id', get_arr_column($userProductList, 'product_id'))
                            ->column('income,offline_income', 'id');
                        foreach ($userProductList as $v) {
                            $productInfo = isset($productList[$v['product_id']]) ? $productList[$v['product_id']] : [];
                            if (empty($productInfo)) {
                                continue;
                            }
                            $offlineIncome += $offlineTime * $productInfo['offline_income'];
                        }
                    }
                    if ($offlineIncome > 0) {
                        (new UsersMoney())->amountChange($userInfo['user_id'], 2, $offlineIncome, 175, '离线收益', [
                            'come_uid' => $userInfo['user_id']
                        ]);
                    }
                    $userInfo['offline_time'] = $offlineTime;
                    $userInfo['offline_income'] = $offlineIncome;
                    $lastReceiveRewardTime = $userInfo['last_receive_reward_time'] > 0 ? $userInfo['last_receive_reward_time'] : time();
                    $connection->send(json_encode([
                        'type' => 'login',
                        'offline_time' => $offlineTime,
                        'offline_income' => $offlineIncome,
                        'last_receive_reward_time' => $lastReceiveRewardTime,
                        'receive_reward_min_time' => $lastReceiveRewardTime + 3600,
                        'receive_reward_countdown' => $lastReceiveRewardTime + 3600 - time()
                    ]));
                    $updateData = [
                        'game_online' => 1,
                        'game_online_time' => time(),
                        'last_offline_income' => $offlineIncome
                    ];
                    if ($userInfo['last_receive_reward_time'] <= 0) {
                        $updateData['last_receive_reward_time'] = $lastReceiveRewardTime;
                    }
                    Users::where('user_id', $userInfo['user_id'])->update($updateData);

                    self::$user[$workId] = $userInfo;
                    self::$works[$userInfo['user_id']] = $connection;
                }
                break;
            case 'receive_reward_money':
                if (isset(self::$user[$workId])) {
                    $userInfo = Users::where('user_id', self::$user[$workId]['user_id'])->find();
                    $basicMoney = 1000;
                    $money = 0;
                    $time = time() - $userInfo['last_receive_reward_time'];
                    $time = (int)($time / 3600);
                    $money += ($time * $basicMoney);
                    if ($money > 0) {
                        (new UsersMoney())->amountChange($userInfo['user_id'], 2, $money, 176, '奖励', [
                            'come_uid' => $userInfo['user_id']
                        ]);
                        $connection->send(json_encode([
                            'type' => 'receive_reward_money_notify',
                            'money' => $money,
                            'time' => $userInfo['last_receive_reward_time'] + ($time * 3600),
                            'receive_reward_min_time' => $userInfo['last_receive_reward_time'] + (($time + 1) * 3600),
                            'receive_reward_countdown' => $userInfo['last_receive_reward_time'] + (($time + 1) * 3600) - time()
                        ]));
                        Users::where('user_id', $userInfo['user_id'])
                            ->update([
                                'last_receive_reward_time' => $userInfo['last_receive_reward_time'] + ($time * 3600)
                            ]);
                    }
                }
                break;
            case 'online_income':
                if (isset(self::$user[$workId])) {
                    $money = (float)$data['money'];
                    if ($money > 0) {
                        $userInfo = self::$user[$workId];
                        (new UsersMoney())->amountChange($userInfo['user_id'], 2, $money, 174, '在线收益', [
                            'come_uid' => $userInfo['user_id']
                        ]);
                    }
                }
                break;
            case 'offline_income':
                if (isset(self::$user[$workId])) {
                    $userInfo = self::$user[$workId];
                    if ($userInfo['offline_income'] > 0) {
                        (new UsersMoney())->amountChange($userInfo['user_id'], 2, $userInfo['offline_income'], 175, '离线收益', [
                            'come_uid' => $userInfo['user_id']
                        ]);
                    }
                }
                break;
        }
        dump($data);
    }

    /**
     * 检测session
     *
     * @param string $workId 工作
     * @param string $sessionId session_id
     * @return bool
     * @author gkdos
     * 2019-09-24 18:11:56
     */
    private function checkSession($workId, $sessionId)
    {
        if (isset(self::$user[$workId])) {
            $userInfo = self::$user[$workId];

            if ($sessionId != $userInfo['session_id']) {
                return false;
            }
        }

        return true;
    }

    public function onConnect($connection)
    {
        echo "onConnect work_id:" . $connection->id . PHP_EOL;
        echo "new connection from ip " . $connection->getRemoteIp() . "\n";
    }

    public function onClose($connection)
    {
        if (isset(self::$user[$connection->id])) {
            $userInfo = self::$user[$connection->id];
            Users::where('user_id', $userInfo['user_id'])->update([
                'game_online' => 2,
                'game_online_time' => time()
            ]);
            if (isset(self::$works[$userInfo['user_id']])) {
                unset(self::$works[$userInfo['user_id']]);
            }
            unset(self::$user[$connection->id]);
        }
        echo "onClose work_id:" . $connection->id . PHP_EOL;
    }
}