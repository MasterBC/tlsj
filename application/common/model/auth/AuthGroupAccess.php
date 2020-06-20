<?php

namespace app\common\model\auth;

use think\facade\Request;
use app\common\model\Common;

class AuthGroupAccess extends Common
{
    protected $name = 'auth_group_access';

    /**
     * 获取管理员权限
     * @param $user_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserRules($user_id)
    {
        $where = [
            'a.uid' => $user_id,
            'a.module_name' => Request::module()
        ];
        $codeTable = AuthGroup::getTable();
        $rules = $this->alias('a')
            ->where($where)
            ->join("$codeTable b", 'b.id=a.group_id')
            ->field('b.rules')
            ->select();

        if (!$rules) {
            return [];
        }

        $rules_str = '';
        foreach ($rules as $v) {
            $rules_str .= $v['rules'] . ',';
        }

        $rules_str = rtrim($rules_str, ',');

        $rules_arr = array_unique(explode(',', $rules_str));

        $menus = (new AuthRule())->getMenus($rules_arr);
        $menus = get_column($menus, 2);

        return $menus;
    }
}