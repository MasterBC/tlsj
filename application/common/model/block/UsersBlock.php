<?php

namespace app\common\model\block;

use think\facade\Request;
use think\Model;
use think\Db;
use think\facade\Log;
use think\db\Where;

class UsersBlock extends Model
{
    protected $name = "block_user";

    
    /**
     * 根据会员的 ID 获取 所有的  货币
     * @param type $uId
     * @param type  1 可用余额  2 冻结金额 3  可用+冻结 总额
     * @return type
     */
    public function getUserOwnedBlock($uId, $type = 1)
    {
        try {
            $where = [
                'uid' => (int) $uId
            ];
            switch ($type) {
                case 1:
                    return self::where($where)->column('bid, money');
                    break;
                case 2:
                    return self::where($where)->column('bid, frozen');
                    break;
                case 3:
                    return self::where($where)->column('bid,' . Db::raw('(money+frozen)'));
                    break;
            }
        } catch (\Exception $e) {
            Log::write('余额查询失败: (请求地址: ' . Request::module() . '/' . Request::controller() . '/' . Request::action() . ',' . 'user_id: ' . $uId . ', type:' . $type . ')' . $e->getMessage(), 'error');
            return [];
        }
    }
    
    
    /**
     * 根据id获取单个数据值
     * @param $uid 用户id
     * @param $bid 货币id
     * @param int $type 类型
     * @return mixed
     */
    public function getUsersBlockInfoById($uid, $bid, $type = 0)
    {
        $where = [
            'uid' => (int)$uid,
            'bid' => (int)$bid
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

    /**
     * 会员货币金额变动
     * @param int $userId 用户id
     * @param int $blockId 货币id
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
     */
    public function amountChange($userId, $blockId, $money, $type = 0, $note = '', $otherData = [])
    {

        try {
            $userId = intval($userId);
            $blockId = intval($blockId);
            $money = floatval($money);
            $type = intval($type);
            $res = self::where('uid', $userId)->where('bid', $blockId)
                ->update([
                    'money' => Db::raw('money+' . $money),
                    'update_time' => time()
                ]);
            // 有类型才添加变动记录
            if ($type > 0) {
                return (new BlockLog())->addLog($userId, $blockId, $money, $type, $note, $otherData);
            }
            return ($res >= 1 ? true : false);
        } catch (\Exception $e) {
            dump($e->getMessage());
            exit;
            Log::write('会员金额变动错误 会员id(' . $userId . '),block_id(' . $blockId . '),金额(' . $money . '),类型(' . $type . '),备注(' . $note . '): ' . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * 锁定会员钱包金额
     * @param int $userId 会员id
     * @param int $blockId 钱包id
     * @param float $money 锁定金额
     * @param int $type 类型
     * @param string $note 备注
     * @return bool|int|string
     * @throws \Exception
     */
    public function amountLock($userId, $blockId, $money, $type = 0, $note = '')
    {
        try {
            $userId = intval($userId);
            $blockId = intval($blockId);
            $money = floatval($money);
            $type = intval($type);

            $res = self::where('uid', $userId)->where('bid', $blockId)
                ->update([
                    'frozen' => Db::raw('frozen+' . $money),
                ]);
            // 有类型才添加变动记录
            if ($type > 0) {
                return (new UsersBlockLockLog())->addLog($userId, $blockId, $money, $type, $note);
            }
            return ($res >= 1 ? true : false);
        } catch (\Exception $e) {
            Log::write('会员货币金额冻结失败 会员id(' . $userId . '),货币(' . $blockId . '),金额(' . $money . '),类型(' . $type . '),备注(' . $note . '): ' . $e->getMessage(), 'error');

            exception('金额冻结失败');
            return false;
        }
    }

    /**
     * 会员冻结货币解冻
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
                $lockLogInfo = (new UsersBlockLockLog())->where('id', intval($lockLog))->find();
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

            $res = self::where('uid', $lockLogInfo['uid'])->where('bid', $lockLogInfo['bid'])
                ->update([
                    'frozen' => Db::raw('frozen-' . $amount),
                ]);

            return ($res >= 1 ? true : false);
        } catch (\Exception $e) {
            Log::write('会员货币金额解冻失败 lockLog: ' . print_r($lockLog, true) . ', amount: ' . $amount . ', note: ' . $note . ': ' . $e->getMessage(), 'error');
            exception('金额解除冻结失败');
            return false;
        }
    }

    /**
     * 根据条件查询
     * @param array $where
     * @param array $field
     * @param int $type
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getWhereUserBlockField($where, $field = [], $type = 1)
    {
        switch ($type) {
            case 1:
                return $this->where($where)->field($field)->find();
                break;
            case 2:
                return $this->where($where)->field($field)->select();
                break;
        }
    }

    /**
     * 根据会员id查询余额
     * @param $userId
     * @param $blockId
     * @param int $type
     * [
     *      1 => '会员可用金额',
     *      2 => '会员冻结金额',
     *      3 => '总金额'
     * ]
     * @return float|int
     */
    public static function getAmountByUid($userId, $blockId, $type = 1): float
    {
        try {
            $where = new Where();
            $where['uid'] = (int)$userId;
            $where['bid'] = (int)$blockId;

            switch ($type) {
                case 1:
                    return (float)self::where($where)->value('money');
                    break;
                case 2:
                    return (float)self::where($where)->value('frozen');
                    break;
                case 3:
                    return (float)self::where($where)->field(Db::raw('(money+frozen) money'))->find()['money'] ?? 0;
                    break;
            }
        } catch (\Exception $e) {
            Log::write('货币查询失败: (请求地址: ' . Request::module() . '/' . Request::controller() . '/' . Request::action() . ',' . 'user_id: ' . $userId . ', block_id: ' . $blockId . ', type:' . $type . ')' . $e->getMessage(), 'error');
            return 0;
        }
    }

    /**
     * 根据会员id查询所有币的金额
     * @param $userId
     * @param int $type
     * [
     *      1 => '会员可用金额',
     *      2 => '会员冻结金额',
     *      3 => '总金额'
     * ]
     * @return array
     */
    public static function getAmountsByUid($userId, $type = 1): array
    {
        try {
            $where = new Where();
            $where['uid'] = (int)$userId;

            switch ($type) {
                case 1:
                    return self::where($where)->column('bid, money');
                    break;
                case 2:
                    return self::where($where)->column('bid, frozen');
                    break;
                case 3:
                    return self::where($where)->column('bid,' . Db::raw('(money+frozen)'));
                    break;
            }
        } catch (\Exception $e) {
            Log::write('货币查询失败: (请求地址: ' . Request::module() . '/' . Request::controller() . '/' . Request::action() . ',' . 'user_id: ' . $userId . ', type:' . $type . ')' . $e->getMessage(), 'error');

            return [];
        }
    }
}