<?php

namespace app\api_admin\controller;

use app\api_admin\response\ReturnCode;
use app\api_admin\service\Token;
use app\common\logic\CacheLogic;
use app\api_admin\logic\AdminUserLogic;
use app\common\model\AdminLog;
use app\common\model\AdminStatistics;
use app\common\model\Users;
use think\facade\Cache;
use think\facade\Log;
use think\facade\Request;
use think\helper\Time;

class Index extends Base
{

    /**
     * 获取菜单目录
     * @return \think\Response|\think\response\Json
     */
    public function getMenu()
    {
        $rule = (new AdminUserLogic())->getUserRules($this->adminUser['admin_id']);

        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $rule);
    }

    /**
     * 获取管理员信息
     * @return \think\Response|\think\response\Json
     */
    public function getUserInfo()
    {
        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $this->adminUser);
    }

    /**
     * 退出登录
     * @return \think\Response|\think\response\Json
     */
    public function logout()
    {
        $tokenServer = new Token();
        $res = $tokenServer->clearToken();
        if ($res) {
            return ReturnCode::showReturnCode(ReturnCode::LOGIN_CODE, '退出成功');
        } else {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '退出失败');
        }
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

        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE);
    }

    /**
     * 获取控制台信息
     * @return \think\Response|\think\response\Json
     */
    public function getConsoleInfo()
    {
        $totalUserNum = Users::count(); // 会员总量
        $newUserNum = Users::where('reg_time', 'between', Time::today())->count(); // 今日新增
        $lockUserNum = Users::where('frozen', '<>', 1)->count(); // 冻结会员数量
        $noActivateUserNum = Users::where('activate', '<>', 1)->count(); // 未激活的会员数量

        $data = [];

        $data['statistics'] = [
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

        $sysInfo = [
            'os' => PHP_OS,
            'ip' => GetHostByName($_SERVER['SERVER_NAME']),
            'phpv' => phpversion(),
            'web_server' => $_SERVER['SERVER_SOFTWARE'],
            'domain' => $_SERVER['HTTP_HOST'],
            'server_time' => date('Y-m-d H:i:s')
        ];
        $data['sys_info'] = $sysInfo;

        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $data);
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
            $list[2] = [];

            $levels = \app\common\model\grade\Level::getLevelField('level_id,name_cn,color');
            foreach ($levels as $v) {
                $list[2]['data'][] = [
                    'name' => $v['name_cn'],
                    'value' => Users::where(['level' => $v['level_id']])->count(),
                    'color' => $v['color']
                ];
            }
            return $list;
        }, 60);
        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $list);
    }

    /**
     * 上传文件
     * @return \think\Response|\think\response\Json
     */
    public function uploadImg()
    {
        $field = (Request::param('field') ? Request::param('field') : 'file');
        $dir = (Request::param('dir') ? Request::param('dir') : 'home');

        try {
            $upload = new \app\common\server\Upload();
            $res = $upload->uploadImageFile($field, $dir);
            if ($res['code'] == 1) {
                if (Request::param('get_all_url') == true) {
                    $res['data']['src'] = get_img_domain() . $res['data']['src'];
                }
                return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', $res['data']);
            } else {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, $res['msg']);
            }
        } catch (\Exception $e) {
            Log::write('上传失败: ' . $e->getMessage(), 'error');
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '上传失败');
        }
    }

    /**
     * 获取短信数量
     * @return \think\Response|\think\response\Json
     */
    public function getSmsNum()
    {
        $smsNum = Cache::remember('get_sms_num', function () {
            $smsNum = get_sms_num();

            return $smsNum;
        });
        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', max(intval($smsNum), 0));
    }

    /**
     * 测试短信发送
     * @return \think\Response|\think\response\Json
     */
    public function testSms()
    {
        $mobile = zf_cache('sms_info.test_send_mobile');
        if (!check_mobile($mobile)) {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '请先设置测试手机号');
        }

        AdminLog::addLog('测试发送短信', ['mobile' => $mobile], $this->adminUser['admin_id']);
        $res = send_sms($mobile, "这是一条测试短信 验证码3293");
        if ($res['status'] == 1) {
            return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '发送成功');
        } else {
            return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, $res['msg']);
        }
    }

    /**
     * 测试邮件发送
     * @return \think\Response|\think\response\Json
     */
    public function testEmail()
    {
        if (Request::isPost()) {
            $email = zf_cache('smtp_info.test_send_email');
            if (!check_mail($email)) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '请先设置测试邮箱账号');
            }

            try {
                AdminLog::addLog('测试发送邮件', ['email' => $email], $this->adminUser['admin_id']);
                $res = send_mail($email, '测试', '测试', date('Y-m-d H:i:s') . "\n这是一封测试邮件");
                if ($res === true) {
                    return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '发送成功');
                } else {
                    return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, $res);
                }
            } catch (\Exception $e) {
                return ReturnCode::showReturnCode(ReturnCode::ERROR_CODE, '发送失败');
            }
        }
    }

    /**
     * 获取钱包名称
     * @return \think\Response|\think\response\Json
     */
    public function getMoneyName()
    {
        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', \app\common\model\money\Money::getMoneyNames());
    }

    /**
     * 获取货币名称
     * @return \think\Response|\think\response\Json
     */
    public function getBlockName()
    {
        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', \app\common\model\block\Block::getBlockNames());
    }

    /**
     * 获取奖金名称
     * @return \think\Response|\think\response\Json
     */
    public function getBonusName()
    {
        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', \app\common\model\Bonus::getBonusNames());
    }
}
