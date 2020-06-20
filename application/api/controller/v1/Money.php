<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\api\response\ReturnCode;
use app\common\model\money\MoneyLog;
use think\db\Where;
use think\Request;

class Money extends Base
{

    /**
     * 获取钱包日志
     * @param Request $request
     * @param Where $where
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMoneyLog(Request $request, Where $where)
    {
        $moneyLogModel = MoneyLog::where('uid', $this->user['user_id']);
        $where['uid'] = $this->user['user_id'];
        if ($type = $request->param('type', 0, 'intval')) {
            $moneyLogModel->where('is_type', $type);
        }
        if ($note = $request->param('note', '', 'trim')) {
            $moneyLogModel->whereLike('note', '%' . $note . '%');
        }
        $startTime = strtotime($request->param('start_time'));
        $endTime = strtotime($request->param('end_time'));
        if ($startTime && $endTime) {
            $moneyLogModel->whereBetween('edit_time', [$startTime, $endTime + 86400]);
        } elseif ($startTime > 0) {
            $moneyLogModel->where('edit_time', '>', $startTime);
        } elseif ($endTime > 0) {
            $moneyLogModel->where('edit_time', '<', $endTime);
        }
        $moneyLogModel->order($request->param('order') ?? 'id', $request->param('sort') ?? 'desc');
        $page = $request->param('page', 1, 'intval');
        $page = $page - 1;
        $pageSize = $request->param('rows', 10, 'intval');
        $moneyLogModel->limit($page * $pageSize, $pageSize);
        $list = $moneyLogModel->select();

        $moneyLogType = money_log_type();
        $moneyNames = \app\common\model\money\Money::getMoneyNames();

        $logList = [];
        foreach ($list as $v) {
            $arr = [
                'id' => $v['id'],
                'wallet_name' => $moneyNames[$v['mid']] ?? '',
                'type' => $moneyLogType[$v['is_type']] ?? '',
                'money' => $v['money'],
                'edit_time' => date('Y-m-d H:i:s', $v['edit_time']),
                'note' => $v['note']
            ];

            $logList[] = $arr;
        }

        $data = [
            'logList' => $logList,
            'type' => $moneyLogType
        ];

        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $data);
    }
}