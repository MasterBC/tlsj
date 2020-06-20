<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use app\common\model\app\AppVersion;
use app\api\response\ReturnCode;
use think\Request;

class Config extends Base
{

    /**
     * 检测是否有版本更新
     * @param Request $request
     * @return \think\Response|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkUpdate(Request $request)
    {
        $version = $request->param('version');
        $system = $request->param('system');
        if ($version == '' || $system == '') {
            return ReturnCode::showReturnCode(1005);
        }

        $lastVersion = AppVersion::getLastVersion($system);
        if ($lastVersion) {
            $newVersion = $lastVersion['version'];
            if ($version != $newVersion) {
                $version = explode('.', $version);
                $newVersion = explode('.', $newVersion);

                if ($version[0] < $newVersion[0]) { // 大版本更新
                    return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                        'update' => true,
                        'wgtUrl' => '',
                        'pkgUrl' => $lastVersion['update_url']
                    ]);
                } else { // 小版本更新
                    return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
                        'update' => true,
                        'wgtUrl' => $lastVersion['update_url'],
                        'pkgUrl' => ''
                    ]);
                }
            }
        }

        return ReturnCode::showReturnCode(ReturnCode::SUCCESS_CODE, '', [
            'update' => false,
            'wgtUrl' => '',
            'pkgUrl' => ''
        ]);
    }
}