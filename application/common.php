<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * 随机小数
 *
 * @param  int $min 最小值
 * @param  int $max 最大值
 * @return float
 * @author gkdos
 * 2019-09-24T10:15:47+0800
 */
function rand_float($min=0, $max=1)
{
    return $min + mt_rand()/mt_getrandmax() * ($max - $min);
}

/**
 * 获取设备类型
 *
 * @return string
 * @author gkdos
 * 2019-09-20T15:20:43+0800
 */
function get_device_type()
{
    //全部变成小写字母
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    $type = 'other';
    //分别进行判断
    if(strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
        $type = 'ios';
    } 
      
    if(strpos($agent, 'android')) {
        $type = 'android';
    }
    return $type;
}

/**
 * 根据某个字符把字符切成数组
 *
 * @param string $str 源字符串
 * @param string $splitStr 切割条件
 * @return array|array[]|false|string[]
 */
function str_split_by_str_to_array($str, $splitStr = '')
{
    if ($str == '') {
        return [];
    }
    if ($splitStr == '') {
        $amounts = preg_split("//", $str, -1, PREG_SPLIT_NO_EMPTY);
    } else {
        $amounts = preg_split("/[" . $splitStr . "]+/", $str);
    }

    return $amounts;
}

/**
 * 字符编码转成utf-8
 *
 * @param string $data 要转码的字符
 * @return string
 */
function charsetConvertToUTF8($data)
{
    if (!empty($data)) {
        $fileType = mb_detect_encoding($data, array('UTF-8', 'GBK', 'LATIN1', 'BIG5'));
        if ($fileType != 'UTF-8') {
            $data = mb_convert_encoding($data, 'utf-8', $fileType);
        }
    }
    return $data;
}

/*
 * 正负数相互转换（支持小数）
 *
 * @param float $number 要转的数
 * @return float
 */
function plus_minus_conversion($number = 0)
{
    return $number > 0 ? -1 * $number : abs($number);
}

/**
 * 求余
 *
 * @param int/float $a 除数
 * @param int/float $b 被除数
 * @return float|int
 */
function kmod($a, $b)
{
    $c = $a / $b;

    return $c - (int)$c;
}

/**
 * 获取ip
 *
 * @return array|false|string
 */
