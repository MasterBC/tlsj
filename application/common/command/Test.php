<?php

namespace app\common\command;

use app\common\model\auth\AuthGroup;
use app\common\model\money\Money;
use app\common\server\kuaidi100\Server;
use app\common\server\Upload;
use org\AesSecurity;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\model\Users;
use app\common\model\WebUserMessage;
use app\common\model\Block;
use app\common\server\bonus\Server as AppServer;
use think\Db;
use Curl\Curl;
use org\DbManage;
use think\facade\Env;
use think\facade\Request;

class Test extends Command
{
    protected function configure()
    {
        $this->setName('test')
            ->setDescription('Used for test scripts');
    }

    private function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float) sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    protected function execute(Input $input, Output $output)
    {
        $userInfo = Users::getByUserId(2);
        $server = new AppServer();
        $server->recommendedAward(1000, $userInfo);
        $server->clear();
        die;
        $data = (new Money())->moneyTransformation(100000000000);
        dump($data);
        exit;
        $arr = [
            'uid' => 1,
        ];
        $str = json_encode($arr, JSON_UNESCAPED_UNICODE);
        $key = 'bEIUfx2ZOKwkhxZV';

        $str = AesSecurity::encrypt($str, $key);
        dump($str);
        $str = 'lN/UGlLdTShXcJjOb2ihTapf/sjy8duhuUC7t6XqIvuUI5EkLywmSIN4qfAACUU9';
        $str = AesSecurity::decrypt($str, $key);
        dump($str);
        die;
        $res = (new \api\bxm\Server())->getBuoyAd(1);
        dump($res);
        die;
        $curl = new Curl();
        $curl->setHeaders([
            'Content-Type' => 'application/json;charset=utf-8'
        ]);
        $data = [
            'request_id' => md5(time() . rand(111111, 999999)),
            'position' => '70b12e47b4d84b1b87b088bb0f741add-4',
            'app' => [
                'name' => 'test'
            ],
            'device' => [
                'ip' => '183.53.106.124',
                'os' => 2,
                'imei' => md5(time())
            ],
            'ua' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.78 Safari/537.36 Vivaldi/2.8.1664.4'
        ];
        $res = $curl->post('http://adsapi.fawulu.com/ticket/getInspireVideo', $data);
        dump($data);
        dump($res);
        dump($curl->getRequestHeaders());
        die;
        $dir = Env::get('ROOT_PATH') . 'public/2019a024/uploads';
        $ignoreDir = Env::get('ROOT_PATH') . 'public/2019a024/';
        $upload = new Upload();
        $upload->uploadDirToOss($dir, $ignoreDir);
        die;
        $userInfo = Users::getByUserId(1);
        $algebra = 2;
        $users = Users::getAssignAlgebraTeamUserId($userInfo, $algebra);
        dump($users);
        die;
        $dbManage = new DbManage();
        $dbManage->back();
        //        die;
        /**
         * 4 = 0000 0000 0000 0000 0000 0000 0000 0100
         * 转补码: 0000 0000 0000 0000 0000 0000 0000 0100
         * 5 = 0000 0000 0000 0000 0000 0000 0000 0101
         * 转补码: 0000 0000 0000 0000 0000 0000 0000 0101
         * 0000 0000 0000 0000 0000 0000 0000 0100
         * 0000 0000 0000 0000 0000 0000 0000 0101
         * 0000 0000 0000 0000 0000 0000 0000 0101
         * 0100
         * 1*2^0 + 0*2^1 + 1*2^2 + 0*2^3 = 5;
         */
        // $a = 4|5;
        // dump($a);
        // $a = 4&5;
        // dump($a);
        // die;
        AuthGroup::generateSuperAdminAuth();
        exit;

        die;
        $arr = [];
        for ($i = 0; $i < 10000; $i++) {
            $arr[] = rand(1, 999999);
        }
        echo "开始sort排序\n";
        $startTime = microtime(true);
        sort($arr);
        dump($arr);
        echo "耗时:" . round(microtime(true) - $startTime, 2) . "秒";

        die;

        echo "开始冒泡排序\n";
        $startTime = microtime(true);

        $count = count($arr);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $count - 1; $j > $i; $j--) {
                if ($arr[$j] < $arr[$j - 1]) {
                    $temp = $arr[$j];
                    $arr[$j] = $arr[$j - 1];
                    $arr[$j - 1] = $temp;
                }
            }
        }
        echo "耗时:" . round(microtime(true) - $startTime, 2) . "秒";

        die;
        $file_path = __DIR__ . "/visit_data.sql";
        $sqlStr = '';
        if (file_exists($file_path)) {
            $fp = fopen($file_path, "r");
            $i = 1;
            while (!feof($fp)) { //循环读取，每次读取一行直至读取完整个文件
                $str = iconv('GBK', 'UTF-8', fgets($fp, 4096));
                $arr = explode("\t", $str);
                if ($i == 1) {
                    $sqlStr = 'INSERT INTO `daoru_test` (';
                    foreach ($arr as $v) {
                        $sqlStr .= '`' . rtrim($v, "\n") . '`,';
                    }
                    $sqlStr = rtrim($sqlStr, ',');
                    $sqlStr .= ') ';
                }
                if ($i != 1) {
                    $daoruSql = $sqlStr . ' value(';
                    foreach ($arr as $v) {
                        $daoruSql .= "'" . rtrim($v, "\n") . "',";
                    }
                    $daoruSql = rtrim($daoruSql, ',');
                    $daoruSql .= ");\n";
                    file_put_contents(__DIR__ . '/test_daochu.sql', $daoruSql, FILE_APPEND);
                }
                //                $str = 'INSERT INTO `test` (';
                echo '正在导出' . $i . "条\n";
                $i++;
            }
        }

        die;
        $curl = new Curl();
        //        $str = $curl->get('http://2019demo.jiafuw.test/zfuwl_auth_rule.txt');
        $str = file_get_contents(__DIR__ . '/test.txt');
        $arr = explode("\n", $str);
        foreach ($arr as $v) {
            $data = explode("\t", $v);
            foreach ($data as $key => $val) {
                $data[$key] = trim($val, '');
            }
            file_put_contents(__DIR__ . '/test.log', print_r($data, true), FILE_APPEND);
        }

        die;

        $upload = new Upload();
        $res = $upload->uploadImageBase64($base64Str);
        dump($res);

        die;

        $server = new Server();
        $server->pollRequest([
            'shipping_name' => '韵达快递',
            'shopping_code' => '3714450223694'
        ]);


        die;
        \app\common\model\grade\Leader::getLeaderInfoById(1);
        $startTime = microtime(true);
        $arr = [];
        for ($i = 0; $i < 1000; $i++) {
            $id = rand(1, 3);
            $info = \app\common\model\grade\Leader::getLeaderInfoById($id);
            $arr[] = $info;
        }
        echo '耗时： ' . (round(microtime(true) - $startTime, 3)) . ' S';
        echo "\n";
        file_put_contents(__DIR__ . '/test.log', print_r($arr, true));
        $arr = [];
        $startTime = microtime(true);
        for ($i = 0; $i < 1000; $i++) {
            $id = rand(1, 3);
            (new \app\common\model\grade\Leader())->testGetInfo($id);
            $arr[] = $info;
        }
        echo '耗时： ' . (round(microtime(true) - $startTime, 3)) . ' S';
        file_put_contents(__DIR__ . '/test.log', "\n" . print_r($arr, true), FILE_APPEND);
        echo "\n";

        $output->writeln('success');
    }
}
