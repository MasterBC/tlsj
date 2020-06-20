<?php

namespace app\common\model;

use think\db\Where;
use think\Model;

class UserAuthName extends Model
{

    protected $name = "users_auth_name";
    public static $statusData = [
        '0' => '待申请',
        '1' => '待审核',
        '2' => '己拒绝',
        '9' => '己审核'
    ];

    /**
     * 根据opening_id查出银行卡的信息
     * @param $openingId 银行的id
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserAuthNameInfo($uid, $field = [])
    {
        $where = [
            'uid' => (int) $uid
        ];
        if ($field) {
            return $this->where($where)->field($field)->find();
        } else {
            return $this->where($where)->find();
        }
    }

    /**
     * 添加实名认证
     * @param $userId
     * @param $username
     * @param $card_number
     * @param $card_just
     * @param $card_back
     * @param string $hold_card
     * @param int $status
     * @return int|string
     */
    public function addUserAuthName($userId, $username, $card_number, $card_just = '', $card_back = '', $hold_card = '', $status = 1)
    {
        $data = [
            'uid' => $userId,
            'username' => $username,
            'card_number' => $card_number,
            'card_just' =>  $card_just != '' ? $card_just : '',
            'card_back' =>  $card_back != '' ? $card_back : '',
            'hold_card' => $hold_card != '' ? $hold_card : '',
            'status' => $status,
            'add_time' => time()
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
    public function updateUserAuthNameInfo($userId, $card_number, $card_just, $card_back, $hold_card = '', $status = 1)
    {
        $data = [
            'card_number' => $openingId,
            'card_just' => $bankAccount,
            'card_back' => $bankAddress,
            'hold_card' => $hold_card != '' ? $hold_card : '',
            'status' => $status
        ];

        return $this->where('uid', $userId)->update($data);
    }

    /**
     * 拒绝 会员 实名认证
     * @param $Id
     * @param $bankName
     * @param $bankAddress
     * @param $openingId
     * @param $bankAccount
     * @param $bankDefault
     * @return UserBank
     */
    public function refuseUserAuthNameInfo($userId, $refuse_note, $admin_id)
    {
        $data = [
            'admin_id' => $admin_id,
            'refuse_note' => $refuse_note,
            'refuse_time' => time(),
            'status' => 2
        ];

        return $this->where('uid', $userId)->update($data);
    }

    /**
     * 确认审核 实名认证
     * @param $Id
     * @param $bankName
     * @param $bankAddress
     * @param $openingId
     * @param $bankAccount
     * @param $bankDefault
     * @return UserBank
     */
    public function confirmUserAuthNameInfo($userId, $refuse_note, $admin_id)
    {
        $data = [
            'admin_id' => $admin_id,
            'confirm_time' => time(),
            'status' => 9
        ];
        return $this->where('uid', $userId)->update($data);
    }

    /**
     * 删除数据
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

}
