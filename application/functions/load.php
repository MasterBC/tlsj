<?php

$path = __DIR__;
if (file_exists($path . '/init.php')) {
    require($path . '/init.php');
} else {
    $content = '<?php' . PHP_EOL;
    $files = [];
    $handler = opendir($path);
    while (($filename = readdir($handler)) !== false) {//务必使用!==，防止目录下出现类似文件名“0”等情况
        $explodeFileName = explode('.', $filename);
        if (count($explodeFileName) > 1) {
            if ($filename != "." && $filename != ".." && $filename != 'init.php' && $filename != 'load.php' && $explodeFileName[1] == 'php') {
                $files[] = $filename;
            }
        }
    }
    foreach ($files as $value) {
        $content .= substr(php_strip_whitespace($path . '/' . $value), 6);
    }

    file_put_contents($path . '/init.php', $content);
    require($path . '/init.php');
}