<?php

namespace app\admin\controller;

use app\common\model\grade\ServiceLog;
use think\Request;
use think\db\Where;
use app\common\model\grade\Service as ServiceModel;
use think\facade\Log;
use app\common\model\Users;
use app\common\model\AdminLog;

class Service extends Base
{
    /**
     * 代理等级参数设置
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function configList(Request $request, Where $where)
    {
        if ($request->isAjax()) {
            try {
                $configList = [];
                $list = ServiceModel::where($where)->select();

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
                    'code' => 1,
                    'data' => $configList,
                ];
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询代理等级列表失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取到代理等级信息',
                ];
                return json()->data($data);
            }

        } else {
            return view('service/config_list');
        }
    }

    /**
     * 编辑代理等级
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function editConfig(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = ServiceModel::getServiceInfoById($id);
        if (empty($info)) {
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {

                $info->name_cn = $request->param('name_cn');
                $info->color = $request->param('color');
                $info->per = $request->param('per', '', 'floatval');
                $info->level_id = $request->param('level_id', '', 'intval');
                $info->save();

                AdminLog::addLog('修改代理等级设置', $request->param());

                $info->_afterUpdate();

                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                Log::write('修改代理等级失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            return view('service/edit_config', ['info' => $info, 'levelNames' => get_level_name()]);
        }
    }

    /**
     * 会员代理等级日志
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function userServiceLogList(Request $request, Where $where)
    {
        if ($request->isAjax()) {
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
                $list = ServiceLog::where($where)
                    ->order('id', 'desc')
                    ->limit($page * $pageSize, $pageSize)->select();

                $userIds = get_arr_column($list, 'uid');

                $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
                $serviceNames = ServiceModel::getServiceNames();

                $serviceLogList = [];
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['id'],
                        'account' => isset($users[$v['uid']]) ? $users[$v['uid']] : '',
                        'old_level' => isset($serviceNames[$v['front_id']]) ? $serviceNames[$v['front_id']] : '',
                        'new_level' => isset($serviceNames[$v['new_id']]) ? $serviceNames[$v['new_id']] : '',
                        'add_time' => date('Y-m-d H:i:s', $v['add_time'])
                    ];

                    $serviceLogList[] = $arr;
                }
                if (count($serviceLogList) == 0) {
                    $data = [
                        'code' => -1,
                        'msg' => '没有代理等级日志'
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $serviceLogList,
                        'count' => ServiceLog::where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询会员代理等级日志失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取到代理等级日志，请联系管理员',
                ];
                return json()->data($data);
            }
        } else {
            return view('service/user_service_log_list');
        }
    }
}