<?php

namespace app\wap\controller;

use app\wap\logic\ProductLogic;
use think\Request;
use app\common\model\product\Product as ProductModel;
use app\common\model\money\UsersMoney;
use app\common\model\product\UsersProduct;
use app\common\server\Log;

class Product extends Base
{
    /**
     * 获取会员产品列表
     *
     * @param Request $request
     * @param ProductLogic $productLogic
     * @return [type]
     * @author gkdos
     * 2019-09-20T20:26:16+0800
     */
    public function getUserProductList(Request $request, ProductLogic $productLogic)
    {
        $userProductList = $productLogic->getUserProductList($this->user);

        return view('product/user_product_list', [
            'userProductList' => $userProductList
        ]);
    }

    /**
     * 获取会员最大产品信息
     *
     * @param Request $request
     * @param ProductLogic $productLogic
     * @author gkdos
     * 2019-09-21T22:36:50+0800
     */
    public function getUserMaxProductInfo(Request $request, ProductLogic $productLogic)
    {
        $productInfo = $productLogic->getUserMaxProductInfo($this->user);

        return json()->data([
            'code' => 1,
            'productInfo' => $productInfo
        ]);
    }

    /**
     * 获取会员最大能购买产品信息
     *
     * @param Request $request
     * @param ProductLogic $productLogic
     * @author gkdos
     * 2019-09-21T22:36:50+0800
     */
    public function getUserBuyMaxProductInfo(Request $request, ProductLogic $productLogic)
    {
        $productInfo = $productLogic->getUserBuyMaxProductInfo($this->user);

        return json()->data([
            'code' => 1,
            'productInfo' => $productInfo
        ]);
    }

    /**
     * 获取产品列表
     *
     * @param Request $request
     * @param ProductLogic $productLogic
     * @author gkdos
     * 2019-09-19T20:42:57+0800
     */
    public function getProductList(Request $request, ProductLogic $productLogic)
    {

        $productList = $productLogic->getProductList($this->user);

        return view('product/product_list', [
            'userBuyMaxProductInfo' => $productLogic->getUserBuyMaxProductInfo($this->user),
            'productList' => $productList
        ]);
    }

    /**
     * 获取排行榜信息
     *
     * @param Request $request
     * @param ProductLogic $productLogic
     * @author gkdos
     * 2019-09-19T20:56:01+0800
     */
    public function getLeaderboardList(Request $request, ProductLogic $productLogic)
    {
        $leaderboardList = $productLogic->getLeaderboardList($this->user);

        $productNames = ProductModel::getProductNames();
        return view('product/leaderboard_info', [
            'productNames' => $productNames,
            'leaderboardList' => $leaderboardList,
            'userMoney' => UsersMoney::where('uid', $this->user_id)->where('mid', 2)->value('total')
        ]);
    }

    /**
     * 购买产品
     *
     * @param Request $request
     * @param ProductLogic $productLogic
     * @author gkdos
     * 2019-09-20T19:42:58+0800
     */
    public function buyProduct(Request $request, ProductLogic $productLogic)
    {
        if ($request->isPost()) {
            try {
                $res = $productLogic->buyProduct($this->user);
                if ($res['code'] == 1) {
                    return json()->data([
                        'code' => 1,
                        'msg' => $res['msg']
                    ]);
                } else {
                    return json()->data($res);
                }
            } catch (\Exception $e) {
                Log::exceptionWrite('购买失败', $e);
                return json()->data(['code' => -1, 'msg' => '购买失败']);
            }
        }
    }

    /**
     * 合成产品
     *
     * @param Request $request
     * @param ProductLogic $productLogic
     * @author gkdos
     * 2019-09-21T17:01:52+0800
     */
    public function mergeProduct(Request $request, ProductLogic $productLogic)
    {
        if ($request->isPost()) {
            try {
                $res = $productLogic->mergeProduct($this->user);
                if ($res['code'] == 1) {
                    return json()->data([
                        'code' => 1,
                        'msg' => $res['msg'],
                        'data' => [
                            'infoid' => $res['data']['id'],
                            'productInfo' => $res['data']['productInfo'],
                            'rewardId' => $res['data']['rewardId']
                        ]
                    ]);
                } else {
                    return json()->data([
                        'code' => -1,
                        'msg' => $res['msg']
                    ]);
                }
            } catch (\Exception $e) {
                Log::exceptionWrite('合并产品失败', $e);
                return json()->data(['code' => -1, 'msg' => '合并失败']);
            }
        }
    }

