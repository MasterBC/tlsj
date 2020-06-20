<?php

namespace app\api_admin\logic;

use app\common\model\AdminUser;
use think\Exception;
use think\facade\Request;
use app\facade\Password;
use think\facade\Session;
use think\facade\Cache;
use app\common\model\AdminLog;
use think\facade\Log;

class AdminUserLogic
{

    /**
     * 管理员登陆
     * @return array
     * @throws \Exception
     */
    public function doLogin()
    {
        try {
            $adminUser = new AdminUser();
            $username = Request::param('username');
            $password = Request::param('password');
            $adminUser = $adminUser->getUserByUsername($username);
        } catch (\Exception $e) {
            Log::write('登录查询会员失败：' . $e->getMessage(), 'error');

            throw new Exception('登陆失败');
        }
        if (!$adminUser) {
            throw new Exception('用户名或者密码错误');
        }
        if (!Password::checkPassword($password, $adminUser)) {
            AdminLog::addLog('密码错误', ['password' => $password], $adminUser['admin_id']);
            throw new Exception('用户名或者密码错误');
        }
        $adminUser->last_login = time();
        $adminUser->last_ip = Request::ip();
        $adminUser->save();

        $this->delCache($adminUser['admin_id']);
        AdminLog::addLog('登陆成功', [], $adminUser['admin_id']);

        return $adminUser->toArray();
    }

    /**
     * 获取管理员权限
     * @param int $adminId 管理员id
     * @return mixed
     */
    public function getUserRules($adminId)
    {
        $cacheKey = 'api_admin:user:menu:' . $adminId;
        $model = new \app\common\model\auth\AuthGroupAccess();
        $menu = Cache::remember($cacheKey, function () use ($adminId, $model) {

            $leftMenus = $model->getUserRules($adminId);
            $rules = [];
            foreach ($leftMenus as $v) {
                $arr = $v;
                if (isset($arr[$arr['id']])) {
                    unset($arr[$arr['id']]);
                }
                $rules[$v['pid']][] = $arr;
                if (isset($v[$v['id']])) {
                    foreach ($v[$v['id']] as $val) {
                        $rules[$val['pid']][] = $val;
                    }
                }
            }
            $menus = $this->getMenusColumn($rules);
            file_put_contents(__DIR__ . '/test.log', print_r($menus, true));


            $zhuye = [
                'title' => '主页'
                , 'icon' => 'layui-icon-home'
                , 'list' => [[
                    'title' => '控制器'
                    , 'jump' => '/'
                ]]
            ];
            array_unshift($menus, $zhuye);

            return $menus;
        });
        return $menu;
        return model('AuthGroupAccess')->getUserRules(Session::get('admin_id'));
    }


    /**
     * 排序权限
     * @param array $rules 权限组
     * @param int $pid 父级id
     * @return array
     */
    private function getMenusColumn($rules, $pid = 0)
    {
        $arr = [];
        if (isset($rules[$pid])) {
            foreach ($rules[$pid] as $k => $v) {
                if ($v['pid'] == $pid) {
                    $name = strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $v['name']));
                    $arr2 = [
                        'title' => $v['title']
                        , 'icon' => $v['icon'] ?? 'layui-icon-more-vertical'
                        , 'jump' => $name
                    ];
                    if ($pid == 0) {
                        $arr2['name'] = $name;
                    } else {
                        $arr2['name'] = explode('/', $name)[1];
                    }
                    if (isset($rules[$v['id']]) && $rules[$v['id']]) {
                        $arr2['list'] = $this->getMenusColumn($rules, $v['id']);
                    }
                    $arr[] = $arr2;
                }
            }
        }
        return $arr;
    }


    /**
     * 清除缓存
     * @param int $adminId 管理员id
     */
    public function delCache($adminId)
    {
        // 清除菜单目录缓存
        $cacheKey = 'api_admin:user:menu:' . $adminId;
        Cache::rm($cacheKey);
        // 清除权限规则缓存
        $cacheKey = Request::module() . ':user:rules:' . $adminId;
        Cache::rm($cacheKey);
    }

}