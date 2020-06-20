<?php

namespace app\common\model\auth;

use think\facade\Log;
use think\facade\Request;
use app\common\model\AdminLog;
use app\common\model\Common;

class AuthGroup extends Common
{
    protected $name = 'auth_group';

    public static $superAdminNames = [
        'admin' => '超级管理员',
        'seller' => '商家超级管理员'
    ];

    /**
     * 关联模型
     * @return \think\model\relation\HasOne
     */
    public function profile()
    {
        return $this->hasOne('AuthGroupAccess', 'group_id');
    }

    /**
     * 添加角色
     * @param string $moduleName
     * @return int|string
     * @throws \Exception
     */
    public function addGroup($moduleName = '')
    {
        $moduleName = ($moduleName ? $moduleName : Request::module());

        if (!$title = Request::param('title')) {
            exception('请输入角色名称');
        }
        $ruleIds = Request::param('menu') ? implode(',', Request::param('menu')) : '';

        $data = [
            'title' => $title,
            'module_name' => $moduleName
        ];

        $ruleIds && $data['rules'] = $ruleIds;
        Request::param('description') && $data['description'] = Request::param('description');

        return self::insert($data);
    }

    /**
     * 编辑角色
     * @return int|string
     * @throws \Exception
     */
    public function editGroup()
    {
        if (!$title = Request::param('title')) {
            exception('请输入角色名称');
        }
        $ruleIds = Request::param('menu') ? implode(',', Request::param('menu')) : '';

        $this->title = $title;
        $ruleIds && $this->rules = $ruleIds;
        Request::param('description') && $this->description = Request::param('description');

        return self::save();
    }


    /**
     * 查询角色列表
     * @param array $where
     * @param int $page
     * @param int $pageSize
     * @return array|\PDOStatement|string|\think\Collection
     */
    public function getGroupList($where = [], $page = 0, $pageSize = 10)
    {
        try {
            return self::where($where)->limit($page * $pageSize, $pageSize)->order(['id' => 'asc'])->select();
        } catch (\Exception $e) {
            Log::write('查询角色列表失败: ' . $e->getMessage(), 'error');

            return [];
        }
    }

    /**根据id 获取数据字段
     * @param array $Id用户id
     * @param string $field字段
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getField($field = '')
    {
        $where = [
            'status' => 1
        ];
        if ($field != '') {
            return $this->where($where)->column($field);
        }
    }

    /**
     * 生成超级管理员的权限
     * @param string $moduleName
     * @return void
     */
    public static function generateSuperAdminAuth()
    {
        foreach (self::$superAdminNames as $k => $v) {
            $moduleName = $k;
            $superAdminName = $v;
            $auth = implode(',', AuthRule::where('status', 1)->where('module_name', $moduleName)->column('id'));
            $info = self::where('title', $superAdminName)->where('module_name', $moduleName)->find();
            if ($info) {
                $info->rules = $auth;
                $info->save();
            } else {
                $data = [
                    'title' => $superAdminName,
                    'status' => 1,
                    'rules' => $auth,
                    'module_name' => $moduleName,
                    'description' => '总管理员拥有所有权限'
                ];

                self::insertGetId($data);
            }
        }

        return true;
    }
}
