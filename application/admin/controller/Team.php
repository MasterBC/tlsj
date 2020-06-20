<?php

namespace app\admin\controller;

use app\common\model\branch\UsersBranchYj;
use think\db\Where;
use think\Request;
use app\common\model\Users;
use app\common\model\grade\Level;
use app\common\model\branch\UsersBranch;

class Team extends Base
{
    /**
     * 直推树状图
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function tjTree(Request $request, Where $where)
    {
        if ($request->isAjax()) {
            $data = [];
            $account = $request->param('account');
            if ($account) {
                $where['account'] = $account;
            } else {
                $tjrId = $request->param('tree_id', '', 'intval');
                $where['tjr_id'] = $tjrId;
            }

            $userList = Users::where($where)->field('user_id,account,tjr_id,reg_time,jh_time')->select();

            if (!$userList) {
                $data = [
                    'status' => [
                        'code' => -1,
                        'message' => '会员不存在'
                    ]
                ];
                return json()->data($data);
            }

            foreach ($userList as $v) {
                $arr = [
                    'id' => $v['user_id'],
                    'isLast' => false,
                    'title' => '账号：' . $v['account'],
                    'level' => 1,
                    'parentId' => $v['tjr_id'],
                    'children' => [],
                    'basicData' => [
                        'dataInfo' => 'ID：' . $v['user_id'] . ' 注册时间：' . date('Y-m-d H:i:s', $v['reg_time']) . ' 激活时间：' . date('Y-m-d H:i:s', $v['jh_time']),
                    ]
                ];
                $tjrNum = $v->getTjrNum();
                if ($tjrNum == 0) {
                    $arr['isLast'] = true;
                }
                $data[] = $arr;
            }


            if (!$data) {
                $data = [
                    'status' => [
                        'code' => -1,
                        'message' => '该账号未推荐会员'
                    ]
                ];
                return json()->data($data);
            }

            $data = [
                'status' => [
                    'code' => 200,
                    'message' => '操作成功'
                ],
                'data' => $data
            ];

            return json()->data($data);
        }

        return view('team/tj_tree');
    }

    /**
     * 直推网络图
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function tjNetWork(Request $request)
    {
        if ($request->isAjax()) {
            $account = $request->param('account', '', 'trim');
            if ($account != '' && $request->param('is_account') == 1) {
                $userInfo = Users::where('account', $account)->field('level,account,user_id,tjr_id')->find();
                if (empty($userInfo)) {
                    return json()->data(['code' => -1, 'msg' => '该会员不存在']);
                }
            } else if($account != '') {
                $userInfo = Users::where('user_id', $account)->field('level,account,user_id,tjr_id')->find();
                if (empty($userInfo)) {
                    return json()->data(['code' => -1, 'msg' => '该会员不存在']);
                }
            } else {
                $userInfo = Users::where('user_id', 1)->find();
            }
            $res = Users::getUserNetworkList($userInfo);
            return json()->data([
                'code' => 1,
                'data' => $res
            ]);
        }

        $levels = Level::getLevelField('level_id,name_cn,color');

        $assignData = [
            'levels' => $levels,
            'firstUsers' => Users::where('tjr_id', 0)->field('user_id,account')->select()
        ];

        return view('team/tj_network', $assignData);
    }

    /**
     * 获取会员推荐网络图信息详情
     * @param Request $request
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTjNetworkDetail(Request $request)
    {
        if ($request->isPost()) {

            $userId = $request->param('id', '', 'intval');

            $userInfo = Users::where('user_id', $userId)->field('user_id,tjr_path')->find();

            $data = [
                'zt_num' => Users::getTjrUserNum($userInfo['user_id']),
                'team_num' => Users::getTeamUserNum($userInfo)
            ];

            return json()->data([
                'code' => 1,
                'data' => $data
            ]);
        }
    }

    /**
     * 接点树状图
     * @param Request $request
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function jdTree(Request $request)
    {
        if ($request->isAjax()) {
            $where = [];
            $account = $request->param('account');
            if ($account) {
                $user = Users::where('account', $account)->field('user_id')->find();
                $where['uid'] = $user['user_id'];
            } else {
                $jdrId = $request->param('tree_id', 0, 'intval');
                $where['jdr_id'] = $jdrId;
            }

            $userList = UsersBranch::withJoin(['userInfo' => function ($query) {
                $query->field('reg_time,jh_time,account,user_id');
            }])->field('id,uid,jdr_id,position')->where($where)->select();

            if (!$userList) {
                $data = [
                    'status' => [
                        'code' => -1,
                        'message' => '该会员不存在'
                    ]
                ];
                return json()->data($data);
            }

            $data = [];
            foreach ($userList as $v) {
                $arr = [
                    'id' => $v['id'],
                    'isLast' => false,
                    'level' => 1,
                    'parentId' => $v['jdr_id'],
                    'children' => [],
                    'basicData' => [
                        'dataInfo' => 'ID：' . $v['user_id'] . ' 注册时间：' . date('Y-m-d H:i:s', $v['reg_time']) . ' 激活时间：' . date('Y-m-d H:i:s', $v['jh_time']),
                    ]
                ];

                if ($v['jdr_id'] == 0) {
                    $arr['title'] = '账号：' . $v['account'];
                } else {
                    $arr['title'] = branch_region($v["position"]) . ' - 账号：' . $v['account'];
                }

                $jdrNum = UsersBranch::where('jdr_id', $v['id'])->count();
                if ($jdrNum == 0) {
                    $arr['isLast'] = true;
                }
                $data[] = $arr;
            }

            if (!$data) {
                $data = [
                    'status' => [
                        'code' => -1,
                        'message' => '该账号未接点会员'
                    ]
                ];
                return json()->data($data);
            }

            $data = [
                'status' => [
                    'code' => 200,
                    'message' => '操作成功'
                ],
                'data' => $data
            ];
            return json()->data($data);
        }

        return view('team/jd_tree');
    }

    /**
     * 接点网络图
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function jdNetwork(Request $request)
    {
        $firstUser = Users::find();
        if ($request->isAjax()) {
            $account = $request->param('account', '', 'trim');
            if ($account != '' && $request->param('is_account') == 1) {
                $userInfo = Users::where('account', $account)->field('level,account,user_id')->find();
                if (empty($userInfo)) {
                    return json()->data(['code' => -1, 'msg' => '该会员不存在']);
                }
            }elseif($account != '') {
                $userInfo = UsersBranch::alias('ub')
                    ->join('Users u', 'u.user_id = ub.uid')
                    ->where('ub.id', $account)
                    ->find();
//                $userInfo = Users::where('account', $account)->field('level,account,user_id')->find();
                if (empty($userInfo)) {
                    return json()->data(['code' => -1, 'msg' => '该会员不存在']);
                }
            } else {
                $userInfo = $firstUser;
            }
            $res = UsersBranch::getUserNetworkList($userInfo);

            $usersBranchModel = new UsersBranch();

            $leftNextJdrId = $usersBranchModel->getLastBranchId(1, 1);
            $leftNextUid = $usersBranchModel->where(['id' => $leftNextJdrId])->field('uid')->find();
            $leftNextUAccount = Users::where(['user_id' => $leftNextUid['uid']])->value('account');
            $rightNextJdrId = $usersBranchModel->getLastBranchId(1, 2);
            $rightNextUid = $usersBranchModel->where(['id' => $rightNextJdrId])->field('uid')->find();
            $rightNextUAccount = Users::where(['user_id' => $rightNextUid['uid']])->value('account');
            return json()->data([
                'code' => 1,
                'data' => $res,
                'left_next' => $leftNextUAccount,
                'right_next' => $rightNextUAccount,
            ]);
        }

        $levels = Level::getLevelField('level_id,name_cn,color');

        $assignData = [
            'levels' => $levels,
            'firstUser' => $firstUser,
            'firstBranch' => UsersBranch::with(['userInfo' => function ($query) {
                $query->field('user_id,account');
            }])->where('jdr_id', 0)->field('uid,id')->select()
        ];

        return view('team/jd_network', $assignData);
    }

    /**
     * 获取会员接点信息详情
     * @param Request $request
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserBranchDetail(Request $request)
    {
        if ($request->isPost()) {

            $branchId = $request->param('id', '', 'intval');

            $data = [];
            $brNum = 2;
            for ($i = 1; $i <= $brNum; $i++) {
                $yjInfo = UsersBranchYj::getYjInfoByBranchIdAndPos($branchId, $i);
                $data[$i] = [
                    'num' => UsersBranch::getBranchNumByPosition($branchId, $i),
                    'total_yj' => (float)$yjInfo['total'],
                    'new_yj' => (float)$yjInfo['new'],
                    'out_yj' => (float)$yjInfo['out'],
                    'pos' => branch_region($i) . '区'
                ];
            }

            return json()->data([
                'code' => 1,
                'data' => $data
            ]);
        }
    }
}