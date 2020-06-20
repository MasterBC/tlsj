<?php

namespace app\wap\controller;

use app\common\logic\UserAuthNameLogic;
use app\common\model\UserAuthName;
use app\common\logic\UserDataLogic;
use app\common\model\block\Block;
use app\common\model\grade\Level;
use app\common\model\grade\Leader;
use app\common\model\Users;
use app\common\model\block\UsersBlock;
use app\common\server\Log;
use app\wap\logic\ProductLogic;
use think\Request;
use app\common\logic\UserLogic;
use app\common\model\BonusLog;
use app\common\model\Notice;
use app\common\model\About;
use app\common\model\money\UsersMoney;
use app\common\model\money\MoneyLog;
use app\common\model\turn\Turn as TurnModel;
use think\facade\Session;
use app\common\model\product\Product as ProductModel;
use app\common\model\product\UsersProduct;
use app\common\model\UsersRedEnvelopeLog;

/**
 * Class User
 * @package app\wap\controller
 */
class User extends Base
{

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct();

        $this->assign('productNameArr', ProductModel::getProductNames());
    }

    private function test(Request $request, ProductLogic $productLogic)
    {
        $res = $productLogic->randomExtractionProduct($this->user);
//        $server = new \api\bxm\Server();
//        $res = $server->getInspireVideo('ios', md5(time()));

        dump($res);
    }

    /**
     * 会员首页
     * @return \think\response\View
     */
    public function index(Notice $noticeModel, ProductLogic $productLogic)
    {
        $blockNames = Block::getBlockNames();
        $userBlocks = UsersBlock::getAmountsByUid($this->user_id, 1);
        $levelInfo = Level::getLevelInfoById($this->user['level']);
        $totalBonus = BonusLog::getBonusSumByUid($this->user_id);

        //奖品数组
        $turnModel = new TurnModel();
        $prizeArr = $turnModel->where('status', 1)->select()->toArray();

        $prize = [];
        foreach ($prizeArr as $k => $v) {
            $prize[] = [
                'id' => $v['id'],
                'name' => $v['name'],
                'image' => get_img_show_url($v['img']),
                'rank' => ($k + 1),
                'percent' => $v['is_per'],
            ];
        }

        $productList = ProductModel::column('picture,product_name,recovery_amount', 'id');
        foreach ($productList as $k => $v) {
            $productList[$k]['picture'] = get_img_show_url($v['picture']);
        }

        $userProductCount = UsersProduct::where('user_id', $this->user_id)
                ->where('status', 1)
                ->whereIn('product_id', '38,39,40,41,42,43,44,45')
                ->group('product_id')
                ->column('count(id)', 'product_id');
        if ($this->user['last_video_income']) {
            Users::where('user_id', $this->user_id)->update([
                'last_video_income' => 0
            ]);
        }
        return view('user/index', [
            'productList' => $productList,
            'userProductCount' => $userProductCount,
            'noticeList' => $noticeModel->getUserUnreadNoticeList($this->user),
            'blockNames' => $blockNames,
            'userBlocks' => $userBlocks,
            'totalBonus' => $totalBonus,
            'levelInfo' => $levelInfo,
            'prizeArr' => json_encode($prize, JSON_UNESCAPED_UNICODE),
            'maxProductInfo' => ProductModel::getProductInfoById(UsersProduct::getUserMaxProductIdByUserId($this->user_id)),
            'lotteryProducts' => $productLogic->getLotteryProducts($this->user),
            'isvideo' => ($this->user['video_num'] <= 0) ? 0 : 1,
        ]);
    }

    /**
     * 我的 个人中心
     * @return \think\response\View
     */
    public function myInfo()
    {
        // 获取推广地址并加密
        $tgUrl = url("reg/index", ["code" => $this->user['reg_code']], false, true);
        $this->assign('tgUrl', $tgUrl);
        if ($this->user['tjr_id'] > 0) {
            $this->assign('tjrUserInfo', Users::where('user_id', $this->user['tjr_id'])->find());
        } else {
            $this->assign('tjrUserInfo', ['account' => '', 'user_id' => '']);
        }

        return view('user/my_info', [
            'leaderNameInfo' => Leader::getLeaderNames(),
            'levelInfo' => Level::getLevelInfoById($this->user['level']),
            'authNameStatusData' => UserAuthName::$statusData,
            'authNameStatus' => intval(UserAuthName::where(['uid' => $this->user_id])->value('status')),
            'work_total_hongbao_money' => floatval(zf_cache('security_info.work_total_hongbao_money')),
            'upgrade_total_hongbao_money' => floatval(zf_cache('security_info.upgrade_total_hongbao_money')),
            'random_total_hongbao_money' => floatval(zf_cache('security_info.random_total_hongbao_money')),
            'randomRedEnvelopeStatus' => UsersRedEnvelopeLog::getUserRandomRedEnvelopeStatus($this->user_id),
            'userPendingUpgradeRedEnvelopeInfo' => UsersRedEnvelopeLog::getUserPendingUpgradeRedEnvelopeInfo($this->user_id),
            'userTodaySignRedEnvelopeStatus' => UsersRedEnvelopeLog::getUserTodaySignRedEnvelopeStatus($this->user_id)
        ]);
    }

    /**
     * 领取红包
     *
     * @author gkdos
     * 2019-09-24T10:11:00+0800
     */
    public function receivingARedEnvelope(Request $request)
    {
        if ($request->isPost()) {
            try {
                $userLogic = new \app\wap\logic\UserLogic();
                $res = $userLogic->receivingARedEnvelope($this->user);
                if ($res['code'] == 1) {
                    return json()->data([
                                'code' => 1,
                                'msg' => $res['msg'],
                                'data' => $res['data']
                    ]);
                } else {
                    return json()->data([
                                'code' => -1,
                                'msg' => $res['msg']
                    ]);
                }
            } catch (\Exception $e) {
                Log::exceptionWrite('领取红包失败', $e);
                return json()->data(['code' => -1, 'msg' => '领取失败']);
            }
        }
    }

    /**
     * 设置页面
     * @return \think\response\View
     */
    public function setUp()
    {
        return view('user/setup', [
            'authNameStatusData' => UserAuthName::$statusData,
            'authNameStatus' => intval(UserAuthName::where(['uid' => $this->user_id])->value('status'))
        ]);
    }

    /**
     * 修改会员头像
     * @param Request $request
     * @return \think\response\Json
     */
    public function saveUserHeadImg(Request $request)
    {
        if ($request->isPost()) {
            $this->user->head = $request->param('src', '', 'trim');
            $this->user->save();

            return json()->data(['code' => 1, 'msg' => '修改成功']);
        }
    }

    /**
     * 修改登录密码操作
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function saveLoginPassword(Request $request)
    {
        if ($request->isAjax()) {
            $UserLogic = new UserLogic();
            $data = $request->post();

            $result = $this->validate($data, 'app\wap\validate\SavePassword');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }
            try {
                $UserLogic->doSaveLoginPassword();

                return json(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            return view('user/set_password', ['user_id' => $this->user_id]);
        }
    }

    /**
     * 找回登录密码
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function forLoginPassword(Request $request)
    {
        if ($request->isAjax()) {
            $UserLogic = new UserLogic();
            $data = $request->post();

            $result = $this->validate($data, 'app\wap\validate\ForPassword');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }

            try {
                $UserLogic->ForLoginPassword($this->user);

                return json(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            return view('user/for_password', ['user_id' => $this->user_id]);
        }
    }

    /**
     * 修改二级密码
     * @param Request $request
     * @return \think\response\View
     */
    public function saveSecpwd(Request $request)
    {
        $userInfo = $this->user;
        if ($request->isAjax()) {
            $UserLogic = new UserLogic();
            $data = $request->post();
            if ($userInfo['secpwd'] != '') {
                $result = $this->validate($data, 'app\wap\validate\SaveSecpwd');
            } else {
                $result = $this->validate($data, 'app\wap\validate\SaveSecpwd.edit');
            }

            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }

            try {
                $UserLogic->doSaveSecpwd($this->user_id);

                return json(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            return view('user/set_secpwd', ['userInfo' => $userInfo]);
        }
    }

    /**
     * 找回二级密码
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function forSecpwd(Request $request)
    {
        if ($request->isAjax()) {
            $UserLogic = new UserLogic();
            $data = $request->post();

            $result = $this->validate($data, 'app\wap\validate\ForSecpwd');

            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }
            try {
                $UserLogic->doForSecpwd($this->user_id, $this->user);

                return json(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            return view('user/for_secpwd');
        }
    }

    /**
     * 个人资料修改
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function personalInfo(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->post();
            $result = $this->validate($data, 'app\common\validate\UserData');
            if ($result !== true) {
                return json(['code' => -1, 'msg' => $result]);
            }
            $UserDataLogic = new UserDataLogic();
            try {
                $UserDataLogic->updateUserData($this->user, $this->userData);
                return json(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            return view('user/personal_info', [
                'userId' => $this->user_id,
                'userInfo' => $this->user,
                'userData' => $this->userData
            ]);
        }
    }

    /**
     * 个人呢称修改
     * @param Request $request
     * @return \think\response\Json|\think\response\View
     */
    public function saveNickname(Request $request)
    {
        if ($request->isPost()) {
            $data = $request->post();
            $UserDataLogic = new UserDataLogic();
            try {
                $UserDataLogic->updateUserNickname($this->user);
                return json(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            return view('user/save_nickname', [
                'userInfo' => $this->user
            ]);
        }
    }

    /**
     * 用户修改手机号码
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function saveMobile(Request $request)
    {
        $userDataModel = new UsersData();
        $userDataInfo = $userDataModel->getUserDataField($this->user['data_id'], 'id,mobile');
        $UserDataLogic = new UserDataLogic();
        if ($request->isPost()) {
            try {
                $UserDataLogic->saveMobile();

                return json(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => $e->getMessage()]);
            }
        }
        return view('user/save_mobile', ['userDataInfo' => $userDataInfo]);
    }

    /**
     * 绑定微信
     * @param Request $request
     * @param UserDataLogic $userDataLogic
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function bindWechat(Request $request, UserLogic $userLogic)
    {
        if ($request->isPost()) {
            try {
                $userLogic->bindWechat($this->user);
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            return view('user/bind_wechat_info');
        }
    }

    /**
     * 绑定支付宝
     * @param Request $request
     * @param UserDataLogic $userDataLogic
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function bindAlipay(Request $request, UserLogic $userLogic)
    {
        if ($request->isPost()) {
            try {
                $userLogic->bindAlipay($this->user);
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            return view('user/bind_alipay_info');
        }
    }

    /**
     * 实名认证
     * @param Request $request
     * @param UserAuthNameLogic $UserAuthNameLogic
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function realNameAuth(Request $request, UserAuthNameLogic $UserAuthNameLogic)
    {
        if ($request->isPost()) {
            try {
                $UserAuthNameLogic->addUserAuthNameData($this->user);
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } catch (\Exception $e) {
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            $this->assign('info', UserAuthName::where(['uid' => $this->user_id])->find());
            return view('user/auth_info');
        }
    }

    /*  安全中心
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function securityInfo(Request $request)
    {
        return view('user/security_info');
    }

    /*  收款账户绑定
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function payInfo(Request $request)
    {
        return view('user/pay_info');
    }

    /*  关于我们
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function aboutInfo(Request $request)
    {
        return view('user/about_info', ['aboutInfo' => About::getRegistrationAgreement(2)]);
    }

    /*  上班
     * @param Request $request
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function workInfo(Request $request)
    {
        return view('user/work_info', ['aboutInfo' => About::getRegistrationAgreement(2)]);
    }

    /**
     * 签到
     *
     * @param Request $request
     * @return \think\Response|\think\response\Json
     * @author No_door
     * 2019-07-10 17:53:25
     */
    public function dayUserSign(Request $request)
    {
        if ($request->isAjax()) {
            if ($this->user['sign_in_time'] > strtotime(date('Y-m-d'))) {
                return json(['code' => -1, 'msg' => '每天只能签到一次哦~']);
            }
            try {
                $daySitgnNum = floatval(zf_cache('security_info.sign_day_give_mid_num'));
                if ($daySitgnNum > 0) {
                    (new UsersMoney())->amountChange($this->user_id, 1, $daySitgnNum, 160, '签到', [
                        'come_uid' => $this->user_id
                    ]);
                }
                $this->user->sign_in_time = time();
                $this->user->save();
                return json(['code' => 1, 'msg' => '签到成功']);
            } catch (\Exception $e) {
                return json(['code' => -1, 'msg' => '签到失败']);
            }
        }
    }

    /**
     * 用户登出
     * @return \think\response\Json
     */
    public function outLogin(Request $request)
    {
        Users::clearSession();
        if ($request->isAjax()) {
            return json(['code' => 0]);
        } else {
            return redirect('Login/accountIndex');
        }
    }
	
	public function moneyLog(Request $request)
	{
		if ($request->isAjax()) {
            $moneyLog = new MoneyLog();
            $page = $request->get('p', '1', 'intval');
            $pageSize = $request->get('size', '10', 'intval');
			$list = MoneyLog::where('uid', $this->user_id)->order('id', 'desc')->limit($page * $pageSize, $pageSize)->select();
            $this->assign('list', $list);
            return view('user/money_log_ajax');
        } else {
            return view('user/money_log');
        }
	}

}
