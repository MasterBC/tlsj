<?php

namespace app\api_admin\controller;

use app\api_admin\response\ReturnCode;
use app\common\model\grade\AgentLog;
use app\common\model\grade\Agent as AgentModel;
use think\Request;
use think\db\Where;
use think\facade\Log;
use app\common\model\Users;
use app\common\model\AdminLog;

class Agent extends Base
{
    /**
     * 报单等级参数设置
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function configList(Request $request, Where $where)
    {
        try {
            $configList = [];
            $list = AgentModel::where($where)->select();

            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'name' => $v['name_cn'],
                    'per' => $v['per'],
                    'level_name' => $v['level_id'] ? get_level_name($v['level_id']) : '不限制',
                    'color' => $v['color']
                ];

                $configList[] = $arr;
            }
            $data = [
                'code' => ReturnCode::SUCCESS_CODE,
                'data' => $configList,
            ];
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询代理等级列表失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到代理等级信息');
        }
    }

    /**
     * 编辑报单等级
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function editConfig(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = AgentModel::getAgentInfoById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }
        if ($request->isPost()) {
            try {

                $info->name_cn = $request->param('name_cn');
                $info->color = $request->param('color');
                $info->per = $request->param('per', '', 'floatval');
                $info->level_id = $request->param('level_id', '', 'intval');
                $info->save();

                AdminLog::addLog('修改报单等级设置', $request->param(), $this->adminUser['admin_id']);

                $info->_afterUpdate();

                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
            } catch (\Exception $e) {
                Log::write('修改报单等级失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '操作失败');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info,
                'levelNames' => \app\common\model\grade\Level::getLevelNames()
            ]);
        }
    }

    /**
     * 会员报单等级日志
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function userAgentLogList(Request $request, Where $where)
    {
        try {
            if ($account = $request->param('account', '', 'trim')) {
                $userId = (int)Users::where('account', $account)->value('user_id');
                $where['uid'] = $userId;
            }
            if ($time = $request->param('time', '', 'trim')) {
                $times = explode(' - ', $time);
                $startTime = strtotime($times[0]);
                $endTime = strtotime($times[1]);
                $where['add_time'] = ['between', [$startTime, $endTime]];
            }
            $page = $request->get('p', '1', 'intval') - 1;
            $pageSize = $request->get('p_num', '10', 'intval');
            $list = AgentLog::where($where)
                ->order('id', 'desc')
                ->limit($page * $pageSize, $pageSize)->select();

            $userIds = get_arr_column($list, 'uid');

            $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
            $agentNames = AgentModel::getAgentNames();

            $agentLogList = [];
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'account' => $users[$v['uid']] ?? '',
                    'old_level' => $agentNames[$v['front_id']] ?? '',
                    'new_level' => $agentNames[$v['new_id']] ?? '',
                    'add_time' => date('Y-m-d H:i:s', $v['add_time'])
                ];

                $agentLogList[] = $arr;
            }
            if (count($agentLogList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '没有报单等级日志'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $agentLogList,
                    'count' => AgentLog::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询会员报单等级日志失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到报单等级日志，请联系管理员');
        }
    }
}