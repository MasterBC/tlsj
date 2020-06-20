<?php

namespace app\admin\controller;

use think\db;
use think\Request;
use think\db\Where;
use think\facade\Log;
use app\common\model\AdminLog;
use app\common\model\UsersRankMoney as UsersRankMoneyModel;

class UsersRankMoney extends Base
{

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct();
    }

    /**
     * 收入阶段列表
     * @param Request $request
     * @param Where $where
     * @return \think\response\Json|\think\response\View
     */
    public function configRankMoneyList(Request $request, Where $where, UsersRankMoneyModel $usersRankMoneyModel)
    {
        if ($request->isAjax()) {
            try {
                $configList = [];
                $title = $request->param('title', '', 'trim');
                $page = $request->get('p', '1', 'int') - 1;
                $pageSize = $request->get('p_num', '10', 'int');
                $list = $usersRankMoneyModel::where($where)->limit($page * $pageSize, $pageSize)->order('sort desc')->select();
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['id'],
                        'target_title' => $v['target_title'],
                        'target_money' => $v['target_money'],
                        'up_per' => $v['up_per'],
                        'sort' => $v['sort'],
                    ];
                    $configList[] = $arr;
                }
                $data = [
                    'code' => 1,
                    'data' => $configList,
                    'count' => $usersRankMoneyModel::where($where)->count()
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
            return view('user/users_rank_money/config_list');
        }
    }

    /**
     * 添加收入阶段参数
     * @param Request $request
     * @param Country $countryModel
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function addRankMoneyConfig(Request $request, UsersRankMoneyModel $usersRankMoneyModel)
    {
        if ($request->isPost()) {
            try {
                $data['target_title'] = $request->param('target_title', '', 'trim');
                $data['target_money'] = $request->param('target_money', '', 'intval');
                $data['up_per'] = $request->param('up_per', '', 'floatval');
                $data['sort'] = $request->param('sort', '', 'floatval');
                $usersRankMoneyModel->insertGetId($data);
                AdminLog::addLog('添加收入阶段参数', $request->param());
                return json()->data(['code' => 1, 'msg' => '添加成功']);
            } catch (\Exception $e) {
                Log::write('添加收入阶段参数失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            return view('user/users_rank_money/add_config');
        }
    }

    /**
     * 编辑修改人物
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function editRankMoneyConfig(Request $request, UsersRankMoneyModel $usersRankMoneyModel)
    {
        $id = $request->param('id', '', 'intval');
        if ($id <= 0) {
            $this->error('非法操作');
        }
        $info = $usersRankMoneyModel::where('id', $id)->find();
        if (empty($info)) {
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info->target_title = $request->param('target_title', '', 'trim');
                $info->target_money = $request->param('target_money', '', 'intval');
                $info->sort = $request->param('sort', '', 'intval');
                $info->up_per = $request->param('up_per', '', 'floatval');
                $info->save();
                AdminLog::addLog('修改收入阶段参数', $request->param());
                $info->_afterUpdate();
                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                Log::write('修改收入阶段参数失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            return view('user/users_rank_money/edit_config', ['info' => $info]);
        }
    }

    /**
     * 删除人物参数
     */
    public function delRankMoneyConfig(Request $request, UsersRankMoneyModel $usersRankMoneyModel)
    {
        if ($request->isPost()) {
            $id = $request->param('id');
            if (!$id) {
                return json(['code' => -1, 'msg' => '请选择']);
            }
            Db::startTrans();
            try {
                $arrId = explode(',', $id);
                $logList = $usersRankMoneyModel::whereIn('id', $arrId)->select();
                if (count($logList) <= 0) {
                    return json()->data(['code' => -1, 'msg' => '此信息不支持该操作']);
                }
                $usersRankMoneyModel::whereIn('id', $arrId)->delete();
                $data = $logList->toArray();
                $num = count($data);
                Db::commit();
                AdminLog::addLog('删除收入阶段参数', $data);
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
