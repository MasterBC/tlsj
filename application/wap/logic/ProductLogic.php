<?php

namespace app\wap\logic;

use app\common\model\product\Product;
use app\common\model\product\UsersProduct;
use app\common\model\product\UsersBuyProductLog;
use app\common\model\UsersRedEnvelopeLog;
use app\common\server\Log;
use app\common\model\money\UsersMoney;
use app\common\model\Users;
use app\common\server\bonus\Server;
use think\facade\Request;
use think\Db;

class ProductLogic
{

    /**
     * 获取会员最大产品信息
     *
     * @param array $userInfo 会员信息
     * @return array
     * @author gkdos
     * 2019-09-21T22:33:41+0800
     */
    public function getUserMaxProductInfo($userInfo)
    {
        try {
            $userProductId = (int) UsersProduct::where('user_id', $userInfo['user_id'])->max('product_id');
            if ($userProductId <= 0) {
                $userProductId = 1;
            }
            $userMaxProductInfo = Product::getProductInfoById($userProductId);
            if ($userMaxProductInfo) {
                $userMaxProductInfo['picture'] = get_img_show_url($userMaxProductInfo['picture']);
            }

            return (array) $userMaxProductInfo->toArray();
        } catch (\Exception $e) {
            Log::exceptionWrite('获取会员最大产品失败', $e);
            return [];
        }
    }

    /**
     * 获取会员最大能购买产品信息
     *
     * @param array $userInfo 会员信息
     * @return array
     * @author gkdos
     * 2019-09-21T22:33:41+0800
     */
    public function getUserBuyMaxProductInfo($userInfo)
    {
        try {
            $userProductId = (int) UsersProduct::where('user_id', $userInfo['user_id'])->max('product_id');
            if ($userProductId <= 0) {
                $userProductId = 1;
            }
            $userProductId = min($userProductId, 37);
            $userProductId -= 4;
            $userProductId = max($userProductId, 1);
            $userMaxProductInfo = Product::getProductInfoById($userProductId);
            if ($userMaxProductInfo) {
                $buyNum = UsersBuyProductLog::where('user_id', $userInfo['user_id'])->where('product_id', $userMaxProductInfo['id'])->count('id');
                for ($i = 0; $i < $buyNum; $i++) {
                    $userMaxProductInfo['amount'] += ($userMaxProductInfo['amount'] * $userMaxProductInfo['next_per'] / 100);
                }
                $userMaxProductInfo['amount'] = min($userMaxProductInfo['amount'], $userMaxProductInfo['total_amount']);
                $userMaxProductInfo['show_amount'] = moneyTransformation($userMaxProductInfo['amount']);
                $userMaxProductInfo['picture'] = get_img_show_url($userMaxProductInfo['picture']);
            }
            return (array) $userMaxProductInfo->toArray();
        } catch (\Exception $e) {
            Log::exceptionWrite('获取会员最大产品失败', $e);
            return [];
        }
    }

    /**
     * 获取会员产品列表
     *
     * @param array $userInfo 会员信息
     * @return [type]
     * @author gkdos
     * 2019-09-20T20:04:51+0800
     */
    public function getUserProductList($userInfo)
    {
        try {
            $userProductList = UsersProduct::where('user_id', $userInfo['user_id'])
                ->where('status', 1)
                ->select();
            $productIds = get_arr_column($userProductList, 'product_id');
            $productList = Product::whereIn('id', $productIds)->column('product_name,picture,number', 'id');

            $data = [];
            foreach ($userProductList as $k => $v) {
                $productInfo = $productList[$v['product_id']] ?? [];
                $data[] = [
                    'product_name' => $productInfo['product_name'] ?? '',
                    'product_picture' => get_img_show_url($productInfo['picture'] ?? ''),
                    'id' => $v['id'],
                    'income' => $v['income'],
                    'position' => $v['position'],
                    'product_id' => $v['product_id'],
                    'product_number' => $productInfo['number'] ?? 1,
                    'show_income' => moneyTransformation($v['income'])
                ];
            }

            return $data;
        } catch (\Exception $e) {
            Log::exceptionWrite('查询会员产品列表', $e);
            return [];
        }
    }

