<?php

namespace app\admin\controller;

use app\common\model\AdminStatistics;
use app\common\model\auth\AuthGroup;
use think\Db;
use think\facade\Cache;
use think\Request;
use org\AesSecurity;
use think\helper\Time;
use think\facade\Session;
use app\common\model\Users;
use app\common\model\Message;
use app\common\model\AdminLog;
use app\common\model\AdminUser;
use app\common\logic\CacheLogic;
use app\common\logic\AdminUserLogic;
use app\common\model\grade\LevelLog;
use app\common\server\AutomaticMining;
use app\common\model\work\MoneyWebDay;

class Index extends Base
{

    /**
     * 赠送转盘劵
     * @return \think\Response|\think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function autoGiveTurnDayNum()
    {
        (new Users())->autoDayGiveTurnNum();
        return json()->data(['code' => 1, 'msg' => '操作成功']);
    }

    /**
     * 赠送视频次数
     * @return \think\Response|\think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function autoGiveVideoDayNum()
    {
        (new Users())->autoDayGiveVideoNum();
        return json()->data(['code' => 1, 'msg' => '操作成功']);
    }

    /**
     * 摇一摇
     * @return \think\Response|\think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function autoGiveShakeDayNum()
    {
        (new Users())->autoDayGiveShakeNum();
        return json()->data(['code' => 1, 'msg' => '操作成功']);
    }

    /**
     * 会员根据算力升级
     * @return \think\Response|\think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function autoMoneyNumLevel()
    {
        (new LevelLog())->autoUserLevel();
        return json()->data(['code' => 1, 'msg' => '操作成功']);
    }

    /**
     * 根据算力 生产鱼币
     * @return \think\Response|\think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function autoMaticMining()
    {
        (new AutomaticMining)->automaticMining();
        return json(['code' => 1, 'msg' => '操作成功']);
    }

    /**
     * 分红龙每天的分红收放
     * @return \think\Response|\think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function autoDayBonusMoney()
    {
        (new MoneyWebDay())->autoUserBonus();
        return json(['code' => 1, 'msg' => '操作成功']);
    }

    public function index()
    {
//        AuthGroup::generateSuperAdminAuth();
        $adminUserLogic = new AdminUserLogic();

        $rules = $adminUserLogic->getUserRules();

        $this->assign('rules', $rules);
        $this->assign('adminUserInfo', (new AdminUser())->getAdminUserInfo());

        return view('index/index');
    }

    /**
     * 获取会员留言数量
     * @param Message $messageModel
     * @return \think\Response|\think\response\Json
     */
    public function getMessageNum(Message $messageModel)
    {
        $messageNum = $messageModel->count();

        $data = [
            'code' => 1,
            'num' => $messageNum
        ];

        return json()->data($data);
    }

    /**
     * 获取短信数量
     */
    public function getSmsNum()
    {
        $smsNum = get_sms_num();

        return json()->data(['code' => 1, 'msg' => max(intval($smsNum), 0)]);
    }

    /**
     * 测试短信发送
     */
    public function testSms()
    {
        $mobile = zf_cache('sms_info.test_send_mobile');
        if (!check_mobile($mobile)) {
            return json()->data(['code' => -1, 'msg' => '请先设置测试手机号']);
        }

        AdminLog::addLog('测试发送短信', ['mobile' => $mobile]);
        $res = send_sms($mobile, "这是一条测试短信 验证码3293");
        if ($res['status'] == 1) {
            return json()->data(['code' => 1, 'msg' => '发送成功']);
        } else {
            return json()->data(['code' => -1, 'msg' => $res['msg']]);
        }
    }

    /**
     * 测试邮件发送
     */
    public function testEmail()
    {
        $email = zf_cache('smtp_info.test_send_email');
        if (!check_mail($email)) {
            return json()->data(['code' => -1, 'msg' => '请先设置测试邮箱账号']);
        }

        try {
            AdminLog::addLog('测试发送邮件', ['email' => $email]);
            $res = send_mail($email, '测试', '测试', date('Y-m-d H:i:s') . "\n这是一封测试邮件");
            if ($res === true) {
                return json()->data(['code' => 1, 'msg' => '发送成功']);
            } else {
                return json()->data(['code' => -1, 'msg' => $res]);
            }
        } catch (\Exception $e) {
            return json()->data(['code' => 1, 'msg' => '发送失败']);
        }
    }

    /**
     * 退出登录
     * @return \think\response\Redirect
     */
    public function logout()
    {
        Session::delete(AdminUser::SESSION_NAME);

        return json()->data(['code' => 1, 'msg' => '退出成功']);
    }

