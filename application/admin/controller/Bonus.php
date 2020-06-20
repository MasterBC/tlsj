<?php

namespace app\admin\controller;

use think\Request;
use think\db\Where;
use app\common\model\Bonus as BonusModel;
use think\facade\Log;
use app\common\model\BonusLog;
use app\common\model\Users;
use app\common\model\AdminLog;

class Bonus extends Base
{

    /**
     * 奖金参数设置
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function configList(Request $request, Where $where)
    {
        if ($request->isAjax()) {
            try {
                $bonusList = [];
                $list = BonusModel::where($where)->select();

                $moneyNames = get_money_name();
                $blockNames = get_block_name();

                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['id'],
                        'name' => $v['name_cn'],
                        'type' => $v['type'],
                        'sj' => $v['sj'],
                        'a_per' => $v['a_per'],
                        'b_per' => $v['b_per'],
                        'c_per' => $v['c_per'],
                        'd_per' => $v['d_per'],
                        'a_money_name' => isset($moneyNames[$v['a_mid']]) ? $moneyNames[$v['a_mid']] : '',
                        'b_money_name' => isset($moneyNames[$v['b_mid']]) ? $moneyNames[$v['b_mid']] : '',
                        'c_money_name' => isset($moneyNames[$v['c_mid']]) ? $moneyNames[$v['c_mid']] : '',
                        'd_block_name' => isset($blockNames[$v['d_bid']]) ? $blockNames[$v['d_bid']] : '',
                    ];

                    $bonusList[] = $arr;
                }
                $data = [
                    'code' => 1,
                    'data' => $bonusList,
                ];
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询奖项设置列表失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取到设置信息',
                ];
                return json()->data($data);
            }

        } else {
            return view('bonus/config_list');
        }
    }

    /**
     * 编辑奖项设置
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function editConfig(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = BonusModel::getInfoById($id);
        if (empty($info)) {
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {

                $info->name_cn = $request->param('name_cn');
                $info->type = $request->param('type', '', 'intval');
                $info->sj = $request->param('sj', '', 'intval');
                $info->a_mid = $request->param('a_mid', '', 'intval');
                $info->b_mid = $request->param('b_mid', '', 'intval');
                $info->c_mid = $request->param('c_mid', '', 'intval');
                $info->d_bid = $request->param('d_bid', '', 'intval');
                $info->a_per = $request->param('a_per', '', 'floatval');
                $info->b_per = $request->param('b_per', '', 'floatval');
                $info->c_per = $request->param('c_per', '', 'floatval');
                $info->d_per = $request->param('d_per', '', 'floatval');
                $info->save();

                AdminLog::addLog('修改奖项设置', $request->param());
                $info->_afterUpdate();

                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                Log::write('修改奖金设置失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            $moneyNames = get_money_name();
            $blockNames = get_block_name();
            return view('bonus/edit_config', ['info' => $info, 'moneyNames' => $moneyNames, 'blockNames' => $blockNames]);
        }
    }

    /**
     * 奖金日志列表
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function logList(Request $request, Where $where)
    {
        $bonusNames = get_bonus_name();
        if ($request->isAjax()) {
            try {
                if ($account = $request->param('account', '', 'trim')) {
                    $userId = (int)Users::where('account', $account)->value('user_id');
                    $where['uid'] = $userId;
                }
                if ($comeAccount = $request->param('come_account', '', 'trim')) {
                    $userId = (int)Users::where('account', $comeAccount)->value('user_id');
                    $where['come_uid'] = $userId;
                }
                if ($bonusId = $request->param('bonus_id', '', 'intval')) {
                    $where['bonus_id'] = $bonusId;
                }
                if ($time = $request->param('time', '', 'trim')) {
                    $times = explode(' - ', $time);
                    $startTime = strtotime($times[0]);
                    $endTime = strtotime($times[1]);
                    $where['add_time'] = ['between', [$startTime, $endTime]];
                }
                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');
                $list = BonusLog::where($where)->limit($page * $pageSize, $pageSize)->order('id', 'desc')->select();

                $logList = [];

                $userIds = array_unique(array_merge(get_arr_column($list, 'uid'), get_arr_column($list, 'come_uid')));
                $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');


                foreach ($list as $v) {
                    $arr = [
                        'account' => isset($users[$v['uid']]) ? $users[$v['uid']] : '',
                        'come_account' => isset($users[$v['come_uid']]) ? $users[$v['come_uid']] : '',
                        'id' => $v['id'],
                        'money' => $v['money'],
                        'add_time' => date('Y-m-d H:i:s', $v['add_time']),
                        'bonus_name' => isset($bonusNames[$v['bonus_id']]) ? $bonusNames[$v['bonus_id']] : '',
                        'status' => $v['status'],
                        'out_time' => date('Y-m-d H:i:s', $v['out_time'])
                    ];
                    switch ($arr['status']) {
                        case 1:
                            $arr['status'] = '待结算';
                            break;
                        case 2:
                            $arr['status'] = '结算中';
                            break;
                        case 9:
                            $arr['status'] = '已结算 ' . $arr['out_time'];
                            break;
                    }

                    $logList[] = $arr;
                }

                if (count($logList) == 0) {
                    $data = [
                        'code' => -1,
                        'msg' => '没有奖金日志'
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $logList,
                        'count' => BonusLog::where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询奖金记录失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取到奖金日志，请联系管理员',
                ];
                return json()->data($data);
            }
        } else {
            $assignData = [
                'bonusNames' => $bonusNames
            ];
            return view('bonus/log_list', $assignData);
        }
    }
}