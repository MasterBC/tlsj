<?php

namespace app\common\model\money;

use think\db\Where;
use think\Model;
use think\Db;
use think\facade\Log;
use think\facade\Request;

class UsersMoney extends Model
{
    protected $name = "users_money";

    
    /**
     * 根据会员的 ID 获取 所有的  可用预额
     * @param type $uId
     * @param type  1 可用余额  2 冻结金额 3  可用+冻结 总额
     * @return type
     */
    public function getUserOwnedMoney($uId, $type = 1)
    {
        try {
            $where = [
                'uid' => (int) $uId
            ];
            switch ($type) {
                case 1:
                    return self::where($where)->column('mid, money');
                    break;
                case 2:
                    return self::where($where)->column('mid, frozen');
                    break;
                case 3:
                    return self::where($where)->column('mid,' . Db::raw('(money+frozen)'));
                    break;
            }
        } catch (\Exception $e) {
            Log::write('余额查询失败: (请求地址: ' . Request::module() . '/' . Request::controller() . '/' . Request::action() . ',' . 'user_id: ' . $uId . ', type:' . $type . ')' . $e->getMessage(), 'error');
            return [];
        }
    }
    
    
    
    
    /**
     * 获取用户的钱包
     * @param string $uId 用户id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserMoneyInfoById($uId, $field = [])
    {
        $where = [
            'uid' => (int)$uId
        ];
        return $this->where($where)->field($field)->select();
    }

    /**
     * 会员钱包金额变动
     * @param int $userId 用户id
     * @param int $moneyId 钱包id
     * @param float $money 变动金额
     * @param int $type 类型 >1 添加变动记录
     * @param string $note 备注
     * @param array $otherData 其他数据
     * [
     *      'admin_id' => '管理员id',
     *      'bonus_log_id' => '奖金记录id',
     *      'come_uid' => '来源会员id',
     *      'order_id' => '订单id'
     * ]
     * @return bool
     * @throws \Exception
     */
    public function amountChange($userId, $moneyId, $money, $type = 0, $note = '', $otherData = [])
    {
        try {
            $userId = intval($userId);
            $moneyId = intval($moneyId);
            $money = floatval($money);
            $type = intval($type);
            $data = [
                    'money' => Db::raw('money+' . $money),
                ];
            if($money > 0) {
                $data['total'] = Db::raw('total+' . $money);
            }
            $res = self::where('uid', $userId)->where('mid', $moneyId)
                ->update($data);
            // 有类型才添加变动记录
            if ($type > 0) {
                return (new MoneyLog())->addLog($userId, $moneyId, $money, $type, $note, $otherData);
            }
            return ($res >= 1 ? true : false);
        } catch (\Exception $e) {
            Log::write('会员金额变动错误 会员id(' . $userId . '),钱包(' . $moneyId . '),金额(' . $money . '),类型(' . $type . '),备注(' . $note . '): ' . $e->getMessage(), 'error');
            exception('金额变动错误');
            return false;
        }
    }

    /**
     * 锁定会员钱包金额
     * @param int $userId 会员id
     * @param int $moneyId 钱包id
     * @param float $money 锁定金额
     * @param int $type 类型
     * @param string $note 备注
     * @return bool|int|string
     * @throws \Exception
     */
    public function amountLock($userId, $moneyId, $money, $type = 0, $note = '')
    {
        try {
            $userId = intval($userId);
            $moneyId = intval($moneyId);
            $money = floatval($money);
            $type = intval($type);

            $res = self::where('uid', $userId)->where('mid', $moneyId)
                ->update([
                    'frozen' => Db::raw('frozen+' . $money),
                ]);
            // 有类型才添加变动记录
            if ($type > 0) {
                return (new UsersMoneyLockLog())->addLog($userId, $moneyId, $money, $type, $note);
            }
            return ($res >= 1 ? true : false);
        } catch (\Exception $e) {
            Log::write('会员金额冻结失败 会员id(' . $userId . '),钱包(' . $moneyId . '),金额(' . $money . '),类型(' . $type . '),备注(' . $note . '): ' . $e->getMessage(), 'error');

            exception('金额冻结失败');
            return false;
        }
    }

    /**
     * 会员冻结金额解冻
     * @param int|array $lockLog 冻结日志|冻结日志id
     * @param float $amount 解除冻结金额
     * @param string $note 备注
     * @return bool
     * @throws \Exception
     */
    public function amountUnLock($lockLog, $amount, $note = '')
    {
        try {
            $amount = floatval($amount);

            if (is_numeric($lockLog)) {
                $lockLogInfo = (new UsersMoneyLockLog())->where('id', intval($lockLog))->find();
            } else {
                $lockLogInfo = $lockLog;
            }
            if (empty($lockLogInfo)) {
                return false;
            }

            $lockLogInfo->stay_money -= $amount;
            $lockLogInfo->last_time = time();
            $lockLogInfo->last_money = $amount;
            $note && $lockLogInfo->note = $note;
            if ($lockLogInfo->stay_money <= 0) {
                $lockLogInfo->status = 9;
                $lockLogInfo->out_time = time();
            } else {
                $lockLogInfo->status = 2;
            }
            $lockLogInfo->save();

            $res = self::where('uid', $lockLogInfo['uid'])->where('mid', $lockLogInfo['mid'])
                ->update([
                    'frozen' => Db::raw('frozen-' . $amount),
                ]);

            return ($res >= 1 ? true : false);
        } catch (\Exception $e) {
            Log::write('会员金额解冻失败 lockLog: ' . print_r($lockLog, true) . ', amount: ' . $amount . ', note: ' . $note . ': ' . $e->getMessage(), 'error');
            exception('金额解除冻结失败');
            return false;
        }
    }


    /**根据条件获取想要的字段
     * @param int $uId 用户id
     * @param int $mId 钱包id
     * @param int $type 类型
     * [
     *      1 => '会员可用金额',
     *      2 => '会员冻结金额',
     *      3 => '总金额'
     * ]
     * @return float
     */
    public static function getUsersMoneyByUserId($uId, $mId, $type = 1)
    {
        try {
            $where = new Where();
            $where['uid'] = (int)$uId;
            $where['mid'] = (int)$mId;

            switch ($type) {
                case 1:
                    return (float)self::where($where)->value('money');
                    break;
                case 2:
                    return (float)self::where($where)->value('frozen');
                    break;
                case 3:
                    $info = self::where($where)->field('money,frozen')->find();
                    return (float)($info['money'] + $info['frozen']);
                    break;
            }
        } catch (\Exception $e) {
            Log::write('金额查询失败: (请求地址: ' . Request::module() . '/' . Request::controller() . '/' . Request::action() . ',' . 'user_id: ' . $uId . ', money_id: ' . $mId . ', type:' . $type . ')' . $e->getMessage(), 'error');
            return 0;
        }
    }

    /**获取用户的某个钱包
     * @param string $userId 用户id
     * @param string $moneyId 钱包id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUserMoneyInfoByUid($userId = '', $moneyId = '')
    {
        return self::where('uid', (int)$userId)->where('mid', (int)$moneyId)->find();
    }

    /**
     * 根据类型查出想要的数据
     * @param int $userId
     * @param int $bid
     * @param int $type
     * @return mixed
     */
    public function getUsersMoneyType($userId = 1, $mid = 0, $type = 0)
    {
        $where = [
            'uid' => $userId,
            'mid' => $mid
        ];

        switch ($type) {
            case 1:
                return $this->where($where)->value('money');
                break;
            case 2:
                return $this->where($where)->value('frozen');
                break;

        }
    }
}