    /**
     * 清除缓存
     * @param CacheLogic $cacheLogic
     * @return \think\Response|\think\response\Json
     */
    public function cleanCache(CacheLogic $cacheLogic)
    {
        $type = \think\facade\Request::param('type', 'all');
        switch ($type) {
            case 'temp':
                $cacheLogic->clearTempCache();
                break;
            case 'data':
                $cacheLogic->clearDataCache();
                break;
            case 'db':
                $cacheLogic->clearDbCache();
                break;
            case 'config':
                $cacheLogic->clearConfigCache();
                break;
            case 'all':
                $cacheLogic->clearAllCache();
                break;
        }

//        $cacheLogic->refushCaceh();

        return json()->data(['code' => 1, 'msg' => '操作成功']);
    }

    public function welcome()
    {
        $this->assign('sysInfo', $this->getSysInfo());
        return view('index/welcome');
    }

    /**
     * 获取会员信息
     */
    public function getStatistics()
    {
        $totalUserNum = Users::count(); // 会员总量
        $newUserNum = Users::where('reg_time', 'between', Time::today())->count(); // 今日新增
        $lockUserNum = Users::where('frozen', '<>', 1)->count(); // 冻结会员数量
        $noActivateUserNum = Users::where('activate', '<>', 1)->count(); // 未激活的会员数量

        $data = [
            'user' => [
                'total_user_num' => $totalUserNum,
                'new_user_num' => $newUserNum,
                'lock_user_num' => $lockUserNum,
                'no_activate_user_num' => $noActivateUserNum
            ],
            'amount' => [
                'total_income' => AdminStatistics::getTotalIncome(),
                'today_income' => AdminStatistics::getTodayIncome(),
                'total_expenditure' => AdminStatistics::getTotalExpenses(),
                'today_expenditure' => AdminStatistics::getTodayExpenses()
            ]
        ];

        return json()->data($data);
    }

    /**
     * 获取系统信息
     */
    public function getSysInfo()
    {
        $sys_info['os'] = PHP_OS;
        $sys_info['zlib'] = function_exists('gzclose') ? 'YES' : 'NO'; //zlib
        $sys_info['safe_mode'] = (boolean) ini_get('safe_mode') ? 'YES' : 'NO'; //safe_mode = Off
        $sys_info['timezone'] = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sys_info['curl'] = function_exists('curl_init') ? 'YES' : 'NO';
        $sys_info['web_server'] = $_SERVER['SERVER_SOFTWARE'];
        $sys_info['phpv'] = phpversion();
        $sys_info['ip'] = GetHostByName($_SERVER['SERVER_NAME']);
        $sys_info['fileupload'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknown';
        $sys_info['max_ex_time'] = @ini_get("max_execution_time") . 's'; //脚本最大执行时间
        $sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
        $sys_info['domain'] = $_SERVER['HTTP_HOST'];
        $sys_info['memory_limit'] = ini_get('memory_limit');
        $mysqlinfo = Db::query("SELECT VERSION() as version");
        $sys_info['mysql_version'] = $mysqlinfo[0]['version'];
        if (function_exists("gd_info")) {
            $gd = gd_info();
            $sys_info['gdinfo'] = $gd['GD Version'];
        } else {
            $sys_info['gdinfo'] = "未知";
        }
        return $sys_info;
    }

    public function welcome2()
    {
        return view('index/welcome2');
    }

    public function welcome3()
    {
        return view('index/welcome3');
    }

    /**
     * 获取地址
     * @param Request $request
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRegion(Request $request)
    {
        $parentId = $request->param('parent_id');
        $selected = $request->param('selected');
        $data = model('Region')->where(["parent_id" => $parentId])->select();
        $html = '';
        if ($data) {
            foreach ($data as $h) {
                if ($h['id'] == $selected) {
                    $html .= "<option value='{$h['id']}' selected>{$h['name_cn']}</option>";
                }
                $html .= "<option value='{$h['id']}'>{$h['name_cn']}</option>";
            }
        }
        echo $html;
    }

    /**
     * 获取乡镇
     * @param Request $request
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTwon(Request $request)
    {
        $parentId = $request->param('parent_id');
        $data = model('Region')->where(["parent_id" => $parentId])->select();
        $html = '';
        if ($data) {
            foreach ($data as $h) {
                $html .= "<option value='{$h['id']}'>{$h['name_cn']}</option>";
            }
        }
        if (empty($html)) {
            echo '0';
        } else {
            echo $html;
        }
    }

    /**
     * 获取图表会员信息
     * @return \think\response\Json
     */
    public function getChartUserInfo()
    {
        $list = Cache::remember('admin_wekk_users', function () {
                    $list[1] = [];
                    for ($i = 6; $i >= 0; $i--) {
                        $list[1][$i]['time'] = date('m-d', strtotime("-" . $i . " day", strtotime(date('Ymd'))));
                        $list[1][$i]['count'] = Users::where('reg_time', 'between', [strtotime(date('Ymd')) - ($i) * 86400, strtotime(date('Ymd')) - ($i - 1) * 86400])->count();
                    }
                    sort($list[1]);
                    return $list;
                }, 60);
        return json()->data($list);
    }

}