    /**
     * 获取产品列表
     *
     * @param array $userInfo 会员信息
     * @return array|\PDOStatement|string|\think\Collection
     * @author gkdos
     * 2019-09-19 20:20:47
     */
    public function getProductList($userInfo)
    {
        try {
            $productList = Product::where('id', '<', 38)->select();
            $data = [];
            $userBuyProductList = UsersBuyProductLog::where('user_id', $userInfo['user_id'])
                ->group('product_id')
                ->column('count(id)', 'product_id');
            foreach ($productList as $k => $v) {
                $arr = $v;
                $buyNum = $userBuyProductList[$v['id']] ?? 0;
                for ($i = 0; $i < $buyNum; $i++) {
                    $arr['amount'] += ($arr['amount'] * $v['next_per'] / 100);
                }
                // $arr['amount'] += ($v['amount'] * $v['next_per'] / 100 * $buyNum);
                $arr['amount'] = min($arr['amount'], $v['total_amount']);
                $arr['show_amount'] = moneyTransformation($arr['amount']);
                $data[] = $arr;
            }

            return $data;
        } catch (\Exception $e) {
            Log::exceptionWrite('查询产品列表失败', $e);
            return [];
        }
    }

    /**
     * 获取排行榜信息
     *
     * @param array $userInfo 会员信息
     * @return array|\PDOStatement|string|\think\Collection
     * @author gkdos
     * 2019-09-19 20:20:47
     */
    public function getLeaderboardList($userInfo)
    {
        try {
            $usersMoneyList = UsersMoney::where('mid', 2)
                ->group('uid')
                ->field('total,uid')
                ->order('total', 'desc')
                ->limit(50)
                ->select();

            $data = [];
            $userIds = get_arr_column($usersMoneyList, 'uid');
            $userList = Users::whereIn('user_id', $userIds)
                ->field('head,nickname,account,user_id,product_id')
                ->select();
            $userList = convert_arr_key($userList, 'user_id');
            foreach ($usersMoneyList as $k => $v) {
                $userInfo = $userList[$v['uid']] ?? [];
                if (empty($userInfo)) {
                    continue;
                }
                $data[] = [
                    'nickname' => $userInfo['nickname'],
                    'account' => $userInfo['account'],
                    'head' => $userInfo['head'] ? get_img_show_url($userInfo['head']) : '',
                    'total' => $v['total'],
                    'product_id' => $userInfo['product_id']
                ];
            }

            return $data;
        } catch (\Exception $e) {
            Log::exceptionWrite('查询排行榜信息失败', $e);
            return [];
        }
    }

    /**
     * 购买产品
     *
     * @param array $userInfo 会员信息
     * @return array
     * @author gkdos
     * 2019-09-20T18:01:49+0800
     */
    public function buyProduct($userInfo)
    {
        $id = Request::param('id', 0, 'intval');
        if ($id <= 0) {
            return ['code' => -1, 'msg' => '参数错误'];
        }
        $productInfo = Product::getProductInfoById($id);
        if (empty($productInfo)) {
            return ['code' => -1, 'msg' => '参数错误'];
        }
        if ($productInfo['id'] > 37) {
            return ['code' => -1, 'msg' => '不能购买'];
        }

        $count = UsersProduct::getUserProductNumByUserId($userInfo['user_id']);
        if ($count >= 12) {
            return ['code' => -1, 'msg' => '位置满了,请合成或者拖到回收箱'];
        }

        $amount = $productInfo['amount'];
        $userBuyNum = UsersBuyProductLog::getUserBuyProductNum($userInfo['user_id'], $productInfo['id']);
        $amount += ($productInfo['amount'] * $productInfo['next_per'] / 100 * $userBuyNum);

        $amount = min($amount, $productInfo['total_amount']);

        $userMoneyModel = new UsersMoney();
        $balance = $userMoneyModel::getUsersMoneyByUserId($userInfo['user_id'], 2);
        if ($balance < $amount) {
            return ['code' => -8, 'msg' => get_money_name(2) . '不足'];
        }

        Db::startTrans();
        try {
            $userMoneyModel->amountChange($userInfo['user_id'], 2, '-' . $amount, 171, '购买' . $productInfo['product_name'], [
                'come_uid' => $userInfo['user_id']
            ]);

            UsersBuyProductLog::addBuyLog($userInfo['user_id'], $productInfo['id'], $amount);
            UsersProduct::addUserProduct($userInfo['user_id'], $productInfo['id'], $amount, $productInfo['income']);

            Db::commit();
            return ['code' => 1, 'msg' => '购买成功', 'data' => $productInfo->toArray()];
        } catch (\Exception $e) {
            Db::rollback();
            Log::exceptionWrite('购买产品失败', $e);
            return ['code' => -1, 'msg' => '购买失败'];
        }
    }

