<?php

namespace app\admin\controller;

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
        if ($request->isAjax()) {
            try {
                $configList = [];
                $list = LevelModel::where($where)->select();

                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['level_id'],
                        'name' => $v['name_cn'],
                        'logo' => $v['logo'],
                        'amount' =>  $v['add_mid_num'] . ' - ' . $v['out_mid_num'],
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
                Log::write('查询等级列表失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取到等级信息',
                ];
                return json()->data($data);
            }
        } else {
            return view('level/config_list');
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
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info->name_cn = $request->param('name_cn');
                $info->color = $request->param('color');
                 $info->logo = $request->param('logo');
                $info->out_mid_num = $request->param('out_mid_num', '', 'intval');
                $info->add_mid_num = $request->param('add_mid_num', '', 'intval');
                $info->save();
                AdminLog::addLog('修改等级设置', $request->param());
                $info->_afterUpdate();
                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                Log::write('修改等级失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            return view('level/edit_config', ['info' => $info]);
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
        if ($request->isAjax()) {
            try {
                if ($account = $request->param('account', '', 'trim')) {
                    $userId = (int) Users::where('account', $account)->value('user_id');
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
                $levelNames = get_level_name();

                $levelLogList = [];
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['id'],
                        'account' => isset($users[$v['uid']]) ? $users[$v['uid']] : '',
                        'old_level' => isset($levelNames[$v['front_id']]) ? $levelNames[$v['front_id']] : '',
                        'new_level' => isset($levelNames[$v['new_id']]) ? $levelNames[$v['new_id']] : '',
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
                        'count' => LevelLog::where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询会员等级日志失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '未获取到等级日志，请联系管理员',
                ];
                return json()->data($data);
            }
        } else {
            return view('level/user_level_log_list');
        }
    }

}
