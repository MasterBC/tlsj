<?php

namespace app\common\model;

use app\common\model\grade\Level;
use app\common\server\Log;
use think\Model;
use think\facade\Request;
use think\facade\Session;

class Users extends Model
{

    protected $name = 'users';
    protected $pk = 'user_id';

    // session名称
    const SESSION_NAME = 'user';

    /**
     * 关联模型
     * @return \think\model\relation\HasOne
     */
    public function userData()
    {
        return $this->hasOne('UsersData', 'id', 'data_id');
    }

    /**
     * 检测用户是否登录
     * @return bool
     */
    public static function checkLogin()
    {
        $outTime = ((int) zf_cache('security_info.web_past_due_time') <= 0 ? 10 : (int) zf_cache('security_info.web_past_due_time')) * 60;
        if (!Session::has(self::SESSION_NAME) || time() - Session::get(self::SESSION_NAME . '_web_past_due_time') > $outTime) {
            Session::has(self::SESSION_NAME) && Session::delete(self::SESSION_NAME);
            return false;
        } else {
            Session::set(self::SESSION_NAME . '_web_past_due_time', time());
            return true;
        }
    }

    /**
     * 设置session信息
     */
    public function setSession()
    {
        $this->session_id = sid();
        $this->save();
        Session::set(self::SESSION_NAME, $this);
        Session::set(self::SESSION_NAME . '_web_past_due_time', time());
    }

    /**
     * 清除用户session
     */
    public static function clearSession()
    {
        Session::delete(self::SESSION_NAME);
    }

    /**
     * 获取session里面的会员信息
     * @return mixed
     */
    public function getSessionUserInfo()
    {
        return Session::get(self::SESSION_NAME);
    }

    /**
     * 插入数据
     * @param array $userData 插入数据
     * @return Users 查询的用户信息
     */
    public function addUserDataInfo($userData)
    {
        return $this->create($userData);
    }

    /**
     * 根据user_id查出用户的信息
     * @param $userId 用户的id
     * @param $field 要查出的字段
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserByUserId($userId, $field = [])
    {
        $where = [
            'user_id' => (int) $userId
        ];
        if ($field) {
            return $this->where($where)->field($field)->find();
        } else {
            return $this->where($where)->find();
        }
    }

    /**
     * 根据tjr_id查出用户的信息
     * @param $userId 用户的id
     * @param $field 要查出的字段
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserByUserTjrId($tjrId, $field = [])
    {
        $where = [
            'tjr_id' => (int) $tjrId
        ];
        if ($field) {
            return $this->where($where)->field($field)->select();
        } else {
            return $this->where($where)->select();
        }
    }

    /*     * 获取单个推荐人信息
     * @param $tjrId
     * @param array $field
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function getUserTjrInfo($tjrId, $field = [])
    {
        $where = [
            'user_id' => (int) $tjrId
        ];
        return $this->where($where)->field($field)->find();
    }

    /*     * 根据id 获取数据字段
     * @param array $Id 用户data_id
     * @param string $field 字段
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function getUsersInfo($Id = [], $field = '')
    {
        if ($field != '') {
            return $this->whereIn('user_id', $Id)->column($field);
        }
    }

    /*     * 根据id 获取数据字段
     * @param array $Id 用户data_id
     * @param string $field 字段
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function getUsersFieldInfo($Id = [], $field = '')
    {

        return $this->whereIn('user_id', $Id)->field($field)->select();
    }

    /**
     * 根据账号获取会员信息
     * @param string $data
     * @param int|float $type
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserByAccount($data, $type = 1, $field = [])
    {
        switch ($type) {
            case 1:
                $where = [
                    'account' => $data
                ];
                break;
            case 2:
                $where = [
                    'reg_code' => $data
                ];
                break;
            case 3:
                $where = [
                    'data_id' => $data
                ];
                break;
        }

        return $this->where($where)->field($field)->find();
    }

    /**
     * 根据手机号查询会员数量
     * @param array $field
     * @return float|string
     */
    public function getUserCountByMobile($field = [])
    {
        $where = [
            'mobile' => $field
        ];
        return $this->where($where)->count();
    }

