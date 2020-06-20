<?php

namespace app\api_admin\controller;

use app\api_admin\response\ReturnCode;
use app\common\model\block\BlockCrowd;
use app\common\model\block\BlockCrowdUser;
use think\Request;
use think\db\Where;
use think\facade\Log;
use app\common\model\Users;
use app\common\model\AdminLog;
use \app\common\model\block\Block;

class Crowd extends Base
{
    /**
     * 众筹参数列表
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function configList(Where $where)
    {
        try {
            $list = BlockCrowd::where($where)->select();

            $changeConfigList = [];
            $blockNames = Block::getBlockNames();
            foreach ($list as $v) {
                $arr = $v;
                $arr['wallet_name'] = $blockNames[$v['bid']] ?? '';
                $arr['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
                $arr['status_msg'] = BlockCrowd::$status[$v['status']] ?? '';

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
            Log::write('查询钱包众筹参数失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到配置，请联系管理员');
        }
    }

    /**
     * 添加众筹参数
     * @param Request $request
     * @param BlockCrowd $blockCrowdModel
     * @return \think\response\Json|\think\response\View
     */
    public function addConfig(Request $request, BlockCrowd $blockCrowdModel)
    {
        try {
            $data = $request->param();
            $data['add_time'] = time();
            $blockCrowdModel->allowField([
                'status', 'now_price', 'web_total', 'yg_num', 'user_total', 'per', 'bid', 'add_time'
            ])->save($data);
            AdminLog::addLog('添加钱包众筹参数', $request->param(), $this->adminUser['admin_id']);
        } catch (\Exception $e) {
            Log::write('添加众筹配置失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '添加失败');
        }
        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '添加成功');
    }

    /**
     * @param Request $request
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editConfig(Request $request)
    {
        $id = $request->param('id', '', 'intval');
        $info = BlockCrowd::getCrowdInfoById($id);
        if (empty($info)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info->allowField([
                    'status', 'now_price', 'web_total', 'yg_num', 'user_total', 'per', 'bid'
                ])->save($request->param());
                AdminLog::addLog('修改钱包众筹参数', $request->param(), $this->adminUser['admin_id']);
            } catch (\Exception $e) {
                Log::write('修改众筹配置失败: ' . $e->getMessage(), 'error');
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '修改失败');
            }
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '修改成功');
        } else {
            $blockNames = Block::getBlockNames();
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                'info' => $info,
                'blockNames' => $blockNames
            ]);
        }
    }

    /**
     * 众筹记录
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function logList(Request $request, Where $where)
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
            $list = BlockCrowdUser::where($where)
                ->order('id', 'desc')
                ->limit($page * $pageSize, $pageSize)->select();

            $logList = [];
            $userIds = array_unique(get_arr_column($list, 'uid'));

            $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
            $blockNames = Block::getBlockNames();
            foreach ($list as $v) {
                $arr = [
                    'id' => $v['id'],
                    'account' => $users[$v['uid']] ?? '',
                    'change_time' => date('Y-m-d H:i:s', $v['add_time']),
                    'wallet_name' => $blockNames[$v['bid']] ?? '',
                    'num' => $v['num'],
                    'price' => $v['price'],
                    'total' => $v['total']
                ];
                $logList[] = $arr;
            }
            if (count($logList) == 0) {
                $data = [
                    'code' => ReturnCode::ERROR_CODE,
                    'msg' => '没有众筹记录'
                ];
            } else {
                $data = [
                    'code' => ReturnCode::SUCCESS_CODE,
                    'data' => $logList,
                    'count' => BlockCrowdUser::where($where)->count()
                ];
            }
            return json()->data($data);
        } catch (\Exception $e) {
            Log::write('查询众筹记录失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '未获取到众筹记录，请联系管理员');
        }
    }
}