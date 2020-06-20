<?php

namespace app\wap\controller;

use think\Request;
use app\common\model\turn\Turn as TurnModel;
use app\common\model\turn\TurnLog;
use app\common\model\Users;
use think\Db;
use app\common\model\money\UsersMoney;
use app\common\model\block\UsersBlock;

/**
 * Class Quotation
 * @package app\wap\controller
 */
class Turn extends Base
{

    /**
     * 首页
     * @param Request $request
     * @param TurnModel $turnModel
     * @param TurnLog $turnLog
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function turnIndex(Request $request, TurnModel $turnModel, TurnLog $turnLog)
    {
        //奖品数组
        $prizeArr = $turnModel->where('status', 1)->column("*",'id');
        if ($request->isAjax()) {

            if ($this->user['turn_num'] <= 0) {
                return json(['code' => -1, 'msg' => '转盘劵不足']);
            }

            $arr = [];

            foreach ($prizeArr as $key => $val) {
                $arr[$val['id']] = $val['is_per']; //概率数组
            }
            $rid = $this->getRand($arr); //根据概率获取奖项id
            if (!isset($prizeArr[$rid])) {
                return json(['code' => -2, 'msg'=>$rid]);
            }
            $res['yes'] = $prizeArr[$rid]; //中奖项
            unset($prizeArr[$rid]); //将中奖项从数组中剔除，剩下未中奖项
            shuffle($prizeArr); //打乱数组顺序
            $turnLogData = [];
            $turnLogData['add_time'] = time();
            $turnLogData['uid'] = $this->user['user_id'];
            $turnLogData['t_id'] = $res['yes']['id'];
            $turnLogData['mid'] = $res['yes']['mid'];
            $turnLogData['money'] = $res['yes']['m_num'];
            $turnLogData['bid'] = intval($res['yes']['bid']);
            $turnLogData['block_money'] = $res['yes']['b_num'];

            Db::startTrans();
            try {
                $usersMoneyModel = new UsersMoney();
                $usersBlockModel = new UsersBlock();
                $userInfo = $this->user;

                Users::where(['user_id' => $userInfo['user_id']])->setDec('turn_num', 1);

                if (intval($turnLogData['mid']) > 0 && $turnLogData['money'] > 0) {
                    $usersMoneyModel->amountChange($userInfo['user_id'], $turnLogData['mid'], $turnLogData['money'], 108, '转盘抽奖', [
                        'come_uid' => $userInfo['user_id']
                    ]);
                }
                if (intval($turnLogData['bid']) > 0 && $turnLogData['block_money'] > 0) {
                    $usersBlockModel->amountChange($userInfo['user_id'], $turnLogData['bid'], $turnLogData['block_money'], 108, '转盘抽奖', [
                        'come_uid' => $userInfo['user_id']
                    ]);
                }
                $turnLog->insertGetId($turnLogData);
                Db::commit();
                $result['code'] = 1;
                $result['msg'] = $res['yes'];
                return json($result);
            } catch (\Exception $e) {
                Db::rollback();

                \app\common\server\Log::exceptionWrite('转盘抽奖失败', $e->getMessage());
                $result['code'] = -2;
                return json($result);
            }
        } else {
            $prize = [];
            foreach ($prizeArr as $k => $v) {
                $prize[] = [
                    'id' => $v['id'],
                    'name' => $v['name'],
                    'image' => get_img_show_url($v['img']),
                    'rank' => ($k + 1),
                    'percent' => $v['is_per'],
                ];
            }

            return view('turn/turn_index', ['prizeArr' => json_encode($prize, JSON_UNESCAPED_UNICODE)]);
        }
    }

    /**
     * 核心部分 计算概率
     * @param $proArr
     * @return int|string
     */
    public function getRand($proArr)
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset($proArr);
        return $result;
    }

    /**
     * 抽奖记录
     * @param Request $request
     * @param TurnLog $turnLog
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function logIndex(Request $request, TurnLog $turnLog)
    {
        if ($request->isAjax()) {
            $turnNames = TurnModel::where('status', 1)->column('id, name');

            $list = $turnLog->getTurnLog($this->user_id);
            $userIdArr = get_arr_column($list, 'uid');
            if ($userIdArr) {
                $this->assign('userList', Users::whereIn('user_id', $userIdArr)->column('user_id, account'));
            }
            $this->assign('turnNames', $turnNames);
            $this->assign('list', $list);
            return view('turn/log_index_ajax');
        } else {
            return view('turn/log_index');
        }
    }

    /**
     * 详情
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function logDetail(Request $request)
    {
        $id = $request->param('id');
        $info = TurnModel::where('id', $id)->find();
        $this->assign('info', $info);
        $this->assign('moneyNames', get_money_name());
        return view('turn/log_detail');
    }

}