    /**
     * 修改用户信息
     * @param $userId 用户的id
     * @param $data 修改的数据
     * @return Users
     */
    public function updateUserInfo($userId, $data)
    {
        $where = [
            'user_id' => $userId
        ];
        return $this->where($where)->update($data);
    }

    /**
     * 获取会员的密码盐
     * @return mixed
     */
    public function getPasswordSalt()
    {
        return $this->pass_salt;
    }

    /**
     * 获取会员的密码
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * 获取会员的密码
     * @return mixed
     */
    public function getPayPassword()
    {
        return $this->secpwd;
    }

    /*     * 获取报单人信息
     * @param $bdrId
     */

    public function getBdrInfo($bdrId)
    {
        $where = [
            'bdr_id' => (int) $bdrId
        ];
        return $this->where($where)->select();
    }

    /**
     * 获取直推人数
     * @return int
     */
    public function getTjrNum()
    {
        return $this->where('tjr_id', $this->user_id)->count();
    }

    /**
     * @param $Id
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getTjrIndex($Id)
    {
        $where = [
            'tjr_id' => (int) $Id
        ];
        $p = intval(Request::param('p'));
        $pSize = 15;
        $info = self::where($where)->limit($p * $pSize, $pSize)->select();
        return $info;
    }

    /**
     * 根据ID 获取用户推荐人数量
     * @param $userId
     * @return float|string
     */
    public static function getTjrUserNum($userId)
    {
        return self::where('tjr_id', (int) $userId)->count();
    }

    /**
     * 获取账号
     * @param string $field
     * @return array
     */
    public function getUserInfo($field = '')
    {
        return $this->column($field);
    }

    /**
     * 生成推荐码
     */
    public function getInvitationCode()
    {
        $invitationCode = get_rand_str(6);

        $next = true;
        while ($next) {
            $count = $this->where('reg_code', $invitationCode)->count();
            if ($count == 0) {
                $next = false;
                break;
            }
            $invitationCode = get_rand_str(6);
        }

        return $invitationCode;
    }

    /**
     * 获取团队业绩
     * @param \app\common\model\Users $userInfo 会员信息
     * @param int $algebra 代数 0为不限制
     * @return float
     */
    public static function getTeamPerformance($userInfo, $algebra = 0)
    {

        $userIds = self::getTeamUserId($userInfo, $algebra);

        $yj = UsersMoney::whereIn('uid', $userIds)->where('mid', 1)->sum('money');

        return $yj;
    }

    /**
     * 获取团队人数
     * @param $userInfo
     * @param int $algebra
     * @return int
     */
    public static function getTeamUserNum($userInfo, $algebra = 0)
    {
        return count(self::getTeamUserId($userInfo, $algebra));
    }

