<?php

namespace app\admin\controller;

use think\db;
use think\Request;
use think\db\Where;
use think\facade\Log;
use app\common\model\AdminLog;
use app\common\model\Users;
use app\common\model\product\Product as ProductModel;
use app\common\model\product\UsersProduct;
use app\common\model\product\UsersBuyProductLog;
use app\common\model\money\Money as MoneyModel;

class Product extends Base
{

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->assign('productNameArr', ProductModel::getProductNames());
        $this->assign('amountTypeData', ProductModel::$amountTypeData);
        $this->assign('webMoneyIdNameArr', MoneyModel::getMoneyNames());
    }

    /**
     * 人物列表参数设置
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function configProductList(Request $request, Where $where, ProductModel $productModel)
    {
        $productNameArr = ProductModel::getProductNames();
        if ($request->isAjax()) {
            try {
                $configList = [];
                $title = $request->param('title', '', 'trim');
                $page = $request->get('p', '1', 'int') - 1;
                $pageSize = $request->get('p_num', '50', 'int');
                $list = $productModel::where($where)->order('id asc')->select();
                foreach ($list as $v) {
                    $arr = [
                    'id' => $v['id'],
                    'product_name' => $v['product_name'],
                    'picture' => $v['picture'],
                    'amount' => $v['amount'],
                    'number' => $v['number'],
                    'income' => $v['income'],
                    'offline_income' => $v['offline_income'],
                    'income' => $v['income'],
                    'next_per' => $v['next_per'] . ' %',
                    'total_amount' => $v['total_amount'],
                    'recovery_amount' => $v['recovery_amount'],
                    'product_release' => $productNameArr[$v['product_release']] ?? '',
                    'status' => $productModel::$statusData[$v['status']] ?? ''
                    ];
                    $configList[] = $arr;
                }
                $data = [
                    'code' => 1,
                    'data' => $configList,
                    'count' => $productModel::where($where)->count()
                ];
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询列表失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取到信息',
                ];
                return json()->data($data);
            }
        } else {
            return view('product/config_product/config_list');
        }
    }

    /**
     * 添加
     * @param Request $request
     * @param Country $countryModel
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function addProductConfig(Request $request, ProductModel $productModel)
    {
        if ($request->isPost()) {
            try {
                $data['product_name'] = $request->param('product_name', '', 'trim');
                $data['picture'] = $request->param('picture', '', 'trim');
                $data['amount'] = $productModel::getAmountTypeData($request->param('amount_type', '', 'intval'), $request->param('amount_k', '', 'intval'));
                $data['number'] = $request->param('number', '', 'trim');
                $data['income'] = $request->param('income', '', 'intval');
                $data['next_per'] = $request->param('next_per', '', 'floatval');
                $data['total_amount'] = $productModel::getAmountTypeData($request->param('total_type', '', 'intval'), $request->param('total_k', '', 'intval'));
                $data['recovery_amount'] =$productModel::getAmountTypeData($request->param('recovery_type', '', 'intval'), $request->param('recovery_k', '', 'intval'));
                $data['product_release'] = $request->param('product_release', '', 'intval');
                $data['status'] = $request->param('status', '', 'intval');
                $productModel->insertGetId($data);
                AdminLog::addLog('添加人物参数', $request->param());
                return json()->data(['code' => 1, 'msg' => '添加成功']);
            } catch (\Exception $e) {
                Log::write('添加人物参数失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            return view('product/config_product/add_config');
        }
    }

    /**
     * 编辑修改人物
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function editProductConfig(Request $request, ProductModel $productModel)
    {
        $id = $request->param('id', '', 'intval');
        if ($id <= 0) {
            $this->error('非法操作');
        }
        $info = $productModel::getProductInfoById($id);
        if (empty($info)) {
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info->product_name = $request->param('product_name', '', 'trim');
                $info->picture = $request->param('picture', '', 'trim');
                if ($request->param('amount_type') <> '' && $request->param('amount_k') <> '') {
                    $info->amount = $productModel::getAmountTypeData($request->param('amount_type', '', 'intval'), $request->param('amount_k', '', 'intval'));
                }
                $info->number = $request->param('number', '', 'trim');
                $info->income = $request->param('income', '', 'intval');
                $info->offline_income = $request->param('offline_income', '', 'floatval');
                $info->next_per = $request->param('next_per', '', 'floatval');
                if ($request->param('total_type') <> '' && $request->param('total_k') <> '') {
                    $info->total_amount = $productModel::getAmountTypeData($request->param('total_type', '', 'intval'), $request->param('total_k', '', 'intval'));
                }
                if ($request->param('recovery_type') <> '' && $request->param('recovery_k') <> '') {
                    $info->recovery_amount = $productModel::getAmountTypeData($request->param('recovery_type', '', 'intval'), $request->param('recovery_k', '', 'intval'));
                }
                $info->product_release = $request->param('product_release', '', 'intval');
                $info->status = $request->param('status', '', 'intval');
                $info->reward_red_envelope = $request->param('reward_red_envelope');
                $info->save();
                AdminLog::addLog('修改人物参数', $request->param());
                $info->_afterUpdate();
                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                Log::write('修改人物参数失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            return view('product/config_product/edit_config', ['info' => $info]);
        }
    }

    /**
     * 删除人物参数
     */
    public function delProductConfig(Request $request, ProductModel $productModel)
    {
        if ($request->isPost()) {
            $id = $request->param('id');
            if (!$id) {
                return json(['code' => -1, 'msg' => '请选择']);
            }
            Db::startTrans();
            try {
                $arrId = explode(',', $id);
                $logList = $productModel::whereIn('id', $arrId)->select();
                if (count($logList) <= 0) {
                    return json()->data(['code' => -1, 'msg' => '此信息不支持该操作']);
                }
                $productModel::whereIn('id', $arrId)->delete();
                $data = $logList->toArray();
                $num = count($data);
                Db::commit();
                AdminLog::addLog('删除人物参数', $data);
                if ($num > 0) {
                    return json()->data(['code' => 1, 'msg' => '成功删除' . $num . '条数据']);
                } else {
                    return json()->data(['code' => -1, 'msg' => '没有任何数据发生变化']);
                }
            } catch (\Exception $e) {
                Db::rollback();
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        }
    }

    /**
     * 会员持有人物列表
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function userProductList(Request $request, Where $where, UsersProduct $usersProductModel)
    {
        $usersProductstatusArr = $usersProductModel::$status;
        if ($request->isAjax()) {
            try {
                if ($account = $request->param('account', '', 'trim')) {
                    $userId = (int) Users::where('account', $account)->value('user_id');
                    $where['user_id'] = $userId;
                }
                if ($productId = $request->param('product_id', '', 'trim')) {
                    $where['product_id'] = $productId;
                }
                if ($status = $request->param('status', '', 'trim')) {
                    $where['status'] = $status;
                }
                if ($time = $request->param('time', '', 'trim')) {
                    $times = explode(' - ', $time);
                    $startTime = strtotime($times[0]);
                    $endTime = strtotime($times[1]);
                    $where['add_time'] = ['between', [$startTime, $endTime]];
                }
                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');
                $list = $usersProductModel::where($where)->order('id', 'desc')->limit($page * $pageSize, $pageSize)->select();
                $userIds = get_arr_column($list, 'user_id');
                $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
                $productNameArr = ProductModel::getProductNames();

                $levelLogList = [];
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['id'],
                        'amount' => $v['amount'],
                        'income' => $v['income'],
                        'account' => isset($users[$v['user_id']]) ? $users[$v['user_id']] : '',
                        'produc_name' => isset($productNameArr[$v['product_id']]) ? $productNameArr[$v['product_id']] : '',
                        'add_time' => date('Y-m-d H:i:s', $v['add_time'])
                    ];

                    $levelLogList[] = $arr;
                }
                if (count($levelLogList) == 0) {
                    $data = [
                        'code' => -1,
                        'msg' => '没有等级日志'
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $levelLogList,
                        'count' => $usersProductModel::where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询日志失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取日志，请联系管理员',
                ];
                return json()->data($data);
            }
        } else {

            return view('product/user_product/user_product_list', [
                'usersProductstatusArr' => $usersProductstatusArr,
            ]);
        }
    }

    /**
     * 添加会员持有的人物
     * @param Request $request
     * @param Country $countryModel
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function addUserProductInfo(Request $request, UsersProduct $usersProductModel)
    {
        if ($request->isPost()) {
            $accont = $request->param('account', '', 'trim');
            $userInfo = Users::where('account', $accont)->field('user_id,account,mobile')->find();
            if ($userInfo['user_id'] <= 0) {
                $this->error($accont . '不存在');
            }
            try {
                $data['user_id'] = $userInfo['user_id'];
                $data['income'] = $request->param('income', '', 'intval');
                $data['amount'] = $request->param('amount', '', 'intval');
                $data['product_id'] = $request->param('product_id', '', 'intval');
                $data['add_time'] = time();
                $usersProductModel->insertGetId($data);
                AdminLog::addLog('添加会员持有的人物', $request->param());
                return json()->data(['code' => 1, 'msg' => '添加成功']);
            } catch (\Exception $e) {
                Log::write('添加会员持有的人物失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            return view('product/user_product/add_info');
        }
    }

    /**
     * 编辑修改人物
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function editUserProductInfo(Request $request, UsersProduct $usersProductModel)
    {
        $id = $request->param('id', '', 'intval');
        if ($id <= 0) {
            $this->error('非法操作');
        }
        $info = $usersProductModel::where('id', $id)->find();
        if (empty($info)) {
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info->product_id = $request->param('product_id', '', 'intval');
                $info->amount = $request->param('amount', '', 'intval');
                $info->income = $request->param('income', '', 'intval');
                $info->save();
                AdminLog::addLog('修改会员持有的人物', $request->param());
                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                Log::write('修改会员持有的人物失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            return view('product/user_product/edit_info', ['info' => $info]);
        }
    }

    /**
     * 删除会员持有的人物
     */
    public function delUserProductList(Request $request, UsersProduct $usersProductModel)
    {
        if ($request->isPost()) {
            $id = $request->param('id');
            if (!$id) {
                return json(['code' => -1, 'msg' => '请选择']);
            }
            Db::startTrans();
            try {
                $arrId = explode(',', $id);
                $logList = $usersProductModel::whereIn('id', $arrId)->select();
                if (count($logList) <= 0) {
                    return json()->data(['code' => -1, 'msg' => '此信息不支持该操作']);
                }
                $usersProductModel::whereIn('id', $arrId)->delete();
                $data = $logList->toArray();
                $num = count($data);
                Db::commit();
                AdminLog::addLog('删除会员持有的人物', $data);
                if ($num > 0) {
                    return json()->data(['code' => 1, 'msg' => '成功删除' . $num . '条数据']);
                } else {
                    return json()->data(['code' => -1, 'msg' => '没有任何数据发生变化']);
                }
            } catch (\Exception $e) {
                Db::rollback();
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        }
    }

    /**
     * 会员购买日志列表
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function userBuyProductList(Request $request, Where $where, UsersBuyProductLog $usersBuyProductLogModel)
    {
        if ($request->isAjax()) {
            try {
                if ($account = $request->param('account', '', 'trim')) {
                    $userId = (int) Users::where('account', $account)->value('user_id');
                    $where['user_id'] = $userId;
                }
                if ($productId = $request->param('product_id', '', 'trim')) {
                    $where['product_id'] = $productId;
                }
                if ($time = $request->param('time', '', 'trim')) {
                    $times = explode(' - ', $time);
                    $startTime = strtotime($times[0]);
                    $endTime = strtotime($times[1]);
                    $where['add_time'] = ['between', [$startTime, $endTime]];
                }
                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');
                $list = $usersBuyProductLogModel::where($where)->order('id', 'desc')->limit($page * $pageSize, $pageSize)->select();
                $userIds = get_arr_column($list, 'user_id');
                $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
                $productNameArr = ProductModel::getProductNames();
                $levelLogList = [];
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['id'],
                        'amount' => $v['amount'],
                        'account' => isset($users[$v['user_id']]) ? $users[$v['user_id']] : '',
                        'produc_name' => isset($productNameArr[$v['product_id']]) ? $productNameArr[$v['product_id']] : '',
                        'add_time' => date('Y-m-d H:i:s', $v['add_time'])
                    ];
                    $levelLogList[] = $arr;
                }
                if (count($levelLogList) == 0) {
                    $data = [
                        'code' => -1,
                        'msg' => '没有日志'
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $levelLogList,
                        'count' => $usersBuyProductLogModel::where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询会员购买人物日志失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取到等级日志，请联系管理员',
                ];
                return json()->data($data);
            }
        } else {
            return view('product/user_buy_product/buy_list');
        }
    }

    /**
     * 添加会员购买人物日志
     * @param Request $request
     * @param Country $countryModel
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function addUserBuyProductInfo(Request $request, UsersBuyProductLog $usersBuyProductLogModel)
    {
        if ($request->isPost()) {
            $accont = $request->param('account', '', 'trim');
            $userInfo = Users::where('account', $accont)->field('user_id,account,mobile')->find();
            if ($userInfo['user_id'] <= 0) {
                $this->error($accont . '不存在');
            }
            try {
                $data['user_id'] = $userInfo['user_id'];
                $data['product_id'] = $request->param('product_id', '', 'intval');
                $data['amount'] = $request->param('amount', '', 'floatval');
                $data['add_time'] = time();
                $usersBuyProductLogModel->insertGetId($data);
                AdminLog::addLog('添加会员购买人物日志', $request->param());
                return json()->data(['code' => 1, 'msg' => '添加成功']);
            } catch (\Exception $e) {
                Log::write('添加会员购买人物日志失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            return view('product/user_buy_product/add_info');
        }
    }

    /**
     * 编辑会员购买人物日志
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function editUserBuyProductInfo(Request $request, UsersBuyProductLog $usersBuyProductLogModel)
    {
        $id = $request->param('id', '', 'intval');
        if ($id <= 0) {
            $this->error('非法操作');
        }
        $info = $usersBuyProductLogModel::where('id', $id)->find();
        if (empty($info)) {
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info['product_id'] = $request->param('product_id', '', 'intval');
                $info['amount'] = $request->param('amount', '', 'floatval');
                $info->save();
                AdminLog::addLog('修改会员购买人物日志', $request->param());
                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                Log::write('修改会员购买人物日志失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            return view('product/user_buy_product/edit_info', ['info' => $info]);
        }
    }

    /**
     * 删除会员购买人物日志
     */
    public function delUserBuyProductList(Request $request, UsersBuyProductLog $usersBuyProductLogModel)
    {
        if ($request->isPost()) {
            $id = $request->param('id');
            if (!$id) {
                return json(['code' => -1, 'msg' => '请选择']);
            }
            Db::startTrans();
            try {
                $arrId = explode(',', $id);
                $logList = $usersBuyProductLogModel::whereIn('id', $arrId)->select();
                if (count($logList) <= 0) {
                    return json()->data(['code' => -1, 'msg' => '此信息不支持该操作']);
                }
                $usersBuyProductLogModel::whereIn('id', $arrId)->delete();
                $data = $logList->toArray();
                $num = count($data);
                Db::commit();
                AdminLog::addLog('删除会员人物', $data);
                if ($num > 0) {
                    return json()->data(['code' => 1, 'msg' => '成功删除' . $num . '条数据']);
                } else {
                    return json()->data(['code' => -1, 'msg' => '没有任何数据发生变化']);
                }
            } catch (\Exception $e) {
                Db::rollback();
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        }
    }

}
