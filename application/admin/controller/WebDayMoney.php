<?php

namespace app\admin\controller;

use think\db;
use think\db\Where;
use think\facade\Log;
use think\Request;
use app\common\model\AdminLog;
use app\common\model\Ad;
use app\common\model\work\MoneyWebDay;
use think\helper\Time;

class WebDayMoney extends Base
{

    /**
     * 显示银行卡列表
     * @param Request $request
     * @param Where $where
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function dayMoneyList(Request $request, Where $where, MoneyWebDay $moneyWebDayModel)
    {
        if ($request->isAjax()) {
            try {
                // 获取搜索参数
                $title = $request->param('title', '', 'trim');
                $page = $request->get('p', '1', 'int') - 1;
                $pageSize = $request->get('p_num', '10', 'int');
                $list = $moneyWebDayModel::where($where)->limit($page * $pageSize, $pageSize)->order('id desc')->select();
                $configList = [];
                foreach ($list as $v) {
                    $arr = $v;
                    $arr['add_time'] = date('Y-m-d', $v['add_time']);
                    $arr['total_money'] = $v['total_money'];
                    $arr['zuo_money'] = $v['zuo_money'];
                    $arr['zuo_levle_money'] = $v['zuo_levle_money'];
                    $arr['totao_level'] = $v['totao_level'];
                    $arr['day_level'] = $v['day_level'];
                    $arr['stay_level'] = $v['stay_level'];
                    $configList[] = $arr;
                }
                $data = [
                    'code' => 1,
                    'data' => $configList,
                    'count' => $moneyWebDayModel::where($where)->count()
                ];
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询日收入失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '没有获取记录'
                ];
                return json()->data($data);
            }
        } else {
            return view('website/money_web/day_list');
        }
    }

    /**
     * 添加日收入
     * @param Request $request
     * @param Bank $bankModel
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function addDayMoney(Request $request, MoneyWebDay $moneyWebDayModel)
    {
        if ($request->isPost()) {
            try {
                $infoDayNum = $moneyWebDayModel->where('add_time', strtotime($request->param('time', '', 'trim')))->count();
                if ($infoDayNum > 0) {
                    $this->error('请不要重复添加');
                }
                $yesterDayMoney = $moneyWebDayModel->whereBetween('add_time', Time::today())->find();
                $data['day_money'] = $request->param('day_money', '', 'floatval');
                $data['total_money'] = $request->param('total_money', '', 'floatval');
                $data['zuo_money'] = $request->param('zuo_money', '', 'floatval');
                $data['zuo_levle_money'] = $request->param('zuo_levle_money', '', 'floatval');
                $data['totao_level'] = $request->param('totao_level', '', 'intval');
                $data['day_level'] = $request->param('day_level', '', 'intval');
                $data['stay_level'] = $request->param('stay_level', '', 'intval');
                $data['add_time'] = strtotime($request->param('time', '', 'trim'));
                $moneyWebDayModel->insertGetId($data);
                AdminLog::addLog('添加日收入', $request->param());
                return json()->data(['code' => 1, 'msg' => '添加成功']);
            } catch (\Exception $e) {
                Log::write('添加日收支失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            return view('website/money_web/day_add_money');
        }
    }

    /**
     * 编辑日收入
     * @param Request $request
     * @param Bank $bankModel
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editDayMoney(Request $request, MoneyWebDay $moneyWebDayModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $moneyWebDayModel->where('id', $id)->find();
        if (empty($info)) {
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {
//                $info->day_money = $request->param('day_money', '', 'floatval');
//                $info->total_money = $request->param('total_money', '', 'floatval');
                $info->zuo_money = $request->param('zuo_money', '', 'floatval');
//                $info->zuo_levle_money = $request->param('zuo_levle_money', '', 'floatval');
//                $info->totao_level = $request->param('totao_level', '', 'intval');
//                $info->day_level = $request->param('day_level', '', 'intval');
//                $info->stay_level = $request->param('stay_level', '', 'intval');
                $info->save();
                AdminLog::addLog('修改日收入', $request->param());
                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            return view('website/money_web/day_edit_money_info', [
                'info' => $info
            ]);
        }
    }

    /**
     * 删除收款方式
     * @param Request $request
     * @param Bank $bankModel
     * @return \think\Response|\think\response\Json
     */
    public function delDayMoney(Request $request, MoneyWebDay $moneyWebDayModel)
    {
        if ($request->isPost()) {
            $id = $request->param('id');
            if (!$id) {
                return json(['code' => -1, 'msg' => '请选择']);
            }
            Db::startTrans();
            try {
                $arrId = explode(',', $id);
                $logList = $moneyWebDayModel::whereIn('id', $arrId)->select();
                if (count($logList) <= 0) {
                    return json()->data(['code' => -1, 'msg' => '此信息不支持该操作']);
                }
                $moneyWebDayModel::whereIn('id', $arrId)->delete();
                $data = $logList->toArray();
                $num = count($data);
                Db::commit();
                AdminLog::addLog('删除日收支明细', $data);
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

    public function adList(Request $request){
        if ($request->isAjax()) {
            $page = $request->get('p', '1', 'int') - 1;
            $pageSize = $request->get('p_num', '10', 'int');
            $res = Ad::limit($page * $pageSize, $pageSize)->order('id desc')->select();
            $list = [];
            foreach ($res as $v) {
                $arr = $v;
                $arr['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
                $arr['state'] = $arr['state']?'启用':'<span style="color:red">停用</span>';
                $arr['type'] = $arr['type']?'安卓':'IOS';
                $arr['times'] = $arr['times'].'秒';
                $list[] = $arr;
            }
            $data = [
                'code' => 1,
                'data' => $list,
                'count' => Ad::count()
            ];
            return json()->data($data);
        }else{
            return view('ad/ad_list');
        }
    }

    public function addAd(Request $request){
        if ($request->isPost()) {
            $data['title'] = $request->param('title','','trim');
            $data['url'] = $request->param('url','','trim');
            $data['state'] = $request->param('state','','trim');
            $data['ico'] = $request->param('ico','','trim');
            $data['app'] = $request->param('app','','trim');
            $data['type'] = $request->param('type','','intval');
            $data['times'] = $request->param('times','','intval');
            $data['add_time'] = time();
            $num = Ad::where('add_time', strtotime($request->param('time', '', 'trim')))->count();
            if ($num > 0) {
                $this->error('请不要重复添加');
            }
            Ad::insertGetId($data);
            AdminLog::addLog('添加广告链接', $request->param());
            return json()->data(['code' => 1, 'msg' => '添加成功']);
        }else{
            return view('ad/add_ad');
        }
    }

    public function editAd(Request $request){
        $id = $request->param('id','','intval');
        if ($request->isPost()) {
            $data['title'] = $request->param('title','','trim');
            $data['url'] = $request->param('url','','trim');
            $data['state'] = $request->param('state','','trim');
            $data['ico'] = $request->param('ico','','trim');
            $data['app'] = $request->param('app','','trim');
            $data['type'] = $request->param('type','','intval');
            $data['times'] = $request->param('times','','intval');
            Ad::where('id',$id)->update($data);
            AdminLog::addLog('更新广告链接', $request->param());
            return json()->data(['code' => 1, 'msg' => '更新成功']);
        }else{
            $data = Ad::where('id',$id)->find();
            return view('ad/edit_ad',[
                'data' => $data,
            ]);
        }
    }

    public function delAd(Request $request){
        if ($request->isPost()) {
            $id = $request->param('id','','intval');
            $result = Ad::where('id',$id)->delete();
            AdminLog::addLog('删除广告', $id);
            if ($result){
                return json()->data(['code' => 1, 'msg' => '成功删除']);
            }else{
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        }
    }

}