    /**
     * 合成产品
     *
     * @param array $userInfo 会员信息
     * @return array
     * @author gkdos
     * 2019-09-21T17:01:19+0800
     */
    public function mergeProduct($userInfo)
    {
        $infoId1 = Request::param('infoid1', 0, 'intval');
        $infoId2 = Request::param('infoid2', 0, 'intval');
        /* $position = Request::param('position', 0, 'intval');

          if($position < 0 || $position > 12) {
          return ['code' => -1, 'msg' => '参数错误'];
          } */
        Db::startTrans();
        try {
            $userProductInfo1 = UsersProduct::getUserProductInfoByIdAndUserId($infoId1, $userInfo['user_id']);
            $userProductInfo2 = UsersProduct::getUserProductInfoByIdAndUserId($infoId2, $userInfo['user_id']);
            if (empty($userProductInfo1) || empty($userProductInfo2)) {
                return ['code' => -1, 'msg' => '请刷新重试'];
            }
            if ($userProductInfo1['product_id'] != $userProductInfo2['product_id'] && (!in_array($userProductInfo1['product_id'], [43, 44]) && !in_array($userProductInfo2['product_id'], [43, 44]))) {
                return ['code' => -1, 'msg' => '必须是同一种牛'];
            }
            if (in_array($userProductInfo2['product_id'], [37, 45])) {
                return ['code' => -1, 'msg' => '此牛不能合成'];
            }
            $money = 0;
            if (in_array($userProductInfo1['product_id'], [43, 44])) {
                $money = (float) config('security_info.niuliang_zhinv_reward');
            } else {
                $oldProductInfo = Product::getProductInfoById($userProductInfo2['product_id']);
                if ($oldProductInfo['reward_red_envelope']) {
                    $arr = explode('-', $oldProductInfo['reward_red_envelope']);
                    $money = $arr[array_rand($arr)] ?? 0;
                }
            }
            if ($money > 0) {
                (new UsersMoney())->amountChange($userInfo['user_id'], 3, $money, 173, '合成红包', [
                    'come_uid' => $userInfo['user_id']
                ]);
                $bonusServer = new Server();
                $bonusServer->recommendedAward($money, $userInfo);
                $bonusServer->clear();
            }

            $productInfo = Product::getProductInfoById($userProductInfo2['product_id'] + 1);
            $userProductId = 0;
            if ($productInfo['id'] <= 37) {
                $userProductId = UsersProduct::addUserProduct(
                    $userInfo['user_id'],
                    $productInfo['id'],
                    $userProductInfo1['amount'] + $userProductInfo2['amount'],
                    $productInfo['income'],
                    $userProductInfo2['position'],
                    2
                );
            }
            UsersProduct::whereIn('id', [$userProductInfo1['id'], $userProductInfo2['id']])
                ->where('status', 1)
                ->update([
                    'status' => 3,
                    'merge_id' => $userProductId,
                    'update_time' => time()
                ]);
            $rewardId = UsersRedEnvelopeLog::issueAnUpgradeRedEnvelope($userInfo['user_id'], $productInfo['id']);

            Db::commit();
            $productInfo['show_income'] = moneyTransformation($productInfo['income']);
            $productInfo['reward_red_envelope'] = $money;
            return ['code' => 1, 'msg' => '合成成功', 'data' => [
                'id' => $userProductId,
                'productInfo' => $productInfo,
                'rewardId' => $rewardId
            ]];
        } catch (\Exception $e) {
            Db::rollback();
            Log::exceptionWrite('产品合成失败', $e);
            return ['code' => -1, 'msg' => '合成失败'];
        }
    }