    /**
     * 获取团队会员id
     * @param \app\common\model\Users $userInfo 会员信息
     * @param int $algebra 代数 0为不限制
     * @return array
     */
    public static function getTeamUserId($userInfo, $algebra = 0)
    {
        try {
            if ($algebra) {
                $num = substr_count($userInfo['tjr_path'], ',') + $algebra;
                $userList = self::where('tjr_path', 'like', $userInfo['tjr_path'] . ',' . $userInfo['user_id'] . '%')
                        ->having('(length(`tjr_path`) - length(replace(`tjr_path`,",", ""))) <= ' . $num)
                        ->field('user_id,tjr_path')
                        ->select();
            } else {
                $userList = self::where('tjr_path', 'like', $userInfo['tjr_path'] . ',' . $userInfo['user_id'] . '%')
                        ->field('user_id,tjr_path')
                        ->select();
            }
            $userIds = get_arr_column($userList, 'user_id');
            return $userIds;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 获取会员指定层团队id
     *
     * @param array $userInfo 会员信息
     * @param int $algebra 层数
     * @return array
     */
    public static function getAssignAlgebraTeamUserId($userInfo, $algebra)
    {
        try {
            $num = substr_count($userInfo['tjr_path'], ',') + $algebra;
            $userList = self::where('tjr_path', 'like', $userInfo['tjr_path'] . ',' . $userInfo['user_id'] . '%')
                    ->having('(length(`tjr_path`) - length(replace(`tjr_path`,",", "")) - '.(strlen($userInfo['tjr_path'])-strlen(str_replace(',','', $userInfo['tjr_path']))).') = '.$num)
//                    ->having('(length(`tjr_path`) - length(replace(`tjr_path`,",", ""))) = ' . $num)
                    ->field('user_id,tjr_path')
                    ->select();
            $userIds = get_arr_column($userList, 'user_id');
            return $userIds;
        } catch (\Exception $e) {
            Log::exceptionWrite('查询会员指定某层团队失败 user_info:' . print_r($userInfo, true), $e);
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
        $levelInfo = Level::getLevelInfoById($userInfo['level']);
        $networkList = [];

        $networkList[] = [
            'id' => $userInfo['user_id'],
            'name' => $userInfo['account'],
            'prev_name' => $userInfo['tjr_id'] > 0 ? self::where('user_id', $userInfo['tjr_id'])->value('account') : '',
            'prev_id' => $userInfo['tjr_id'] > 0 ? $userInfo['tjr_id'] : '',
            'bg_color' => $levelInfo['color'],
            'open' => 'true',
            'html' => '',
            'child' => self::getNetworkList($userInfo, 5)
        ];

        return $networkList;
    }

    /**
     * 获取网络图列表
     * @param $userInfo
     * @param $endLayer
     * @param int $startLayer
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function getNetworkList($userInfo, $endLayer, $startLayer = 0)
    {
        $networkList = [];
        if (!empty($userInfo) && $endLayer > $startLayer) {
            $list = self::where('tjr_id', $userInfo['user_id'])->field('user_id,account,level')->select()->toArray();
            foreach ($list as $v) {
                $levelInfo = Level::getLevelInfoById($v['level']);
                $arr = [
                    'id' => $v['user_id'],
                    'name' => $v['account'],
                    'bg_color' => $levelInfo['color'],
                    'prev_name' => $userInfo['account'],
                    'prev_id' => $userInfo['user_id'],
                    'open' => 'true',
                    'html' => '',
                    'child' => self::getNetworkList($v, $endLayer, $startLayer + 1)
                ];
                $networkList[] = $arr;
            }
        }

        return $networkList;
    }

    /**
     * 每日赠送转盘劵
     * @param $userInfo
     * @param $endLayer
     * @param int $startLayer
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function autoDayGiveTurnNum()
    {
        $giveDayNum = intval(zf_cache('security_info.turn_day_give_num'));
        if ($giveDayNum > 0) {
            self::where('user_id', '>', 0)->update(['turn_num' => $giveDayNum]);
        }
    }

    /**
     * 每日赠视频次数
     * @param $userInfo
     * @param $endLayer
     * @param int $startLayer
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function autoDayGiveVideoNum()
    {
        $giveDayNum = intval(zf_cache('security_info.video_day_total_num'));
        if ($giveDayNum > 0) {
            self::where('user_id', '>', 0)->update(['video_num' => $giveDayNum]);
        }
    }

    /**
     * 每日赠送摇一摇
     * @param $userInfo
     * @param $endLayer
     * @param int $startLayer
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function autoDayGiveShakeNum()
    {
        $giveDayNum = intval(zf_cache('security_info.shake_day_total_num'));
        if ($giveDayNum > 0) {
            self::where('user_id', '>', 0)->update(['shake_num' => $giveDayNum]);
        }
    }

}
