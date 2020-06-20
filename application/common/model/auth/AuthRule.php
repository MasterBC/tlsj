<?php

namespace app\common\model\auth;

use think\facade\Request;
use app\common\model\Common;

class AuthRule extends Common
{
    protected $name = 'auth_rule';


    /**
     * 显示菜单
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function selectAllMenu()
    {
        $where = [
            'status' => 1,
            'module_name' => Request::module()
        ];

        return self::where($where)->order('sort desc')->order('id asc')->select()->toArray();
    }

    /**
     * 根据规则id数组获取菜单
     * @param $rules_arr
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMenus($rules_arr)
    {
        return $this->where('status', 1)->where('is_menu', 1)->whereIn('id', implode(',', $rules_arr))->order('sort desc,id asc')->select()->toArray();
    }

    /**
     * 删除不显示的权限
     */
    public static function delNotShowAuth()
    {
        $authList = self::where('status', 0)->select()->toArray();
        $ids = get_arr_column($authList, 'id');
        self::whereIn('id', $ids)->delete();

        $authList = self::whereIn('pid', $ids)->select()->toArray();
        $ids = get_arr_column($authList, 'id');
        self::whereIn('id', $ids)->delete();

        $authList = self::whereIn('pid', $ids)->select()->toArray();
        $ids = get_arr_column($authList, 'id');
        self::whereIn('id', $ids)->delete();

        return true;
    }
}