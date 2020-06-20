<?php

namespace app\common\logic;

use app\common\model\money\Money;
use app\common\model\money\MoneyTransform;
use app\common\model\money\MoneyTransformLog;
use app\common\model\money\UsersMoney;
use think\facade\Request;
use think\facade\Log;
use think\Db;
use app\facade\Password;

class MoneyTransformLogic
{
    /**
     * 钱包转换
     * @param $data
     * @param $userInfo
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doMoneyTransform($data, $userInfo)
    {
        //获取参数
        $mid = intval($data['mid']);
        $toMid = intval($data['to_mid']);
        $money = intval($data['toNum']);
        $userId = intval($userInfo['user_id']);
        $pwd = floatval($data['secpwd']);
        if ($userId <= 0) {
            exception('网络错误，请刷新后重试');
        }
        if ($data['toNum'] <= 0) {
            exception('转换数量不能为零');
        }

        // 实例化model类
        $moneyModel = new Money();
        $userMoneyModel = new UsersMoney();
        $moneyTransformModel = new MoneyTransform();
        $moneyTransformLogModel = new MoneyTransformLog();

        //转账参数
        $transformConfig = $moneyTransformModel->getMoneyTransformInfoById($mid, $toMid, 'low,bei,out,fee,per,day_total');
        // 将参数强制转换为浮点型
        $low = (float)($transformConfig['low']);//起点金额
        $bei = (float)($transformConfig['bei']);//倍率
        $out = (float)($transformConfig['out']);//单笔最高金额
        $fee = (float)($transformConfig['fee']);//手续费
        $per = (float)($transformConfig['per']);//到账比例
        // 最低金额
        if ($low > 0) {
            if ($money < $low) {
                exception('金额输入错误，转账' . $low . '起');
            }
        }
        // 多少的倍数
        if ($bei > 0) {
            if ($money % $bei != 0) {
                exception('金额必须是' . $bei . '的倍数');
            }
        }
        // 单笔最高
        if ($out > 0) {
            if ($money > $out) {
                exception('金额单笔最高' . $out);
            }
        }
        // 今日转换封顶
        $todayTransformMoney = $moneyTransformLogModel->getUserTodayMoney($userId, $mid, $toMid);
        if ($transformConfig['day_total'] > 0 && $transformConfig['day_total'] > $todayTransformMoney) {
            exception('转换金额今日已达到上限!');
        }
        // 默认备注
        $outNote = $enterNote = '';
        $poundage = 0;
        if ($fee > 0) {
            $poundage = $money * $fee / 100; //手续费
            $perMoney = $money - $poundage;//扣除手续费
            $outMoney = $perMoney * $per;//实际到账金额
            $outNote = $money . '，转出手续费' . $poundage . '%';//备注
        } else {
            $outMoney = $enterMoney = $money * $per;
        }
        //获取转出钱包金额
        $balance = $userMoneyModel::getUsersMoneyByUserId($userId, $mid, 1);

        $moneyNames = $moneyModel::getMoneyNames();
        //获取钱包名称
        $moneyName = isset($moneyNames[$mid]) ? $moneyNames[$mid] : '';//转出钱包名称
        $ToMoneyName = isset($moneyNames[$toMid]) ? $moneyNames[$toMid] : '';//转入钱包名称

        if ($money > $balance) {
            exception($moneyName . '余额不足');
        }
        // 判断二级密码是否正确
        if (!Password::checkPayPassword($pwd, $userInfo)) {
            exception('二级密码错误');
        }

        //启动事务
        Db::startTrans();
        try {
            // 结算到账
            $userMoneyModel->amountChange($userId, $mid, '-' . $money, 101, $moneyName . '转至' . $ToMoneyName . '金额:' . $outNote, '');//转出钱包
            $userMoneyModel->amountChange($userId, $toMid, $outMoney, 102, $moneyName . '转入' . $ToMoneyName . '金额:' . $enterNote, '');//转入钱包

            $moneyTransformLogModel->addLog($userId, $mid, $toMid, $money, $outMoney, $fee, $poundage, '', $moneyName, $ToMoneyName);//添加转换钱包金额日志

        } catch (\Exception $e) {
            Log::write('会员钱包转换 钱包ID(' . $mid . '),转入钱包ID(' . $toMid . '),金额(' . $outMoney . '),类型(' . 52 . '),备注 钱包转换(' . $outNote . $money . '): ' . $e->getMessage(), 'error');
            Db::rollback();

            exception('操作失败');
        }
        Db::commit();

        return true;

    }
}