    /**
     * 删除产品
     *
     * @param array $userInfo 会员信息
     * @return array
     * @author gkdos
     * 2019-09-21T17:01:19+0800
     */
    public function deleteProduct($userInfo)
    {
        $infoId = Request::param('id', 0, 'intval');
        Db::startTrans();
        try {
            $userProductInfo = UsersProduct::getUserProductInfoByIdAndUserId($infoId, $userInfo['user_id']);
            if (empty($userProductInfo)) {
                return ['code' => -1, 'msg' => '产品不存在'];
            }
            $productInfo = Product::getProductInfoById($userProductInfo['product_id']);
            $userProductInfo->status = 4;
            $userProductInfo->update_time = time();
            $userProductInfo->save();
            if ($productInfo['recovery_amount'] > 0) {
                (new UsersMoney())->amountChange($userInfo['user_id'], 2, $productInfo['recovery_amount'], 172, '删除' . $productInfo['product_name'], [
                    'come_uid' => $userInfo['user_id']
                ]);
            }
            Db::commit();
            return ['code' => 1, 'msg' => '删除成功'];
        } catch (\Exception $e) {
            Db::rollback();
            Log::exceptionWrite('删除产品失败', $e);
            return ['code' => -1, 'msg' => '删除失败'];
        }
    }

    /**
     * 获取抽奖产品
     *
     * @param array $userInfo 会员信息
     * @return array
     * @author gkdos
     * 2019-09-24 21:49:43
     */
    public function getLotteryProducts($userInfo)
    {
        $productList = Product::where('id', '>', 37)
            ->column('picture,product_name,number', 'id');

        $data = [
            0 => $productList[45],
            1 => $productList[44],
            2 => $productList[43],
            3 => $productList[42],
            4 => $productList[41],
            5 => $productList[40],
            6 => $productList[39],
            7 => $productList[38],
            8 => $productList[41],
            9 => $productList[43],
            10 => $productList[39],
            11 => $productList[38]
        ];
        $arr = [38, 39, 40, 41, 42];
        $num = $userInfo['user_id'];
        while ($num > 5) {
            $num = (int) ($num / 2);
        }
        $disabledId = $arr[$num] ?? '';

        foreach ($data as $k => $v) {
            $arr = $v;
            $arr['probability'] = 100;
            if ($userInfo['is_hold_dividend_product'] != 1) {
                if ($v['id'] == 45) {
                    $arr['probability'] = 0;
                }
                if ($v['id'] == $disabledId) {
                    $arr['probability'] = 0;
                }
            }
            if ($v['id'] == 43) {
                if ($userInfo['is_niuqi'] != 1) {
                    $arr['probability'] = 0;
                }
            }
            if ($v['id'] == 44) {
                if ($userInfo['is_zilvu'] != 1) {
                    $arr['probability'] = 0;
                }
            }

            $data[$k] = $arr;
        }

        return $data;
    }

    /**
     * 合成抽取
     *
     * @param array $userInfo 会员信息
     * @return array
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author gkdos
     * 2019-09-25 15:20:48
     */
    public function randomExtractionProduct($userInfo)
    {
        $infoId1 = Request::param('infoid1', 0, 'intval');
        $infoId2 = Request::param('infoid2', 0, 'intval');

        $userProductInfo1 = UsersProduct::getUserProductInfoByIdAndUserId($infoId1, $userInfo['user_id']);
        $userProductInfo2 = UsersProduct::getUserProductInfoByIdAndUserId($infoId2, $userInfo['user_id']);
        if (empty($userProductInfo1) || empty($userProductInfo2)) {
            return ['code' => -1, 'msg' => '请刷新重试'];
        }

        if ($userProductInfo1['product_id'] != $userProductInfo2['product_id']) {
            return ['code' => -1, 'msg' => '必须是同一种牛'];
        }
        if ($userProductInfo2['product_id'] != 37) {
            return ['code' => -1, 'msg' => '此级别不能合成哦'];
        }

        $lotteryProducts = $this->getLotteryProducts($userInfo);

        $data = [];
        foreach ($lotteryProducts as $k => $v) {
            if ($v['probability'] > 0) {
                $data[] = $k;
            }
        }

        $key = $data[array_rand($data)];

        $productInfo = $lotteryProducts[$key] ?? [];
        $productInfo = Product::getProductInfoById($productInfo['id']);
        $userProductId = UsersProduct::addUserProduct(
            $userInfo['user_id'],
            $productInfo['id'],
            $userProductInfo1['amount'] + $userProductInfo2['amount'],
            $productInfo['income'],
            $userProductInfo2['position'],
            2
        );
        UsersProduct::whereIn('id', [$userProductInfo1['id'], $userProductInfo2['id']])
            ->where('status', 1)
            ->update([
                'status' => 3,
                'merge_id' => $userProductId,
                'update_time' => time()
            ]);
        $productInfo['key'] = $key;
        return ['code' => 1, 'msg' => '成功', 'data' => $productInfo];
    }

