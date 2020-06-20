<?php
    if(!function_exists('parse_padding')){
        function parse_padding($source)
        {
            $length  = strlen(strval(count($source['source']) + $source['first']));
            return 40 + ($length - 1) * 8;
        }
    }

    if(!function_exists('parse_class')){
        function parse_class($name)
        {
            $names = explode('\\', $name);
            return '<abbr title="'.$name.'">'.end($names).'</abbr>';
}
}

if(!function_exists('parse_file')){
function parse_file($file, $line)
{
return '<a class="toggle" title="'."{$file} line {$line}".'">'.basename($file)." line {$line}".'</a>';
}
}

if(!function_exists('parse_args')){
function parse_args($args)
{
$result = [];

foreach ($args as $key => $item) {
switch (true) {
case is_object($item):
$value = sprintf('<em>object</em>(%s)', parse_class(get_class($item)));
break;
case is_array($item):
if(count($item) > 3){
$value = sprintf('[%s, ...]', parse_args(array_slice($item, 0, 3)));
} else {
$value = sprintf('[%s]', parse_args($item));
}
break;
case is_string($item):
if(strlen($item) > 20){
$value = sprintf(
'\'<a class="toggle" title="%s">%s...</a>\'',
htmlentities($item),
htmlentities(substr($item, 0, 20))
);
} else {
$value = sprintf("'%s'", htmlentities($item));
}
break;
case is_int($item):
case is_float($item):
$value = $item;
break;
case is_null($item):
$value = '<em>null</em>';
break;
case is_bool($item):
$value = '<em>' . ($item ? 'true' : 'false') . '</em>';
break;
case is_resource($item):
$value = '<em>resource</em>';
break;
default:
$value = htmlentities(str_replace("\n", '', var_export(strval($item), true)));
break;
}

$result[] = is_int($key) ? $value : "'{$key}' => {$value}";
}

return implode(', ', $result);
}
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex,nofollow" />
    <title>系统发生错误</title>
    <style>
        .hinesdaBox {
            text-align: center;
            padding:0px 10px;
            padding-top:12%;
        }
        .hinesdaBox img {
            width:35%;
            max-width:180px;
        }
        .hinesdatx{
            font-size:20px;
            color:#666666;
        }
    </style>
    <style>
        /* Base */
        body {
            color: #333;
            font: 16px Verdana, "Helvetica Neue", helvetica, Arial, 'Microsoft YaHei', sans-serif;
            margin: 0;
            padding: 0 20px 20px;
        }
        h1{
            margin: 10px 0 0;
            font-size: 28px;
            font-weight: 500;
            line-height: 32px;
        }
        h2{
            color: #4288ce;
            font-weight: 400;
            padding: 6px 0;
            margin: 6px 0 0;
            font-size: 18px;
            border-bottom: 1px solid #eee;
        }
        h3{
            margin: 12px;
            font-size: 16px;
            font-weight: bold;
        }
        abbr{
            cursor: help;
            text-decoration: underline;
            text-decoration-style: dotted;
        }
        a{
            color: #868686;
            cursor: pointer;
        }
        a:hover{
            text-decoration: underline;
        }
        .line-error{
            background: #f8cbcb;
        }

        .echo table {
            width: 100%;
        }

        .echo pre {
            padding: 16px;
            overflow: auto;
            font-size: 85%;
            line-height: 1.45;
            background-color: #f7f7f7;
            border: 0;
            border-radius: 3px;
            font-family: Consolas, "Liberation Mono", Menlo, Courier, monospace;
        }

        .echo pre > pre {
            padding: 0;
            margin: 0;
        }

        /* Exception Info */
        .exception {
            margin-top: 20px;
        }
        .exception .message{
            padding: 12px;
            border: 1px solid #ddd;
            border-bottom: 0 none;
            line-height: 18px;
            font-size:16px;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
            font-family: Consolas,"Liberation Mono",Courier,Verdana,"微软雅黑";
        }

        .exception .code{
            float: left;
            text-align: center;
            color: #fff;
            margin-right: 12px;
            padding: 16px;
            border-radius: 4px;
            background: #999;
        }
        .exception .source-code{
            padding: 6px;
            border: 1px solid #ddd;

            background: #f9f9f9;
            overflow-x: auto;

        }
        .exception .source-code pre{
            margin: 0;
        }
        .exception .source-code pre ol{
            margin: 0;
            color: #4288ce;
            display: inline-block;
            min-width: 100%;
            box-sizing: border-box;
            font-size:14px;
            font-family: "Century Gothic",Consolas,"Liberation Mono",Courier,Verdana;
            padding-left: <?php echo (isset($source) && !empty($source)) ? parse_padding($source) : 40;  ?>px;
        }
        .exception .source-code pre li{
            border-left: 1px solid #ddd;
            height: 18px;
            line-height: 18px;
        }
        .exception .source-code pre code{
            color: #333;
            height: 100%;
            display: inline-block;
            border-left: 1px solid #fff;
            font-size:14px;
            font-family: Consolas,"Liberation Mono",Courier,Verdana,"微软雅黑";
        }
        .exception .trace{
            padding: 6px;
            border: 1px solid #ddd;
            border-top: 0 none;
            line-height: 16px;
            font-size:14px;
            font-family: Consolas,"Liberation Mono",Courier,Verdana,"微软雅黑";
        }
        .exception .trace ol{
            margin: 12px;
        }
        .exception .trace ol li{
            padding: 2px 4px;
        }
        .exception div:last-child{
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        /* Exception Variables */
        .exception-var table{
            width: 100%;
            margin: 12px 0;
            box-sizing: border-box;
            table-layout:fixed;
            word-wrap:break-word;
        }
        .exception-var table caption{
            text-align: left;
            font-size: 16px;
            font-weight: bold;
            padding: 6px 0;
        }
        .exception-var table caption small{
            font-weight: 300;
            display: inline-block;
            margin-left: 10px;
            color: #ccc;
        }
        .exception-var table tbody{
            font-size: 13px;
            font-family: Consolas,"Liberation Mono",Courier,"微软雅黑";
        }
        .exception-var table td{
            padding: 0 6px;
            vertical-align: top;
            word-break: break-all;
        }
        .exception-var table td:first-child{
            width: 28%;
            font-weight: bold;
            white-space: nowrap;
        }
        .exception-var table td pre{
            margin: 0;
        }

        /* Copyright Info */
        .copyright{
            margin-top: 24px;
            padding: 12px 0;
            border-top: 1px solid #eee;
        }

        /* SPAN elements with the classes below are added by prettyprint. */
        pre.prettyprint .pln { color: #000 }  /* plain text */
        pre.prettyprint .str { color: #080 }  /* string content */
        pre.prettyprint .kwd { color: #008 }  /* a keyword */
        pre.prettyprint .com { color: #800 }  /* a comment */
        pre.prettyprint .typ { color: #606 }  /* a type name */
        pre.prettyprint .lit { color: #066 }  /* a literal value */
        /* punctuation, lisp open bracket, lisp close bracket */
        pre.prettyprint .pun, pre.prettyprint .opn, pre.prettyprint .clo { color: #660 }
        pre.prettyprint .tag { color: #008 }  /* a markup tag name */
        pre.prettyprint .atn { color: #606 }  /* a markup attribute name */
        pre.prettyprint .atv { color: #080 }  /* a markup attribute value */
        pre.prettyprint .dec, pre.prettyprint .var { color: #606 }  /* a declaration; a variable name */
        pre.prettyprint .fun { color: red }  /* a function name */
    </style>
</head>
<body>

<div class="echo">
    <?php echo $echo;?>
</div>
<?php if(\think\facade\App::isDebug()) { ?>
<div class="exception">
    <div class="message">

        <div class="info">
            <div>
                <h2>[<?php echo $code; ?>]&nbsp;<?php echo sprintf('%s in %s', parse_class($name), parse_file($file, $line)); ?></h2>
            </div>
            <div><h1><?php echo nl2br(htmlentities($message)); ?></h1></div>
        </div>

    </div>
    <?php if(!empty($source)){?>
    <div class="source-code">
    <pre class="prettyprint lang-php"><ol start="<?php echo $source['first']; ?>"><?php foreach ((array) $source['source'] as $key => $value) { ?><li class="line-<?php echo $key + $source['first']; ?>"><code><?php echo htmlentities($value); ?></code></li><?php } ?></ol></pre>
</div>
<?php }?>
<div class="trace">
    <h2>Call Stack</h2>
    <ol>
        <li><?php echo sprintf('in %s', parse_file($file, $line)); ?></li>
        <?php foreach ((array) $trace as $value) { ?>
        <li>
            <?php
                    // Show Function
                    if($value['function']){
                        echo sprintf(
                            'at %s%s%s(%s)',
                            isset($value['class']) ? parse_class($value['class']) : '',
                            isset($value['type'])  ? $value['type'] : '',
                            $value['function'],
                            isset($value['args'])?parse_args($value['args']):''
                        );
                    }

                    // Show line
                    if (isset($value['file']) && isset($value['line'])) {
                        echo sprintf(' in %s', parse_file($value['file'], $value['line']));
                    }
                ?>
        </li>
        <?php } ?>
    </ol>
</div>
</div>
<?php } else { ?>
<div class="hinesdaBox">
    <img src="data:image/png;base64,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" alt="">
    <div class="hinesdatx"><?php echo htmlentities($message); ?></div>
</div>
<?php } ?>

<?php if(!empty($datas)){ ?>
<div class="exception-var">
    <h2>Exception Datas</h2>
    <?php foreach ((array) $datas as $label => $value) { ?>
    <table>
        <?php if(empty($value)){ ?>
        <caption><?php echo $label; ?><small>empty</small></caption>
        <?php } else { ?>
        <caption><?php echo $label; ?></caption>
        <tbody>
        <?php foreach ((array) $value as $key => $val) { ?>
        <tr>
            <td><?php echo htmlentities($key); ?></td>
            <td>
                <?php
                            if(is_array($val) || is_object($val)){
                                echo htmlentities(json_encode($val, JSON_PRETTY_PRINT));
                            } else if(is_bool($val)) {
                                echo $val ? 'true' : 'false';
                            } else if(is_scalar($val)) {
                                echo htmlentities($val);
                            } else {
                                echo 'Resource';
                            }
                        ?>
            </td>
        </tr>
        <?php } ?>
        </tbody>
        <?php } ?>
    </table>
    <?php } ?>
</div>
<?php } ?>

<?php if(!empty($tables)){ ?>
<div class="exception-var">
    <h2>Environment Variables</h2>
    <?php foreach ((array) $tables as $label => $value) { ?>
    <table>
        <?php if(empty($value)){ ?>
        <caption><?php echo $label; ?><small>empty</small></caption>
        <?php } else { ?>
        <caption><?php echo $label; ?></caption>
        <tbody>
        <?php foreach ((array) $value as $key => $val) { ?>
        <tr>
            <td><?php echo htmlentities($key); ?></td>
            <td>
                <?php
                            if(is_array($val) || is_object($val)){
                                echo htmlentities(json_encode($val, JSON_PRETTY_PRINT));
                            } else if(is_bool($val)) {
                                echo $val ? 'true' : 'false';
                            } else if(is_scalar($val)) {
                                echo htmlentities($val);
                            } else {
                                echo 'Resource';
                            }
                        ?>
            </td>
        </tr>
        <?php } ?>
        </tbody>
        <?php } ?>
    </table>
    <?php } ?>
</div>
<?php } ?>
<?php if(\think\facade\App::isDebug()) { ?>
<script>
    var LINE = <?php echo $line; ?>;

    function $(selector, node){
        var elements;

        node = node || document;
        if(document.querySelectorAll){
            elements = node.querySelectorAll(selector);
        } else {
            switch(selector.substr(0, 1)){
                case '#':
                    elements = [node.getElementById(selector.substr(1))];
                    break;
                case '.':
                    if(document.getElementsByClassName){
                        elements = node.getElementsByClassName(selector.substr(1));
                    } else {
                        elements = get_elements_by_class(selector.substr(1), node);
                    }
                    break;
                default:
                    elements = node.getElementsByTagName();
            }
        }
        return elements;

        function get_elements_by_class(search_class, node, tag) {
            var elements = [], eles,
                pattern  = new RegExp('(^|\\s)' + search_class + '(\\s|$)');

            node = node || document;
            tag  = tag  || '*';

            eles = node.getElementsByTagName(tag);
            for(var i = 0; i < eles.length; i++) {
                if(pattern.test(eles[i].className)) {
                    elements.push(eles[i])
                }
            }

            return elements;
        }
    }

    $.getScript = function(src, func){
        var script = document.createElement('script');

        script.async  = 'async';
        script.src    = src;
        script.onload = func || function(){};

        $('head')[0].appendChild(script);
    }

    ;(function(){
        var files = $('.toggle');
        var ol    = $('ol', $('.prettyprint')[0]);
        var li    = $('li', ol[0]);

        // 短路径和长路径变换
        for(var i = 0; i < files.length; i++){
            files[i].ondblclick = function(){
                var title = this.title;

                this.title = this.innerHTML;
                this.innerHTML = title;
            }
        }

        // 设置出错行
        var err_line = $('.line-' + LINE, ol[0])[0];
        err_line.className = err_line.className + ' line-error';

        $.getScript('//cdn.bootcss.com/prettify/r298/prettify.min.js', function(){
            prettyPrint();

            // 解决Firefox浏览器一个很诡异的问题
            // 当代码高亮后，ol的行号莫名其妙的错位
            // 但是只要刷新li里面的html重新渲染就没有问题了
            if(window.navigator.userAgent.indexOf('Firefox') >= 0){
                ol[0].innerHTML = ol[0].innerHTML;
            }
        });

    })();
</script>
<?php } ?>
</body>
</html>