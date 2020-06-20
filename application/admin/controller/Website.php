<?php

namespace app\admin\controller;

use app\common\model\Bank;
use app\common\model\Region;
use app\common\model\Country;
use app\common\model\UserBank;
use think\db\Where;
use think\facade\Log;
use think\Request;
use app\common\logic\WebsiteLogic;
use app\common\model\AdminLog;
use app\common\model\block\Block as BlockModel;
use app\common\model\money\Money as MoneyModel;

class Website extends Base
{

    /**
     * 基本信息管理
     * @param Request $request
     * @param WebsiteLogic $websiteLogic
     * @return mixed|\think\response\Json
     */
    public function webInfo(Request $request, WebsiteLogic $websiteLogic)
    {
        $websiteLogic->fileName = 'web_info';

        if ($request->isPost()) {

            $param = $request->param();

            $res = $websiteLogic->setConfig($param, '基本信息管理');
            if ($res) {
                AdminLog::addLog('修改网站基本信息', $request->param());
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            $this->assign('config', $websiteLogic->getData());

            return $this->fetch('website/web_info');
        }
    }

    /**
     * 邮件参数配置
     * @param Request $request
     * @param WebsiteLogic $websiteLogic
     * @return mixed|\think\response\Json
     */
    public function smtpInfo(Request $request, WebsiteLogic $websiteLogic)
    {
        $websiteLogic->fileName = 'smtp_info';

        if ($request->isPost()) {

            $param = $request->param();

            $res = $websiteLogic->setConfig($param, '邮件参数配置');
            if ($res) {
                AdminLog::addLog('修改邮件参数配置', $request->param());
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            $this->assign('config', $websiteLogic->getData());

            return $this->fetch('website/smtp_info');
        }
    }

    /**
     * 短信参数配置
     * @param Request $request
     * @param WebsiteLogic $websiteLogic
     * @return mixed|\think\response\Json
     */
    public function smsInfo(Request $request, WebsiteLogic $websiteLogic)
    {
        $websiteLogic->fileName = 'sms_info';

        if ($request->isPost()) {

            $param = $request->param();

            $res = $websiteLogic->setConfig($param, '短信参数配置');
            if ($res) {
                AdminLog::addLog('修改短信参数配置', $request->param());
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            $this->assign('config', $websiteLogic->getData());

            return $this->fetch('website/sms_info');
        }
    }

    /**
     * 注册参数设置
     * @param Request $request
     * @param WebsiteLogic $websiteLogic
     * @return mixed|\think\response\Json
     */
    public function regInfo(Request $request, WebsiteLogic $websiteLogic)
    {
        $websiteLogic->fileName = 'reg_info';

        if ($request->isPost()) {

            $param = $request->param();

            $res = $websiteLogic->setConfig($param, '注册参数配置');
            if ($res) {
                AdminLog::addLog('修改注册参数配置', $request->param());
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            $this->assign('config', $websiteLogic->getData());
            $this->assign('moneyNames', get_money_name());
            return $this->fetch('website/reg_info');
        }
    }

    /**
     * 支付参数设置
     * @param Request $request
     * @param WebsiteLogic $websiteLogic
     * @return mixed|\think\response\Json
     */
    public function payInfo(Request $request, WebsiteLogic $websiteLogic)
    {
        $websiteLogic->fileName = 'pay_info';

        if ($request->isPost()) {

            $param = $request->param();

            $res = $websiteLogic->setConfig($param, '支付参数设置');
            if ($res) {
                AdminLog::addLog('修改支付参数设置', $request->param());
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            $this->assign('config', $websiteLogic->getData());

            return $this->fetch('website/pay_info');
        }
    }

    /**
     * 支付参数设置
     * @param Request $request
     * @param WebsiteLogic $websiteLogic
     * @return mixed|\think\response\Json
     */
    public function sellerInfo(Request $request, WebsiteLogic $websiteLogic)
    {
        $websiteLogic->fileName = 'store_info';

        if ($request->isPost()) {

            $param = $request->param();

            $res = $websiteLogic->setConfig($param, '商家参数设置');
            if ($res) {
                AdminLog::addLog('修改商家参数设置', $request->param());
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            $this->assign('config', $websiteLogic->getData());

            return $this->fetch('website/store_info');
        }
    }

    /**
     * 安全参数设置
     * @param Request $request
     * @param WebsiteLogic $websiteLogic
     * @return mixed|\think\response\Json
     */
    public function securityInfo(Request $request, WebsiteLogic $websiteLogic)
    {
        $websiteLogic->fileName = 'security_info';

        if ($request->isPost()) {

            $param = $request->param();

            $res = $websiteLogic->setConfig($param, '注册参数配置');
            if ($res) {
                AdminLog::addLog('修改安全参数配置', $request->param());
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            $this->assign('config', $websiteLogic->getData());
            $this->assign('webmoneyIdNameArr', MoneyModel::getMoneyNames());
            $this->assign('webBlockIdNameArr', BlockModel::getBlockNames());
            return $this->fetch('website/security_info');
        }
    }

    /**
     * 登陆参数设置
     * @param Request $request
     * @param WebsiteLogic $websiteLogic
     * @return mixed|\think\response\Json
     */
    public function loginInfo(Request $request, WebsiteLogic $websiteLogic)
    {
        $websiteLogic->fileName = 'login_info';

        if ($request->isPost()) {

            $param = $request->param();

            $res = $websiteLogic->setConfig($param, '注册参数配置');
            if ($res) {
                AdminLog::addLog('修改安全参数配置', $request->param());
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            $assignData = [
                'config' => $websiteLogic->getData(),
                'weekName' => [
                    '0' => '星期日',
                    '1' => '星期一',
                    '2' => '星期二',
                    '3' => '星期三',
                    '4' => '星期四',
                    '5' => '星期五',
                    '6' => '星期六'
                ]
            ];
            return $this->fetch('website/login_info', $assignData);
        }
    }

    /**
     * 互助参数设置
     * @param Request $request
     * @param WebsiteLogic $websiteLogic
     * @return mixed|\think\response\Json
     */
    public function pdInfo(Request $request, WebsiteLogic $websiteLogic)
    {
        $websiteLogic->fileName = 'login_info';

        if ($request->isPost()) {

            $param = $request->param();

            $res = $websiteLogic->setConfig($param, '注册参数配置');
            if ($res) {
                AdminLog::addLog('修改安全参数配置', $request->param());
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            $this->assign('config', $websiteLogic->getData());
            $this->assign('blockNames', get_block_name());
            $this->assign('moneyNames', get_money_name());
            return $this->fetch('website/pd_info');
        }
    }

    /**
     * 广告信息管理
     * @param Request $request
     * @param WebsiteLogic $websiteLogic
     * @return mixed|\think\response\Json
     */
    public function adInfo(Request $request, WebsiteLogic $websiteLogic)
    {
        $websiteLogic->fileName = 'ad_info';

        if ($request->isPost()) {

            $param = $request->param();

            unset($param['file']);
            $res = $websiteLogic->setConfig($param, '广告信息管理');
            if ($res) {
                AdminLog::addLog('修改广告基本信息', $request->param());
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            $ads = [
                [
                    'name' => '会员中心',
                    'key' => 'user_index'
                ],
            ];
            $this->assign('config', $websiteLogic->getData());
            $this->assign('ads', $ads);

            return $this->fetch('website/ad_info');
        }
    }

    /**
     * 海报信息管理
     * @param Request $request
     * @param WebsiteLogic $websiteLogic
     * @return mixed|\think\response\Json
     */
    public function posterInfo(Request $request, WebsiteLogic $websiteLogic)
    {
        $websiteLogic->fileName = 'poster_info';

        if ($request->isPost()) {

            $param = $request->param();

            unset($param['file']);
            $res = $websiteLogic->setConfig($param, '海报信息管理');
            if ($res) {
                AdminLog::addLog('修改海报基本信息', $request->param());
                return json()->data(['code' => 1, 'msg' => '操作成功']);
            } else {
                return json()->data(['code' => -1, 'msg' => '操作失败']);
            }
        } else {
            $ads = [
                [
                    'name' => '海报中心',
                    'key' => 'poster_index'
                ],
            ];
            $this->assign('config', $websiteLogic->getData());
            $this->assign('ads', $ads);

            return $this->fetch('website/poster_info');
        }
    }

    /**
     * 显示银行卡列表
     * @param Request $request
     * @param Where $where
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function bankList(Request $request, Where $where)
    {
        if ($request->isAjax()) {

            try {
                $list = Bank::where($where)->select();

                $bankList = [];
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['id'],
                        'name' => $v['name_cn'],
                        'username' => $v['username'],
                        'address' => $v['address'],
                        'account' => $v['account'],
                        'logo' => get_img_domain() . $v['logo'],
                        'code' => get_img_domain() . $v['code'],
                        'status' => $v['status'] == 1 ? '启用' : '禁用',
                        'is_c' => $v['is_c'] == 1 ? '允许' : '禁止',
                        'is_t' => $v['is_t'] == 1 ? '允许' : '禁止'
                    ];

                    $bankList[] = $arr;
                }
                if (empty($bankList)) {
                    $data = [
                        'code' => -1,
                        'msg' => '没有收款方式'
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $bankList
                    ];
                }

                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询银行卡列表失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '没有获取到菜单目录'
                ];
                return json()->data($data);
            }
        } else {
            return view('website/bank_list');
        }
    }

    /**
     * 添加收款方式
     * @param Request $request
     * @param Bank $bankModel
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function addBank(Request $request, Bank $bankModel)
    {
        if ($request->isPost()) {
            try {
                $bankModel->addBank();
                AdminLog::addLog('添加收款方式', $request->param());
                return json()->data(['code' => 1, 'msg' => '添加成功']);
            } catch (\Exception $e) {
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            return view('website/add_bank_info');
        }
    }

    /**
     * 编辑收款方式
     * @param Request $request
     * @param Bank $bankModel
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editBank(Request $request, Bank $bankModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $bankModel->getBankFieldById($id);
        if (empty($info)) {
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info->editBank();
                AdminLog::addLog('修改收款方式', $request->param());
                return json()->data(['code' => 1, 'msg' => '修改成功']);
            } catch (\Exception $e) {
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
        } else {
            $assignData = [
                'info' => $info
            ];
            return view('website/edit_bank_info', $assignData);
        }
    }

    /**
     * 删除收款方式
     * @param Request $request
     * @param Bank $bankModel
     * @return \think\Response|\think\response\Json
     */
    public function delBank(Request $request, Bank $bankModel)
    {
        if ($request->isPost()) {
            try {
                $id = $request->param('id', '', 'trim');
                $bankList = $bankModel->whereIn('id', $id)->select()->toArray();
                if (empty($bankList)) {
                    return json()->data(['code' => -1, 'msg' => '没有数据']);
                }
                $count = UserBank::whereIn('opening_id', $id)->count();
                if ($count > 0) {
                    return json()->data(['code' => -1, 'msg' => '此收款方式有会员在使用，不能删除']);
                }
                $bankModel->whereIn('id', $id)->delete();
                $bankModel->_afterDelete();

                $data = [
                    'bank_list' => $bankList
                ];
                AdminLog::addLog('删除收款方式', $data);
                return json()->data(['code' => 1, 'msg' => '删除成功']);
            } catch (\Exception $e) {
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
        }
    }

    /**
     * 全球国家地址
     * @param Request $request
     * @param Where $where
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function regionList(Request $request, Where $where)
    {
        $levelData = [1 => '省', 2 => '市', 3 => '县', 4 => '乡'];
        $statusData = [1 => '启用', 2 => '禁用'];
        if ($request->isAjax()) {
            try {
                $where['parent_id'] = $request->param('pid', 0, 'intval');

                if ($nameCn = $request->param('name_cn', '', 'trim')) {
                    $where['name_cn'] = ['like', '%' . $nameCn . '%'];
                }
                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');
                $list = Region::where($where)
                                ->order('id', 'desc')
                                ->limit($page * $pageSize, $pageSize)->select();

                $List = [];
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['id'],
                        'name_cn' => $v['name_cn'],
                        'name_en' => $v['name_en'],
                        'level' => isset($levelData[$v['level']]) ? $levelData[$v['level']] : '',
                        'parent_id' => $v['parent_id'],
                        'status' => isset($statusData[$v['status']]) ? $statusData[$v['status']] : '',
                    ];
                    $List[] = $arr;
                }
                if (count($List) == 0) {
                    $data = [
                        'code' => -1,
                        'msg' => '没有记录'
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $List,
                        'count' => Region::where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询地址列表失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => '查询记录失败',
                ];
                return json()->data($data);
            }
        } else {
            $this->assign('levelData', $levelData);
            return view('website/region_list');
        }
    }

    /**
     * 添加
     * @param Request $request
     * @param Region $regionModel
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function addRegion(Request $request, Region $regionModel)
    {
        $levelData = [1 => '省', 2 => '市', 3 => '县', 4 => '乡'];
        if ($request->isPost()) {
            try {

                if ($request->param('status') <= 0) {
                    return json(['code' => -1, 'msg' => '请选择状态']);
                }

                $data = $request->param();
                // 写入操作
                $regionModel->addRegion($data);
                AdminLog::addLog('添加地址信息', $data);
            } catch (\Exception $e) {
                Log::write('添加地址信息失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
            return json()->data(['code' => 1, 'msg' => '添加成功']);
        } else {
            $this->assign('levelData', $levelData);
            return view('website/add_region');
        }
    }

    /**
     * 编辑
     * @param Request $request
     * @param Region $regionModel
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editRegion(Request $request, Region $regionModel)
    {
        $levelData = [1 => '省', 2 => '市', 3 => '县', 4 => '乡'];
        $id = $request->param('id', '', 'intval');
        $info = $regionModel->where('id', $id)->find();
        if (empty($info)) {
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info->allowField([
                    'name_cn', 'level', 'parent_id', 'status'
                ])->save($request->param());
                AdminLog::addLog('修改地址信息', $request->param());
            } catch (\Exception $e) {
                Log::write('修改地址信息失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '修改失败']);
            }
            return json()->data(['code' => 1, 'msg' => '修改成功']);
        } else {
            $this->assign('levelData', $levelData);
            return view('website/edit_region', ['info' => $info]);
        }
    }

    /**
     * 删除
     * @param Request $request
     * @param Region $regionModel
     * @return \think\Response|\think\response\Json
     * @throws \Exception
     */
    public function delRegion(Request $request, Region $regionModel)
    {
        if ($request->isAjax()) {
            $id = $request->param('id', '', 'intval');
            $region = $regionModel->where('id', $id)->find()->toArray();
            if ($id <= 0) {
                return json()->data(['code' => -1, 'msg' => '网络错误，请刷新后重试']);
            }

            $res = $regionModel->where('id', $id)->delete();

            if ($res) {
                AdminLog::addLog('删除地址信息', $region);
                return json()->data(['code' => 1, 'msg' => '删除成功']);
            } else {
                return json()->data(['code' => -1, 'msg' => '删除失败']);
            }
        }
    }

    /**
     * 创建前台地址文件
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function generateCity()
    {
        $res = create_data_city();
        file_put_contents('template/wap/default/Static/js/data.city.js', 'var cityData3 = ' . json_encode($res, JSON_UNESCAPED_UNICODE));
        return json()->data(['code' => 1, 'msg' => '删除成功']);
    }

    /**
     * 全球国家地址
     * @param Request $request
     * @param Where $where
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function countryList(Request $request, Where $where)
    {
        if ($request->isAjax()) {
            try {
                if ($code = $request->param('code', '', 'trim')) {
                    $where['code'] = $code;
                }
                if ($nameCn = $request->param('name_cn', '', 'trim')) {
                    $where['name_cn'] = ['like', '%' . $nameCn . '%'];
                }
                if ($areaCode = $request->param('area_code', '', 'trim')) {
                    $where['area_code'] = $areaCode;
                }
                $page = $request->get('p', '1', 'intval') - 1;
                $pageSize = $request->get('p_num', '10', 'intval');
                $list = Country::where($where)
                                ->order('id', 'desc')
                                ->limit($page * $pageSize, $pageSize)->select();

                $List = [];
                foreach ($list as $v) {
                    $arr = [
                        'id' => $v['id'],
                        'code' => $v['code'],
                        'name_cn' => $v['name_cn'],
                        'name_en' => $v['name_en'],
                        'area_code' => $v['area_code'],
                    ];
                    $List[] = $arr;
                }
                if (count($List) == 0) {
                    $data = [
                        'code' => -1,
                        'msg' => '没有记录'
                    ];
                } else {
                    $data = [
                        'code' => 1,
                        'data' => $List,
                        'count' => Country::where($where)->count()
                    ];
                }
                return json()->data($data);
            } catch (\Exception $e) {
                Log::write('查询国家列表失败: ' . $e->getMessage(), 'error');
                $data = [
                    'code' => -1,
                    'msg' => $e->getMessage(),
                ];
                return json()->data($data);
            }
        } else {
            return view('website/country_list');
        }
    }

    /**
     * 添加
     * @param Request $request
     * @param Country $countryModel
     * @return \think\Response|\think\response\Json|\think\response\View
     */
    public function addCountry(Request $request, Country $countryModel)
    {
        if ($request->isPost()) {
            try {
                $data = $request->param();
                // 写入操作
                $countryModel->addCountry($data);
                AdminLog::addLog('添加国家信息', $data);
            } catch (\Exception $e) {
                Log::write('添加配置失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => $e->getMessage()]);
            }
            return json()->data(['code' => 1, 'msg' => '添加成功']);
        } else {
            return view('website/add_country');
        }
    }

    /**
     * 编辑
     * @param Request $request
     * @param Country $countryModel
     * @return \think\Response|\think\response\Json|\think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function editCountry(Request $request, Country $countryModel)
    {
        $id = $request->param('id', '', 'intval');
        $info = $countryModel->where('id', $id)->find();
        if (empty($info)) {
            $this->error('未获取到信息');
        }
        if ($request->isPost()) {
            try {
                $info->allowField([
                    'code', 'name_cn', 'name_en', 'area_code'
                ])->save($request->param());
                AdminLog::addLog('修改国家信息', $request->param());
            } catch (\Exception $e) {
                Log::write('修改全球国家配置失败: ' . $e->getMessage(), 'error');
                return json()->data(['code' => -1, 'msg' => '修改失败']);
            }
            return json()->data(['code' => 1, 'msg' => '修改成功']);
        } else {
            return view('website/edit_country', ['info' => $info]);
        }
    }

    /**
     * 删除
     * @param Request $request
     * @param Country $countryModel
     * @return \think\Response|\think\response\Json
     * @throws \Exception
     */
    public function delCountry(Request $request, Country $countryModel)
    {
        if ($request->isAjax()) {
            $id = $request->param('id', '', 'intval');
            $country = $countryModel->where('id', $id)->find()->toArray();
            if ($id <= 0) {
                return json()->data(['code' => -1, 'msg' => '网络错误，请刷新后重试']);
            }

            $res = $countryModel->where('id', $id)->delete();

            if ($res) {
                AdminLog::addLog('删除国家信息', $country);
                return json()->data(['code' => 1, 'msg' => '删除成功']);
            } else {
                return json()->data(['code' => -1, 'msg' => '删除失败']);
            }
        }
    }

}