    /**
     * 删除产品
     *
     * @param Request $request
     * @param ProductLogic $productLogic
     * @return [type]
     * @author gkdos
     * 2019-09-23T16:10:39+0800
     */
    public function deleteProduct(Request $request, ProductLogic $productLogic)
    {
        if ($request->isPost()) {
            try {
                $res = $productLogic->deleteProduct($this->user);
                if ($res['code'] == 1) {
                    return json()->data([
                        'code' => 1,
                        'msg' => $res['msg']
                    ]);
                } else {
                    return json()->data([
                        'code' => -1,
                        'msg' => $res['msg']
                    ]);
                }
            } catch (\Exception $e) {
                Log::exceptionWrite('删除产品失败', $e);
                return json()->data(['code' => -1, 'msg' => '删除失败']);
            }
        }
    }

    /**
     * 合成抽取
     *
     * @param Request $request
     * @param ProductLogic $productLogic
     * @return \think\response\Json
     * @author gkdos
     * 2019-09-25 15:20:34
     */
    public function randomExtractionProduct(Request $request, ProductLogic $productLogic)
    {
        if ($request->isPost()) {
            try {
                $res = $productLogic->randomExtractionProduct($this->user);
                if ($res['code'] == 1) {
                    return json()->data([
                        'code' => 1,
                        'msg' => $res['msg'],
                        'data' => $res['data']
                    ]);
                } else {
                    return json()->data([
                        'code' => -1,
                        'msg' => $res['msg']
                    ]);
                }
            } catch (\Exception $e) {
                Log::exceptionWrite('随机抽取产品失败', $e);
                return json()->data(['code' => -1, 'msg' => '抽取失败']);
            }
        }
    }

    /**
     * 移动位置
     *
     * @param Request $request
     * @param ProductLogic $productLogic
     * @return void
     * @author gkdos
     */
    public function movePosition(Request $request, ProductLogic $productLogic)
    {
        if ($request->isPost()) {
            try {
                $res = $productLogic->movePosition($this->user);
                if ($res['code'] == 1) {
                    return json()->data([
                        'code' => 1,
                        'msg' => $res['msg']
                    ]);
                } else {
                    return json()->data([
                        'code' => -1,
                        'msg' => $res['msg']
                    ]);
                }
            } catch (\Exception $e) {
                Log::exceptionWrite('移动位置失败', $e);
                return json()->data(['code' => -1, 'msg' => '移动失败']);
            }
        }
    }

    /**
     * 五福合成
     *
     * @param Request $request
     * @param ProductLogic $productLogic
     * @author gkdos
     */
    public function wufuComposite(Request $request, ProductLogic $productLogic)
    {
        if ($request->isPost()) {
            try {
                $res = $productLogic->wufuComposite($this->user);
                if ($res['code'] == 1) {
                    return json()->data([
                        'code' => 1,
                        'msg' => $res['msg']
                    ]);
                } else {
                    return json()->data([
                        'code' => -1,
                        'msg' => $res['msg']
                    ]);
                }
            } catch (\Exception $e) {
                Log::exceptionWrite('五福合成失败', $e);
                return json()->data(['code' => -1, 'msg' => '合成失败']);
            }
        }
    }

    /**
     * 检测是否可以合成五福
     *
     * @param Request $request
     * @author gkdos
     */
    public function checkIsWufuComposite(Request $request)
    {
        $ids = UsersProduct::where('user_id', $this->user['user_id'])
            ->where('status', 1)
            ->whereIn('product_id', [38, 39, 40, 41, 42])
            ->group('product_id')
            ->column('product_id');
        $ids = array_flip($ids);
        $count = count($ids);
        if ($count >= 5) {
            return ['code' => 1, 'msg' => '达到条件了', 'data' => ['ids' => $ids]];
        }
        return ['code' => -1, 'msg' => '未达到条件', 'data' => ['ids' => $ids]];
    }
}
