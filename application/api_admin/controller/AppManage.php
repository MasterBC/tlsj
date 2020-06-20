<?php

namespace app\api_admin\controller;

use app\api_admin\response\ReturnCode;
use app\common\model\AdminLog;
use app\common\model\PhoneRecharge as PhoneRechargeModel;
use app\common\model\block\Block as BlockModel;
use app\common\model\money\Money as MoneyModel;
use app\common\model\OilCardRecharge as OilCardRechargeModel;
use app\common\model\turn\TurnLog;
use app\common\model\turn\Turn as TurnModel;
use app\common\model\claw\ClawLog;
use app\common\model\claw\Claw as ClawModel;
use think\Request;
use think\db\Where;
use think\facade\Log;
use app\common\model\Users;

class AppManage extends Base
{
    /**
     * 话费充值记录
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function phoneRechargeLogList(Request $request, Where $where)
    {
        try {
            if ($account = $request->param('account', '', 'trim')) {
                $userId = (int)Users::where('account', $account)->value('user_id');
                $where['user_id'] = $userId;
            }
            if ($sporderId = $request->param('sporder_id', '', 'trim')) {
                $where['sporder_id'] = $sporderId;
            }

            if ($time = $request->param('time', '', 'trim')) {
                $times = explode(' - ', $time);
                $startTime = strtotime($times[0]);
                $endTime = strtotime($times[1]);
                $where['add_time'] = ['between', [$startTime, $endTime]];
            }

            $page = $request->get('p', '1', 'intval') - 1;
            $pageSize = $request->get('p_num', '10', 'intval');
            $list = PhoneRechargeModel::where($where)
                ->order('id', 'desc')
                ->limit($page * $pageSize, $pageSize)->select();

            $changeLogList = [];
            $userIds = get_arr_column($list, 'user_id');
            $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
            $blockNames = BlockModel::getBlockNames();
            foreach ($list as $v) {
                $arr = $v;
                $arr['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
                $arr['user_id'] = $users[$v['user_id']] ?? '';
                $arr['bid'] = $blockNames[$v['bid']];
                $changeLogList[] = $arr;
            }
            if (count($changeLogList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '没有话费记录'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $changeLogList,
                    'count' => PhoneRechargeModel::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询话费记录失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到话费记录，请联系管理员');
        }
    }

    /**
     * 油卡充值记录
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function oilCardRechargeLogList(Request $request, Where $where)
    {
        try {
            if ($account = $request->param('account', '', 'trim')) {
                $userId = (int)Users::where('account', $account)->value('user_id');
                $where['user_id'] = $userId;
            }
            if ($sporderId = $request->param('sporder_id', '', 'trim')) {
                $where['sporder_id'] = $sporderId;
            }

            if ($time = $request->param('time', '', 'trim')) {
                $times = explode(' - ', $time);
                $startTime = strtotime($times[0]);
                $endTime = strtotime($times[1]);
                $where['add_time'] = ['between', [$startTime, $endTime]];
            }

            $page = $request->get('p', '1', 'intval') - 1;
            $pageSize = $request->get('p_num', '10', 'intval');
            $list = OilCardRechargeModel::where($where)
                ->order('id', 'desc')
                ->limit($page * $pageSize, $pageSize)->select();

            $changeLogList = [];
            $userIds = get_arr_column($list, 'user_id');
            $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
            $blockNames = BlockModel::getBlockNames();
            foreach ($list as $v) {
                $arr = $v;
                $arr['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
                $arr['user_id'] = $users[$v['user_id']] ?? '';
                $arr['bid'] = $blockNames[$v['bid']];
                $changeLogList[] = $arr;
            }
            if (count($changeLogList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '没有油卡记录'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $changeLogList,
                    'count' => OilCardRechargeModel::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询油卡记录失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到油卡记录，请联系管理员');
        }
    }


    /**
     * 转抽抽奖参数
     * @param Where $where
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function turnConfigList(Where $where)
    {
        try {
            $list = TurnModel::where($where)->select();

            $statusData = [1 => '启用', 2 => '关闭'];

            $changeConfigList = [];
            foreach ($list as $v) {
                $arr = $v;
                $arr['img'] = get_img_domain() . $v['img'];
                $arr['status'] = $statusData[$v['status']] ?? '';

                $changeConfigList[] = $arr;
            }
            if (count($changeConfigList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '未配置参数，请联系管理员'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $changeConfigList
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询参数失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到配置，请联系管理员');
        }
    }

    /**
     * 编辑转账抽奖参数
     * @param Request $request
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editTurnConfig(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = TurnModel::where('id', $id)->find();
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info->allowField([
                    'name', 'img', 'num', 'is_per', 'day_total', 'status', 'message'
                ])->save($request->param());
                AdminLog::addLog('修改抽奖参数', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改抽奖配置失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info
            ]);
        }
    }

    /**
     * 转盘抽奖记录
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function turnLogList(Request $request, Where $where)
    {
        $turnNames = TurnModel::where(['status' => 1])->column('id, name');
        if ($request->param('is_get_data') == true) {
            try {
                if ($account = $request->param('account', '', 'trim')) {
                    $userId = (int)Users::where('account', $account)->value('user_id');
                    $where['uid'] = $userId;
                }
                if ($cId = $request->param('t_id', '', 'intval')) {
                    $where['t_id'] = $cId;
                }
                if ($time = $request->param('time', '', 'trim')) {
                    $times = explode(' - ', $time);
                    $startTime = strtotime($times[0]);
                    $endTime = strtotime($times[1]);
                    $where['add_time'] = ['between', [$startTime, $endTime]];
                }
                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');
                $list = TurnLog::where($where)
                    ->order('id', 'desc')
                    ->limit($page * $pageSize, $pageSize)->select();

                $changeLogList = [];
                $userIds = get_arr_column($list, 'uid');

                $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
                $moneyNames = MoneyModel::getMoneyNames();
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['id'],
                        'add_time' => date('Y-m-d H:i:s', $v['add_time']),
                        'uid' => $users[$v['uid']] ?? '',
                        't_id' => $turnNames[$v['t_id']] ?? '',
                        'mid' => $moneyNames[$v['mid']] ?? '',
                        'money' => $v['money'],
                    ];
                    $changeLogList[] = $arr;
                }
                if (count($changeLogList) == 0) {
                    $data = [
                        'code' => ReturnCode::ERROR_CODE,
                        'msg' => '没有记录'
                    ];
                } else {
                    $data = [
                        'code' => ReturnCode::SUCCESS_CODE,
                        'data' => $changeLogList,
                        'count' => TurnLog::where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询记录失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '查询抽奖记录失败，请联系管理员');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'turnNames' => $turnNames
            ]);
        }
    }


    /**
     * 夹娃娃参数列表
     * @param Where $where
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function clawConfigList(Where $where)
    {
        try {
            $list = ClawModel::where($where)->select();

            $statusData = [1 => '启用', 2 => '关闭'];

            $changeConfigList = [];
            foreach ($list as $v) {
                $arr = $v;
                $arr['img'] = get_img_domain() . $v['img'];
                $arr['status'] = $statusData[$v['status']] ?? '';

                $changeConfigList[] = $arr;
            }
            if (count($changeConfigList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '未配置参数，请联系管理员'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $changeConfigList
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询参数失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到配置，请联系管理员');
        }
    }

    /**
     * 编辑
     * @param Request $request
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editClawConfig(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = ClawModel::where('id', $id)->find();
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info->allowField([
                    'name', 'img', 'num', 'is_per', 'day_total', 'status', 'message'
                ])->save($request->param());
                AdminLog::addLog('修改参数', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改抽奖配置失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info
            ]);
        }
    }


    /**
     * 转盘抽奖记录
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function clawLogList(Request $request, Where $where)
    {
        $clawNames = ClawModel::where(['status' => 1])->column('id, name');
        if ($request->param('is_get_data') == true) {
            try {
                if ($account = $request->param('account', '', 'trim')) {
                    $userId = (int)Users::where('account', $account)->value('user_id');
                    $where['uid'] = $userId;
                }
                if ($cId = $request->param('c_id', '', 'intval')) {
                    $where['c_id'] = $cId;
                }
                if ($time = $request->param('time', '', 'trim')) {
                    $times = explode(' - ', $time);
                    $startTime = strtotime($times[0]);
                    $endTime = strtotime($times[1]);
                    $where['add_time'] = ['between', [$startTime, $endTime]];
                }
                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');
                $list = ClawLog::where($where)
                    ->order('id', 'desc')
                    ->limit($page * $pageSize, $pageSize)->select();

                $changeLogList = [];
                $userIds = get_arr_column($list, 'uid');

                $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
                $moneyNames = MoneyModel::getMoneyNames();
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['id'],
                        'add_time' => date('Y-m-d H:i:s', $v['add_time']),
                        'uid' => $users[$v['uid']] ?? '',
                        'c_id' => $clawNames[$v['c_id']] ?? '',
                        'mid' => $moneyNames[$v['mid']] ?? '',
                        'money' => $v['money'],
                    ];
                    $changeLogList[] = $arr;
                }
                if (count($changeLogList) == 0) {
                    $data = [
                        'code' => ReturnCode::ERROR_CODE,
                        'msg' => '没有记录'
                    ];
                } else {
                    $data = [
                        'code' => ReturnCode::SUCCESS_CODE,
                        'data' => $changeLogList,
                        'count' => ClawLog::where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询记录失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未查询到记录，请联系管理员');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'clawNames' => $clawNames
            ]);
        }
    }
}