function get_ip()
{
    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    else if (isset($_SERVER["HTTP_CLIENT_IP"]))
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    else if (isset($_SERVER["REMOTE_ADDR"]))
        $ip = $_SERVER["REMOTE_ADDR"];
    else if (@getenv("HTTP_X_FORWARDED_FOR"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (@getenv("HTTP_CLIENT_IP"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (@getenv("REMOTE_ADDR"))
        $ip = getenv("REMOTE_ADDR");
    else
        $ip = "Unknown";
    return $ip;
}

/**
 * 递归删除目录和目录下的文件
 *
 * @param string $dir 目录路径
 * @return bool
 */
function del_dir($dir)
{
    if (!is_dir($dir)) {
        return false;
    }
    $handle = opendir($dir);
    while (($file = readdir($handle)) !== false) {
        if ($file != "." && $file != "..") {
            is_dir("$dir/$file") ? del_dir("$dir/$file") : @unlink("$dir/$file");
        }
    }
    if (readdir($handle) == false) {
        closedir($handle);
        @rmdir($dir);
    }
    return true;
}

/**
 * 循环获取目录结构
 *
 * @param $arr_file
 * @param $directory
 * @param string $dir_name
 */
function tree(&$arr_file, $directory, $dir_name = '')
{
    $mydir = dir($directory);
    while ($file = $mydir->read()) {
        if ((is_dir("$directory/$file")) AND ($file != ".") AND ($file != "..")) {
            tree($arr_file, "$directory/$file", "$dir_name/$file");
        } else if (($file != ".") AND ($file != "..")) {
            $arr_file[] = "$dir_name/$file";
        }
    }
    $mydir->close();
}


/**
 * 获取用户设备信息
 *
 * @return string
 */
function equipment_system()
{
    $browser = new \Browser();
    $platform = $browser->getPlatform();
    $browserName = $browser->getBrowser();
    $version = $browser->getVersion();
    if ($platform == $browserName) {
        return $platform . $version;
    } else {
        return $platform . ' ' . $browserName . $version;
    }
    $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if (stristr($agent, 'iPad')) {
        $fb_fs = "iPad";
    } else if (preg_match('/Android (([0-9_.]{1,3})+)/i', $agent, $version)) {
        $fb_fs = "手机(Android " . $version[1] . ")";
    } else if (stristr($agent, 'Linux')) {
        $fb_fs = "电脑(Linux)";
    } else if (preg_match('/iPhone OS (([0-9_.]{1,3})+)/i', $agent, $version)) {
        $fb_fs = "手机(iPhone " . $version[1] . ")";
    } else if (preg_match('/Mac OS X (([0-9_.]{1,5})+)/i', $agent, $version)) {
        $fb_fs = "电脑(OS X " . $version[1] . ")";
    } else if (preg_match('/unix/i', $agent)) {
        $fb_fs = "Unix";
    } else if (preg_match('/windows/i', $agent)) {
        $fb_fs = "电脑(Windows)";
    } else {
        $fb_fs = "Unknown";
    }
    return $fb_fs;
}

/**
 * 生成随机字符串
 *
 * @param int $endNum
 * @return string
 */
function get_rand_str($endNum = 0)
{
    $endNum = ($endNum <= 0 ? rand(4, 10) : $endNum);
    $str = md5(microtime() . rand(1111, 9999));
    $length = strlen($str) - 1;
    $substr = substr($str, rand(0, $length), rand(0, $length));
    $str .= $substr;
    $endStr = md5($str) . ($str . md5($str)) . md5(microtime() . rand(1111, 9999));
    $str = '';

    for ($i = 0; $i < $endNum; $i++) {
        $str .= $endStr[mt_rand(0, strlen($endStr) - 1)];
    }
    $str = strtolower($str);

    return $str;
}

/**
 * 获取数组中的某一列
 *
 * @param array $arr 数组
 * @param string $keyName 列名
 * @return array 返回那一列的数组
 */
function get_arr_column($arr, $keyName)
{
    $returnArr = array();
    if (!empty($arr)) {
        foreach ($arr as $k => $v) {
            $returnArr[] = $v[$keyName];
        }
    }
    return $returnArr;
}

/**
 * 获取所有数组的差集
 *
 * @param mixed ...$arrays
 * @return array|mixed
 */
function get_array_diff(...$arrays)
{
    $array = [];
    if (count($arrays) == 1) {
        return $arrays[0];
    }
    foreach ($arrays as $k => $v) {
        $diffArray = [];
        if (isset($arrays[$k + 1])) {
            foreach ($arrays[$k] as $k2 => $v2) {
                if (!in_array($v2, $arrays[$k + 1])) {
                    $diffArray[] = $v2;
                }
            }
            foreach ($arrays[$k + 1] as $k3 => $v3) {
                if (!in_array($v3, $arrays[$k])) {
                    $diffArray[] = $v3;
                }
            }
        } else {
            if (!empty($diffArray)) {
                foreach ($arrays[$k] as $k2 => $v2) {
                    if (!in_array($v2, $diffArray)) {
                        $diffArray[] = $v2;
                    }
                }
            } else {
                $diffArray = $v;
            }
        }
        if (!empty($array)) {
            if (!empty($diffArray)) {
                $array = array_merge($array, $diffArray);
            }
        } else {
            $array = $diffArray;
        }
    }
    $array = array_unique($array);
    return $array;
}

/**
 * 两个数组的笛卡尔积
 *
 * @param array $arr1
 * @param array $arr2
 * @return array
 */
function combine_array($arr1, $arr2)
{
    $result = array();
    foreach ($arr1 as $item1) {
        foreach ($arr2 as $item2) {
            $temp = $item1;
            $temp[] = $item2;
            $result[] = $temp;
        }
    }
    return $result;
}


/**
 * 将数组中的某个键值作为键
 *
 * @param array $arr 数组
 * @param string $keyName 键名
 * @return array
 */
function convert_arr_key($arr, $keyName)
{
    $arr2 = array();
    foreach ($arr as $key => $val) {
        $arr2[$val[$keyName]] = $val;
    }
    return $arr2;
}

/**
 * 获取session_id
 *
 * @return string
 */
function sid()
{
    if (PHP_SESSION_ACTIVE != session_status()) {
        session_start();
    }
    return session_id();
}

/**
 * Url生成
 *
 * @param string $url 路由地址
 * @param string|array $vars 变量
 * @param bool|string $suffix 生成的URL后缀
 * @param bool|string $domain 域名
 * @return string
 */
function U($url = '', $vars = '', $suffix = true, $domain = true)
{
    return url($url, $vars, $suffix, $domain);
}

/**
 * 发送短信
 *
 * @param string|array $mobile 手机号
 * @param string $text 短信内容
 * @param string $code 验证码
 * @return array
 */
function send_sms($mobile, $text, $code = '', $type = 1)
{
    $sms = new \org\Sms();
    $num = 0;
    $err = '';
    $mobile = explode(',', $mobile);
    $smsLogModel = new \app\common\model\SmsLog();
    foreach ($mobile as $v) {
        if (check_mobile($v)) {
            $sms->setMobile($v); // 设置发送手机号
            $sms->setContent($text); // 设置发送内容
            $res = $sms->sendSms();
            $smsModel = $smsLogModel->create(['name' => $v, 'add_time' => time(), 'content' => $text, 'status' => 2, 'session_id' => sid(), 'send_type' => $type]);
            $code && $smsModel->code = $code;
            if ($res) {
                $smsModel->status = 1;
                $num++;
            } else {
                $smsModel->errcode = $sms->showErr();
                $err .= $v . $sms->showErr();
            }
            $smsModel->save();
        }
    }
    if ($num > 0) {
        return ['status' => 1, 'msg' => '发送成功!'];
    } else {
        return ['status' => -1, 'msg' => $err];
    }
}

/**
 * 验证手机短信
 *
 * @param int $mobile 手机号
 * @param int $code 验证码
 * @param string $session_id session ID
 * @return array
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function validate_sms_code_verify($mobile, $code, $session_id)
{
    if ($code == '') {
        return ['code' => -1, 'msg' => '请输入手机验证码'];
    }
    $session_id = $session_id ? $session_id : session_id();
    //判断是否存在验证码
    $smsLogLogic = new app\common\model\SmsLog;
    $where = [];
    $where['name'] = $mobile;
    $where['session_id'] = $session_id;
    $where['code'] = $code;
    $where['is_verify'] = 2;
    $smsLogData = $smsLogLogic->getSmsLogData($where, 1, 'id DESC');
    if (empty($smsLogData)) {
        return ['code' => -1, 'msg' => '手机验证码不匹配'];
    }

    //获取时间配置
    $sms_time_out = zf_cache('sms_info.sms_time_out') ? zf_cache('sms_info.sms_time_out') : 120;

    //验证是否过时
    if ((time() - $smsLogData['add_time']) > $sms_time_out) {
        return ['code' => -1, 'msg' => '手机验证码超时']; //超时处理
    }

    $smsLogLogic->updateSmsLogData($where, ['is_verify' => 1]);
    return ['code' => 1, 'msg' => '验证成功'];
}

/**
 * 获取短信数量
 *
 * @return int
 */
function get_sms_num()
{
    $sms = new \org\Sms();
    $res = $sms->getSmsNum();

    return $res;
}

/**
 * 发送邮件
 *
 * @param array|string $to 对方邮箱号
 * @param array|string $name 对方姓名
 * @param string $subject 主题
 * @param string $body 内容
 * @param int $code 验证码
 * @param array $attachment 附件
 * @param bool $debug 是否开启调试模式
 * @return bool|string
 * @throws \PHPMailer\PHPMailer\Exception
 */
function send_mail($to, $name, $subject = '', $body = '', $code = 0, $attachment = [], $debug = false)
{

    $mailLogModel = new \app\common\model\MailLog();
    $mailModel = $mailLogModel->create([
        'name' => (is_array($name) ? implode(',', $name) : $name),
        'email' => (is_array($to) ? implode(',', $to) : $to),
        'add_time' => time(),
        'subject' => $subject,
        'content' => $body,
        'code' => intval($code),
        'status' => 2,
        'session_id' => sid()
    ]);


    $config = zf_cache('smtp_info.');
    $mail = new PHPMailer\PHPMailer\PHPMailer(); //PHPMailer对象
    $mail->CharSet = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();  // 设定使用SMTP服务
    $mail->SMTPDebug = $debug;                     // 关闭SMTP调试功能
    // 1 = errors and messages
    // 2 = messages only
    $mail->SMTPAuth = true;                  // 启用 SMTP 验证功能
//    $mail->SMTPSecure = 'ssl';                 // 使用安全协议
    $mail->Host = $config['smtp_server'];  // SMTP 服务器
    $mail->Port = $config['smtp_port'];  // SMTP服务器的端口号
    $mail->Username = $config['send_email'];  // SMTP服务器用户名
    $mail->Password = $config['send_pass'];  // SMTP服务器密码
    $mail->SetFrom($config['send_email'], $config['send_nickname']); // 发件人邮箱 和姓名设置
    $replyEmail = $config['send_email']; // 接受回复的邮箱
    $replyName = $config['send_nickname']; // 接受回复的姓名
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject = $subject;
    $mail->MsgHTML($body);
    if (is_array($to)) {
        foreach ($to as $k => $v) {
            $mail->AddAddress($v, $name[$k]);
        }
    } else {
        $mail->AddAddress($to, $name);
    }
    if (!empty($attachment)) { // 添加附件
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    try {
        $mailModel->status = 1;
        $mailModel->save();
        $mail->Send();
    } catch (\Exception $e) {
        $mailModel->errcode = $mail->ErrorInfo . $e->getMessage();
        $mailModel->save();
        think\facade\Log::write('邮件发送失败: ' . $mail->ErrorInfo . $e->getMessage(), 'error');
        return ($debug ? $mail->ErrorInfo : '邮件发送失败');
    }
    return true;
}


/**
 * 验证邮箱验证码
 *
 * @param string $email 邮箱号
 * @param string $code 验证码
 * @param string $session_id ssession_id
 * @return array
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function validate_smtp_email_code_verify($email, $code, $session_id)
{
    $session_id = $session_id ? $session_id : session_id();
    //判断是否存在验证码
    $smtpLogLogic = new app\common\model\MailLog;
    $where = [];
    $where['email'] = $email;
    $where['session_id'] = $session_id;
    $where['code'] = $code;
    $where['is_verify'] = 2;
    $smtpLogData = $smtpLogLogic->getEmailLogData($where, 1, 'id DESC');
    if (!$smtpLogData) {
        return ['code' => -1, 'msg' => '邮箱验证码不匹配'];
    }

    //获取时间配置
    $smtp_time_out = zf_cache('smtp_info.email_time_out') ? zf_cache('smtp_info.email_time_out') : 120;


    //验证是否过时
    if ((time() - $smtpLogData['add_time']) > $smtp_time_out) {
        return ['code' => -1, 'msg' => '邮箱验证码超时']; //超时处理
    }

    $smtpLogLogic->updateEmailLogData($where, ['is_verify' => 1]);
    return ['code' => 1, 'msg' => '验证成功'];
}

/**
 * 获取缓存
 *
 * @param string $key 键值
 * @return mixed
 */
function zf_cache($key = '')
{
    $logic = new \app\common\logic\WebsiteLogic();

    return $logic->getData($key);
}

/**
 * 过滤参数
 *
 * @param $str
 * @return null|string|string[]
 */
function filter_words(&$str)
{
    $farr = array(
        "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
        "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
        "/select\b|insert\b|update\b|delete\b|drop\b|;|\"|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is"
    );
    $str = preg_replace($farr, '', $str);
    //$str = strip_tags($str);
    return $str;
}

/**
 * 参数过滤
 *
 * @param $params
 * @return mixed
 */
function filter_params(&$params)
{
    if (is_array($params)) {
        foreach ($params as $k => &$v) {
            if (is_array($v)) {
                filter_params($v);
            } else {
                filter_words($v);

            }
        }
    } else {
        $arr[] = filter_words($params);
    }
    return $params;
}

/**
 * 验证密码格式
 *
 * @param string $pass 密码
 * @return bool
 */
function check_pass($pass)
{
    $min = ((int)zf_cache('security_info.web_pass_min') <= 0 ? 6 : (int)zf_cache('security_info.web_pass_min'));
    $max = ((int)zf_cache('security_info.web_pass_max') <= 0 ? 16 : (int)zf_cache('security_info.web_pass_max'));
    if (preg_match('/[0-9a-zA-Z\!\@\#\^\_]{' . $min . ',' . $max . '}$/', $pass))
        return true;
    return false;
}

/**
 * 检查手机号码格式
 *
 * @param string $mobile 手机号
 * @return bool
 */
function check_mobile($mobile)
{
    if (preg_match('/1[123456789]\d{9}$/', $mobile))
        return true;
    return false;
}

/**
 * 纯数字
 *
 * @param string $number 数字
 * @return bool
 */
function check_number($number)
{
    if (preg_match('/^[0-9]*$/', $number))
        return true;
    return false;
}

/**
 * 检查邮箱地址格式
 *
 * @param string $email 邮箱号
 * @return bool
 */
function check_mail($email)
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL))
        return true;
    return false;
}

/**
 * 获取图片显示地址
 *
 * @param $imgSrc
 * @return string
 */
function get_img_show_url($imgSrc)
{
    return get_img_domain() . $imgSrc;
}


/**
 * 获取图片访问域名
 *
 * @return string
 */
function get_img_domain()
{
    return zf_cache('oss.oss_upload') === true ? zf_cache('oss.oss_url') : request()->scheme() . '://' . request()->host() . '/';

}

use think\helper\Time;

/**
 * 友好时间提示
 *
 * @param int $time 时间戳
 * @return string
 */
function friend_date($time)
{
    if (!$time)
        return '';

    $today = Time::today();
    $yesterday = Time::yesterday();
    $year = Time::year();;

    if ($time > $today[0] && $time < $today[1]) {
        return '今天 ' . date('H:i:s', $time);
    } elseif ($time > $yesterday[0] && $time < $yesterday[1]) {
        return '昨天 ' . date('H:i:s', $time);
    } elseif ($time > $year[0] && $time < $year[1]) {
        return date('m-d H:i:s', $time);
    } else {
        return date('Y-m-d H:i:s', $time);
    }
}

/**
 * 隐藏手机号用*代替
 *
 * @param $str
 * @return string
 */
function hideMobile($str)
{
    return hideStr($str, 3, 4);
}

/**
 * +----------------------------------------------------------
 * 将一个字符串部分字符用*替代隐藏
 * http://www.thinkphp.cn/code/94.html
 * +----------------------------------------------------------
 * @param string $string 待转换的字符串
 * @param int $bengin 起始位置，从0开始计数，当$type=4时，表示左侧保留长度
 * @param int $len 需要转换成*的字符个数，当$type=4时，表示右侧保留长度
 * @param int $type 转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
 * @param string $glue 分割符
 * +----------------------------------------------------------
 * @return string   处理后的字符串
 * +----------------------------------------------------------
 */
function hideStr($string, $bengin = 0, $len = 4, $type = 0, $glue = "@")
{
    if (empty($string))
        return false;
    $array = array();
    if ($type == 0 || $type == 1 || $type == 4) {
        $strlen = $length = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string, 0, 1, "utf8");
            $string = mb_substr($string, 1, $strlen, "utf8");
            $strlen = mb_strlen($string);
        }
    }
    if ($type == 0) {
        for ($i = $bengin; $i < ($bengin + $len); $i++) {
            if (isset($array[$i]))
                $array[$i] = "*";
        }
        $string = implode("", $array);
    } else if ($type == 1) {
        $array = array_reverse($array);
        for ($i = $bengin; $i < ($bengin + $len); $i++) {
            if (isset($array[$i]))
                $array[$i] = "*";
        }
        $string = implode("", array_reverse($array));
    } else if ($type == 2) {
        $array = explode($glue, $string);
        $array[0] = hideStr($array[0], $bengin, $len, 1);
        $string = implode($glue, $array);
    } else if ($type == 3) {
        $array = explode($glue, $string);
        $array[1] = hideStr($array[1], $bengin, $len, 0);
        $string = implode($glue, $array);
    } else if ($type == 4) {
        $left = $bengin;
        $right = $len;
        $tem = array();
        for ($i = 0; $i < ($length - $right); $i++) {
            if (isset($array[$i]))
                $tem[] = $i >= $left ? "*" : $array[$i];
        }
        $array = array_chunk(array_reverse($array), $right);
        $array = array_reverse($array[0]);
        for ($i = 0; $i < $right; $i++) {
            $tem[] = $array[$i];
        }
        $string = implode("", $tem);
    }
    return $string;
}

