<?php

namespace app\api_admin\controller;

use app\api_admin\response\ReturnCode;
use think\Request;
use think\db\Where;
use app\common\model\grade\Level as LevelModel;
use think\facade\Log;
use app\common\model\Users;
use app\common\model\grade\LevelLog;
use app\common\model\AdminLog;

class Level extends Base
{

    /**
     * 等级参数设置
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function configList(Request $request, Where $where)
    {
        try {
            $configList = [];
            $list = LevelModel::where($where)->select();

            foreach ($list as $v) {
                $arr = [
                    'id' => $v['level_id'],
                    'name' => $v['name_cn'],
                    'amount' => $v['amount'],
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
            Log::write('查询等级列表失败: ' . $e->getMessage(), 'error');

            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到等级信息');
        }
    }

    /**
     * 编辑等级
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function editConfig(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = LevelModel::getLevelInfoById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }
        if ($request->isPost()) {
            try {

                $info->name_cn = $request->param('name_cn');
                $info->color = $request->param('color');
                $info->amount = $request->param('amount', '', 'floatval');
                $info->save();

                AdminLog::addLog('修改等级设置', $request->param(), $this->adminUser['admin_id']);

                $info->_afterUpdate();

                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');
            } catch (\Exception $e) {
                Log::write('修改等级失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '操作失败');
            }
        } else {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info
            ]);
        }
    }

    /**
     * 会员等级日志
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function userLevelLogList(Request $request, Where $where)
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
            $list = LevelLog::where($where)
                ->order('id', 'desc')
                ->limit($page * $pageSize, $pageSize)->select();

            $userIds = get_arr_column($list, 'uid');

            $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
            $levelNames = LevelModel::getLevelNames();

            $levelLogList = [];
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'account' => $users[$v['uid']] ?? '',
                    'old_level' => $levelNames[$v['front_id']] ?? '',
                    'new_level' => $levelNames[$v['new_id']] ?? '',
                    'add_time' => date('Y-m-d H:i:s', $v['add_time'])
                ];

                $levelLogList[] = $arr;
            }
            if (count($levelLogList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '没有等级日志'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $levelLogList,
                    'count' => LevelLog::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询会员等级日志失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到等级日志，请联系管理员');
        }
    }
}