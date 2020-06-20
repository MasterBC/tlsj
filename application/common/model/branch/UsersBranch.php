<?php

namespace app\common\model\branch;

use app\common\model\grade\Level;
use think\Db;
use think\facade\Log;
use app\common\model\Common;

class UsersBranch extends Common
{
    protected $name = 'users_branch';

    /**
     * 关联users模型
     * @return \think\model\relation\HasOne
     */
    public function userInfo()
    {
        return $this->hasOne('app\\common\\model\\Users', 'user_id', 'uid');
    }

    /**
     * 根据id获取接点信息
     * @param $branchId
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getBranchInfoById($branchId)
    {
        return self::where('id', (int)$branchId)->find();
    }

    /**
     * 根据会员id获取接点信息
     * @param $userId
     * @return array|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getBranchInfoByUid($userId)
    {
        return self::where('uid', (int)$userId)->find();
    }

    /**
     * 获取那个区域的接点人数
     * @param int $branchId 接点id
     * @param int $position 接点位置
     * @return int
     */
    public static function getBranchNumByPosition($branchId, $position)
    {
        try {
            $branchInfo = self::where('jdr_id', $branchId)->where('position', $position)->field('id')->find();

            return !empty($branchInfo) ? self::getBranchNumById($branchInfo['id']) + 1 : 0;
        } catch (\Exception $e) {
            Log::write('查询区域接点人数失败: branchId:' . $branchId . ', position: ' . $position . ', 错误信息:' . $e->getMessage(), 'error');

            return 0;
        }
    }

    /**
     * 获取接点人数
     * @param int $branchId 接点id
     * @return int 人数
     */
    public static function getBranchNumById($branchId)
    {
        $branchNum = 0;
        try {
            $jdrList = self::where('jdr_id', $branchId)->field('id')->select();

            foreach ($jdrList as $v) {
                $branchNum += 1;
                $branchNum = self::getBranchNumById($v['id']) + $branchNum;
            }
        } catch (\Exception $e) {
            Log::write('查询接点人数失败: branchId:' . $branchId . ', 错误信息:' . $e->getMessage(), 'error');
            $branchNum = 0;
        }
        return $branchNum;
    }


    /**
     * 获取下面接点uid
     * @param array $userInfo 用户的信息
     * @return int 人数
     */
    public static function getBranchUid($userInfo)
    {
        try {
            $userList = self::where('path', 'like', $userInfo['path'] . ',' . $userInfo['id'] . '%')->field('id,uid,path')->select();
            $branchIds = get_arr_column($userList, 'id');
            return $branchIds;
        } catch (\Exception $e) {
            Log::write('获取下面接点id 查询失败'.$e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * 查询会员网络图
     * @param $userInfo
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getUserNetworkList($userInfo)
    {
        $branchInfo = self::getBranchInfoByUid($userInfo['user_id']);
        if (empty($branchInfo)) {
            return [];
        }
        $levelInfo = Level::getLevelInfoById($userInfo['level']);
        $branchInfo['user_info'] = $userInfo;
        $networkList = [];

        $networkList[] = [
            'id' => $branchInfo['id'],
            'name' => $userInfo['account'],
            'prev_name' => $branchInfo['jdr_id'] > 0 ? self::withJoin(['userInfo' => function ($query) {
                $query->field('account');
            }])->where('id', $branchInfo['jdr_id'])->value('account') : '',
            'prev_id' => $branchInfo['jdr_id'] > 0 ? $branchInfo['jdr_id'] : '',
            'bg_color' => $levelInfo['color'],
            'open' => 'true',
            'html' => '',
            'child' => self::getNetworkList($branchInfo, 5)
        ];

        return $networkList;
    }

    /**
     * 获取最下的id
     * @param $id
     * @param int $type
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getLastBranchId($id, $type = 1)
    {
        $jdr = $this->where(['jdr_id' => $id])->field('position,id,uid')->select();
        $jdr = convert_arr_key($jdr, 'position');
        if (isset($jdr[$type]['id'])) {
            return $this->getLastBranchId($jdr[$type]['id'], $type);
        } else {
            return $id;
        }
    }

    /**
     * 获取网络图列表
     * @param $branchInfo
     * @param $endLayer
     * @param int $startLayer
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getNetworkList($branchInfo, $endLayer, $startLayer = 0)
    {
        $networkList = [];
        if (!empty($branchInfo) && $endLayer > $startLayer) {
            $list = self::with(['userInfo' => function ($query) {
                $query->field('user_id,account,level');
            }])->where('jdr_id', $branchInfo['id'])
                ->field('uid,id,br_num,position')->select()->toArray();
            $list = convert_arr_key($list, 'position');

            $num = $branchInfo['br_num'];
            if ($num > 0) {
                for ($i = 1; $i <= $num; $i++) {
                    if (isset($list[$i])) {
                        $levelInfo = Level::getLevelInfoById($list[$i]['user_info']['level']);
                        $arr = [
                            'id' => $list[$i]['id'],
                            'name' => $list[$i]['user_info']['account'],
                            'bg_color' => $levelInfo['color'],
                            'prev_name' => $branchInfo['user_info']['account'],
                            'prev_id' => $branchInfo['id'],
                            'open' => 'true',
                            'html' => '',
                            'child' => self::getNetworkList($list[$i], $endLayer, $startLayer + 1)
                        ];
                    } else {
                        $arr = [
                            'id' => 0,
                            'name' => '空',
                            'open' => 'true',
                            'html' => '',
                            'child' => []
                        ];
                    }
                    $networkList[] = $arr;
                }
            }
        }

        return $networkList;
    }

}