/**
 * 创建前台地址文件
 *
 * @param int $pid
 * @param int $level
 * @param array $region
 * @return array
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function create_data_city($pid = 0, $level = 1, $region = [])
{
    if (!$region) {
        $list = model('Region')->field('id,name_cn,parent_id')->select();
        foreach ($list as $key => $val) {
            if (isset($region[$val['parent_id']])) {
                $region[$val['parent_id']][] = $val;
            } else {
                $region[$val['parent_id']] = [$val];
            }
        }
    }
    $arr = [];
    if (isset($region[$pid])) {
        foreach ($region[$pid] as $k => $v) {
            $arrCity = [
                'value' => $v['id'],
                'text' => $v['name_cn']
            ];
            if (isset($region[$v['id']]) && count($region[$v['id']]) > 0) {
                $arrCity['children'] = create_data_city($v['id'], $level + 1, $region);
            }
            $arr[] = $arrCity;
        }
    }
    return $arr;
}

/**
 * 油卡请求接口返回内容
 *
 * @param string $url [请求的URL地址]
 * @param string $params [请求的参数]
 * @param int $ipost [是否采用POST形式]
 * @return  string
 */
function juhe_curl($url, $params = false, $ispost = 0)
{
    $httpInfo = array();
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'JuheData');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    $response = curl_exec($ch);
    if ($response === FALSE) {
        //echo "cURL Error: " . curl_error($ch);
        return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
    curl_close($ch);
    return $response;
}


/* * ******************php验证身份证号码是否正确函数******************** */

function check_idcard($code)
{
    $id = strtoupper($code);
    $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
    $arr_split = array();
    if (!preg_match($regx, $id)) {
        return FALSE;
    }
    if (15 == strlen($id)) { //检查15位 
        $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

        @preg_match($regx, $id, $arr_split);
        //检查生日日期是否正确 
        $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) {
            return FALSE;
        } else {
            return TRUE;
        }
    } else {      //检查18位 
        $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        @preg_match($regx, $id, $arr_split);
        $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
        if (!strtotime($dtm_birth)) { //检查生日日期是否正确 
            return FALSE;
        } else {
            //检验18位身份证的校验码是否正确。 
            //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。 
            $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            $sign = 0;
            for ($i = 0; $i < 17; $i++) {
                $b = (int) $id{$i};
                $w = $arr_int[$i];
                $sign += $b * $w;
            }
            $n = $sign % 11;
            $val_num = $arr_ch[$n];
            if ($val_num != substr($id, 17, 1)) {
                return FALSE;
            } //phpfensi.com 
            else {
                return TRUE;
            }
        }
    }
}