    /**
     * 五福合成
     *
     * @param array $userInfo 会员信息
     * @return array
     * @author gkdos
     */
    public function wufuComposite($userInfo)
    {
        Db::startTrans();
        try {
            $ids = UsersProduct::where('user_id', $userInfo['user_id'])
                ->where('status', 1)
                ->whereIn('product_id', [38, 39, 40, 41, 42])
                ->group('product_id')
                ->column('id');
            if (count($ids) < 5) {
                return ['code' => -1, 'msg' => '条件未达到'];
            }

            $productInfo = Product::getProductInfoById(45);
            if (empty($productInfo)) {
                return ['code' => -1, 'msg' => '暂时不能合成'];
            }
            UsersProduct::whereIn('id', $ids)->update([
                'status' => 3
            ]);
            $userProductId = UsersProduct::addUserProduct(
                $userInfo['user_id'],
                $productInfo['id'],
                $productInfo['amount'],
                $productInfo['income'],
                0,
                2
            );
            UsersProduct::whereIn('id', $ids)->update([
                'merge_id' => $userProductId,
                'update_time' => time()
            ]);

            Db::commit();

            return ['code' => 1, 'msg' => '合成成功'];
        } catch (\Exception $e) {
            Db::rollback();
            Log::exceptionWrite('五福合成失败', $e);
            return ['code' => -1, 'msg' => '合成失败'];
        }
    }

    /**
     * 移动位置
     *
     * @param array $userInfo 会员信息
     * @return array
     * @author gkdos
     */
    public function movePosition($userInfo)
    {
        Db::startTrans();
        try {
            $type = (int) Request::param('type');
            if (!in_array($type, [1, 2])) {
                return ['code' => -1, 'msg' => '参数错误'];
            }
            if ($type == 1) {
                $infoId1 = Request::param('infoid1', 0, 'intval');
                $infoId2 = Request::param('infoid2', 0, 'intval');

                $userProductInfo1 = UsersProduct::getUserProductInfoByIdAndUserId($infoId1, $userInfo['user_id']);
                $userProductInfo2 = UsersProduct::getUserProductInfoByIdAndUserId($infoId2, $userInfo['user_id']);
                if (empty($userProductInfo1) || empty($userProductInfo2)) {
                    return ['code' => -1, 'msg' => '参数错误'];
                }

                UsersProduct::where('id', $userProductInfo1['id'])->update([
                    'position' => $userProductInfo2['position']
                ]);
                UsersProduct::where('id', $userProductInfo2['id'])->update([
                    'position' => $userProductInfo1['position']
                ]);
            } elseif ($type == 2) {
                $infoId = Request::param('infoid', 0, 'intval');
                $position = Request::param('position', 0, 'intval');
                if (!in_array($position, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12])) {
                    return ['code' => -1, 'msg' => '参数错误'];
                }

                $userProductInfo = UsersProduct::getUserProductInfoByIdAndUserId($infoId, $userInfo['user_id']);
                if (empty($userProductInfo)) {
                    return ['code' => -1, 'msg' => '参数错误'];
                }
                $count = UsersProduct::where('user_id', $userInfo['user_id'])
                    ->where('status', 1)
                    ->where('position', $position)->count();
                if ($count > 0) {
                    return ['code' => -1, 'msg' => '此位置已有牛'];
                }
                UsersProduct::where('id', $userProductInfo['id'])->update([
                    'position' => $position
                ]);
            }
            Db::commit();
            return ['code' => 1, 'msg' => '移动成功'];
        } catch (\Exception $e) {
            Db::rollback();
            Log::exceptionWrite('移动位置失败', $e);
            return ['code' => -1, 'msg' => '移动失败'];
        }
    }
}
