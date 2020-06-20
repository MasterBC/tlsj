<?php

namespace app\admin\controller;

use think\db;
use think\Request;
use think\db\Where;
use think\facade\Log;
use app\common\model\AdminLog;
use app\common\model\Users;
use app\common\model\game\Game as GameModel;
use app\common\model\game\GameUser;

class Game extends Base
{

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct();
        $this->assign('gameNameArr', GameModel::getGameNames());
        $this->assign('statusDataArr', GameModel::$statusData);
        $this->assign('isTypeDataArr', GameModel::$isTypeData);
    }

    /**
     * 人物列表参数设置
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function configGameList(Request $request, Where $where, GameModel $gameModel)
    {
        if ($request->isAjax()) {
            try {
                $configList = [];
                $title = $request->param('gram_name', '', 'trim');
                $page = $request->get('p', '1', 'int') - 1;
                $pageSize = $request->get('p_num', '10', 'int');
                $list = $gameModel::where($where)->limit($page * $pageSize, $pageSize)->order('id desc')->select();
                foreach ($list as $v) {
                    $arr = [
                    'id' => $v['id'],
                    'gram_name' => $v['gram_name'],
                    'gram_logo' => $v['gram_logo'],
                    'gram_url' => $v['gram_url'],
                    'gram_mood' => $v['gram_mood'],
                    'is_type' => $gameModel::$isTypeData[$v['is_type']] ?? '',
                    'status' => $gameModel::$statusData[$v['status']] ?? ''
                    ];
                    $configList[] = $arr;
                }
                $data = [
                    'code' => 1,
                    'data' => $configList,
                    'count' => $gameModel::where($where)->count()
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
            return view('game/config_game/config_list');
        }
    }

    /**
     * 添加
     * @param Request $request
     * @param Country $countryModel
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function addGameConfig(Request $request, GameModel $gameModel)
    {
        if ($request->isPost()) {
            try {
                $data['gram_name'] = $request->param('gram_name', '', 'trim');
                $data['gram_mood'] = $request->param('gram_mood', '', 'trim');
                $data['gram_url'] = $request->param('gram_url', '', 'trim');
                $data['gram_logo'] = $request->param('gram_logo', '', 'trim');
                $data['gram_mood'] = $request->param('gram_mood', '', 'trim');
                $data['is_type'] = $request->param('is_type', '', 'intval');
                $data['status'] = $request->param('status', '', 'intval');
                $gameModel->insertGetId($data);
                AdminLog::addLog('添加游戏', $request->param());
                return json()->data(['code' => 1, 'msg' => '添加成功']);
            } catch (\Exception $e) {
                Log::write('添加游戏失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            return view('game/config_game/add_config');
        }
    }

    /**
     * 编辑修改人物
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function editGameConfig(Request $request, GameModel $gameModel)
    {
        $id = $request->param('id', '', 'intval');
        if ($id <= 0) {
            $this->error('非法操作');
        }
        $info = $gameModel::getGameInfoById($id);
        if (empty($info)) {
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info->gram_name = $request->param('gram_name', '', 'trim');
                $info->gram_mood = $request->param('gram_mood', '', 'trim');
                $info->gram_url = $request->param('gram_url', '', 'trim');
                $info->gram_logo = $request->param('gram_logo', '', 'trim');
                $info->gram_mood = $request->param('gram_mood', '', 'trim');
                $info->is_type = $request->param('is_type', '', 'intval');
                $info->status = $request->param('status', '', 'intval');
                $info->save();
                AdminLog::addLog('修改游戏参数', $request->param());
                $info->_afterUpdate();
                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                Log::write('修改游戏参数失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            return view('game/config_game/edit_config', ['info' => $info]);
        }
    }

    /**
     * 删除人物参数
     */
    public function delGameConfig(Request $request, GameModel $gameModel)
    {
        if ($request->isPost()) {
            $id = $request->param('id');
            if (!$id) {
                return json(['code' => -1, 'msg' => '请选择']);
            }
            Db::startTrans();
            try {
                $arrId = explode(',', $id);
                $logList = $gameModel::whereIn('id', $arrId)->select();
                if (count($logList) <= 0) {
                    return json()->data(['code' => -1, 'msg' => '此信息不支持该操作']);
                }
                $gameModel::whereIn('id', $arrId)->delete();
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
     * 会员点击游戏列表
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function userGameList(Request $request, Where $where, GameUser $gameUserModel)
    {
        if ($request->isAjax()) {
            try {
                if ($account = $request->param('account', '', 'trim')) {
                    $userId = (int) Users::where('account', $account)->value('user_id');
                    $where['user_id'] = $userId;
                }
                if ($time = $request->param('time', '', 'trim')) {
                    $times = explode(' - ', $time);
                    $startTime = strtotime($times[0]);
                    $endTime = strtotime($times[1]);
                    $where['add_time'] = ['between', [$startTime, $endTime]];
                }
                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');
                $list = $gameUserModel::where($where)->order('id', 'desc')->limit($page * $pageSize, $pageSize)->select();
                $userIds = get_arr_column($list, 'user_id');
                $users = Users::whereIn('user_id', $userIds)->column('account', 'user_id');
                $gameNameArr =  GameModel::getGameNames();
                $levelLogList = [];
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['id'],
                        'account' => isset($users[$v['user_id']]) ? $users[$v['user_id']] : '',
                        'gram_name' => isset($gameNameArr[$v['game_id']]) ? $gameNameArr[$v['game_id']] : '',
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
                        'count' => $gameUserModel::where($where)->count()
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
            return view('game/user_game/game_list');
        }
    }

    /**
     * 删除会员点击游戏日志
     */
    public function deluserGameList(Request $request, GameUser $gameUserModel)
    {
        if ($request->isPost()) {
            $id = $request->param('id');
            if (!$id) {
                return json(['code' => -1, 'msg' => '请选择']);
            }
            Db::startTrans();
            try {
                $arrId = explode(',', $id);
                $logList = $gameUserModel::whereIn('id', $arrId)->select();
                if (count($logList) <= 0) {
                    return json()->data(['code' => -1, 'msg' => '此信息不支持该操作']);
                }
                $gameUserModel::whereIn('id', $arrId)->delete();
                $data = $logList->toArray();
                $num = count($data);
                Db::commit();
                AdminLog::addLog('删除会员游戏日志', $data);
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
