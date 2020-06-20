<?php

namespace app\common\model;

use think\db\Where;
use think\Model;

class UserBank extends Model
{
    protected $name = "users_bank";

    /**
     * 获取用户银行卡数据
     * @param $uId
     */
    public function getUserBank($uId)
    {
        $where = [
            'uid' => (int)$uId
        ];
        return $this->where($where)->order('bank_default asc')->select();
    }

    /**
     * 根据id获取银行卡信息
     * @param int $id 会员银行卡id
     * @return array 银行卡信息
     */
    public function getInfoById($id)
    {
        $where = [
            'id' => (int)$id,
        ];
        return $this->where($where)->find();
    }

    /**
     * 根据opening_id查出银行卡的信息
     * @param $openingId 银行的id
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBankByBankInfo($openingId, $field = [])
    {
        $where = [
            'opening_id' => (int)$openingId
        ];

        if ($field) {
            return $this->where($where)->field($field)->find();
        } else {
            return $this->where($where)->find();
        }
    }

    /**
     * 设置默认银行卡
     * @param int $id 银行卡id
     * @param int $userId 会员id
     * @return bool
     */
    public function setDefault($id, $userId)
    {

        $this->where(['uid' => $userId])->setField('bank_default', 2);

        $res = $this->where(['id' => $id])->setField('bank_default', 1);
        if (!$res) {
            return false;
        }
        return true;
    }

    /**
     * 判断会员默认银行卡
     * @param int $userId 会员id
     * @return bool
     */
    public function afterOperaForUser($userId)
    {

        $banks = $this->getUserBank($userId);
        if (!$banks) {
            return false;
        }
        $bankDefaults = get_arr_column($banks, 'bank_default');
        if (!in_array(1, $bankDefaults)) {
            $this->where(['uid' => $userId])->order('id asc')->limit(1)->update(['bank_default' => 1]);
        }

        return true;
    }

    /**
     * 根据会员Id和银行卡id获取信息
     * @param $userId
     * @param $openingId
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBankInfoByUidAndOpeningId($userId, $openingId)
    {
        $where = [
            'user_id' => (int)$userId,
            'opening_id' => (int)$openingId
        ];

        return $this->where($where)->find();
    }


    /**
     * 添加银行卡
     * @param $userId 用户id
     * @param $openingId  开户银行id
     * @param $bankAccount 账号
     * @param $bankAddress 支行
     * @param $bankName 开户名称
     * @return int|string
     */
    public function addBank($userId, $openingId, $bankAccount, $bankAddress, $bankName, $default = 0)
    {
        $data = [
            'uid' => $userId,
            'opening_id' => $openingId,
            'bank_account' => $bankAccount,
            'bank_address' => $bankAddress,
            'bank_name' => $bankName,
            'bank_default' => $default,
        ];
        return $this->insertGetId($data);
    }

    /**
     * 修改用户银行卡信息
     * @param $Id
     * @param $bankName
     * @param $bankAddress
     * @param $openingId
     * @param $bankAccount
     * @param $bankDefault
     * @return UserBank
     */
    public function updateUserBankInfo($Id, $bankName, $bankAddress, $openingId, $bankAccount, $bankDefault)
    {
        $data = [
            'bank_name' => $bankName,
            'bank_address' => $bankAddress,
            'opening_id' => $openingId,
            'bank_account' => $bankAccount,
            'bank_default' => $bankDefault,
        ];

        return $this->where('id', $Id)->update($data);
    }

    /**删除数据
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function delBank($id)
    {
        $where = [
            'id' => $id
        ];
        return $this->where($where)->delete();
    }

    /**
     * 获取会员默认的一张银行卡
     * @param $userId
     * @return array|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUserDefaultBank($userId)
    {
        $where = [
            'uid' => (int)$userId
            , 'bank_default' => 1
        ];
        return self::where($where)->find();
    }
}