<?php

namespace app\common\model\block;

use think\Model;

class BlockUserLock extends Model
{
    protected $name = 'block_user_lock';

    /**
     * 添加日志
     * @param $uId
     * @param string $bId
     * @param $lockNum
     * @param string $note
     * @return int|string
     * @throws \think\Exception
     */
    public function addLog($uId, $bId, $lockNum, $type = 1, $note = '')
    {
        $data = [
            'uid' => $uId,
            'bid' => $bId,
            'add_time' => time(),
            'frozen_money' => $lockNum,
            'status' => 2,
            'type' => $type,
            'lock_note' => $note ? $note : ' '

        ];
        return $this->insertGetId($data);
//        $usersBlockModel = new UsersBlock();
//        $res = $usersBlockModel->getSetInc($uId,$bId,$lockNum);
//        dump($res);exit;
//        if ($res){
//           return $this->insertGetId($data);
//        }
    }

}