<?php /*a:2:{s:57:"/www/wwwroot/tlsj/application/admin/view/index/index.html";i:1577103178;s:57:"/www/wwwroot/tlsj/application/admin/view/public/base.html";i:1577103178;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>后台管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    
    <link rel="stylesheet" type="text/css" href="/static/layuiadmin/layui/css/layui.css" /><link rel="stylesheet" type="text/css" href="/static/layuiadmin/style/admin.css" />
    
    <link rel="stylesheet" type="text/css" href="/vendor/pace/css/pace-theme-flash.css" />
    <link rel="icon" href="<?php echo get_img_show_url(zf_cache('web_info.web_ico')); ?>" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo get_img_show_url(zf_cache('web_info.web_ico')); ?>" type="image/x-icon"/>
    
<style>
    .layadmin-side-shrink .layui-layout-admin .layui-logo{
        background-image: url(<?php echo get_img_show_url(zf_cache('web_info.web_ico')); ?>);
    }
</style>

</head>
<body>

<div id="LAY_app">
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <!-- 头部区域 -->
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item layadmin-flexible" lay-unselect>
                    <a href="javascript:;" layadmin-event="flexible" title="侧边伸缩">
                        <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="/" target="_blank" title="前台">
                        <i class="layui-icon layui-icon-website"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs clear_test_data">
                    <a href="javaScript:void(0);" title="清除测试数据">清除测试数据</a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" layadmin-event="refresh" title="刷新">
                        <i class="layui-icon layui-icon-refresh-3"></i>
                    </a>
                </li>
            </ul>
            <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">

                <li class="layui-nav-item" lay-unselect>
                    <a lay-href="<?php echo url('User/messageLogList'); ?>" id="message_num" layadmin-event="message" lay-text="消息中心">
                        <i class="layui-icon layui-icon-notice"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="theme">
                        <i class="layui-icon layui-icon-theme"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="note">
                        <i class="layui-icon layui-icon-note"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a href="javascript:;" layadmin-event="fullscreen">
                        <i class="layui-icon layui-icon-screen-full"></i>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;"> <cite>功能模拟</cite> </a>
                    <dl class="layui-nav-child">
                        <dd><a href="javaScript:void(0);" class="manualGiveTurnDayNum">检测赠送转盘劵</a></dd>
                        <dd><a href="javaScript:void(0);" class="manualGiveVideoDayNum">检测视频次数</a></dd>
                        <dd><a href="javaScript:void(0);" class="manualGiveShakeDayNum">检测摇一摇次数</a></dd>
                        <!--<dd><a href="javaScript:void(0);" class="manualMoneyNumLevel">检测算力升级</a></dd>-->
                        <!--<dd><a href="javaScript:void(0);" class="manualMminingNum">检测生产挖矿</a></dd>-->
                        <dd><a href="javaScript:void(0);" class="autoDayBonus">检测每日分红</a></dd>

                    </dl>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;">
                        <cite>清除缓存</cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a href="javaScript:void(0);" class="clearCache" data-type="temp">清除模板缓存</a></dd>
                        <dd><a href="javaScript:void(0);" class="clearCache" data-type="data">清除数据缓存</a></dd>
                        <dd><a href="javaScript:void(0);" class="clearCache" data-type="config">清除配置缓存</a></dd>
                        <dd><a href="javaScript:void(0);" class="clearCache" data-type="db">清除数据库缓存</a></dd>
                        <dd><a href="javaScript:void(0);" class="clearCache" data-type="all">清除所有缓存</a></dd>
                    </dl>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;">
                        <cite><?php echo htmlentities($adminUserInfo['user_name']); ?></cite>
                    </a>
                    <dl class="layui-nav-child">
                        <dd><a class="edit_info">基本资料</a></dd>
                        <hr>
                        <dd style="text-align: center;" class="logout"><a>退出</a></dd>
                    </dl>
                </li>

                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <i class="layui-icon layui-icon-more-vertical"></i>
                </li>
                <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
                    <a href="javascript:;" layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
                </li>
            </ul>
        </div>

        <!-- 侧边菜单 -->
        <div class="layui-side layui-side-menu">
            <div class="layui-side-scroll">
                <div class="layui-logo" lay-href="<?php echo url('Index/welcome'); ?>">
                    <span><?php echo zf_cache('web_info.web_name'); ?></span>
                </div>

                <ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
                    <li data-name="home" class="layui-nav-item layui-nav-itemed">
                        <a href="javascript:;" lay-tips="主页" lay-direction="2">
                            <i class="layui-icon">&#xe68e;</i>
                            <cite>主页</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="console" class="layui-this">
                                <a lay-href="<?php echo url('Index/welcome'); ?>">系统控制台</a>
                            </dd>
                        </dl>
                    </li>
                    <?php if(is_array($rules) || $rules instanceof \think\Collection || $rules instanceof \think\Paginator): if( count($rules)==0 ) : echo "" ;else: foreach($rules as $key=>$v): ?>
                    <li data-name="home" class="layui-nav-item">
                        <a href="javascript:;" lay-tips="主页" lay-direction="2">
                            <i class="layui-icon <?php echo htmlentities((isset($v['icon']) && ($v['icon'] !== '')?$v['icon']:'layui-icon-more-vertical')); ?>"></i>
                            <cite><?php echo htmlentities($v['title']); ?></cite>
                        </a>
                        <dl class="layui-nav-child">
                            <?php if($v[$v['id']]): if(is_array($v[$v['id']]) || $v[$v['id']] instanceof \think\Collection || $v[$v['id']] instanceof \think\Paginator): if( count($v[$v['id']])==0 ) : echo "" ;else: foreach($v[$v['id']] as $key=>$val): ?>
                            <dd data-name="console" class="<?php echo $key==0 ? 'layui-this'  :  ''; ?>">
                                <a lay-href="<?php echo url($val['name']); ?>"><?php echo htmlentities($val['title']); ?></a>
                            </dd>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                            <?php endif; ?>
                        </dl>
                    </li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </div>
        </div>

        <!-- 页面标签 -->
        <div class="layadmin-pagetabs" id="LAY_app_tabs">
            <div class="layui-icon layadmin-tabs-control layui-icon-prev" layadmin-event="leftPage"></div>
            <div class="layui-icon layadmin-tabs-control layui-icon-next" layadmin-event="rightPage"></div>
            <div class="layui-icon layadmin-tabs-control layui-icon-down">
                <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
                    <li class="layui-nav-item" lay-unselect>
                        <a href="javascript:;"></a>
                        <dl class="layui-nav-child layui-anim-fadein">
                            <dd layadmin-event="closeThisTabs"><a href="javascript:;">关闭当前标签页</a></dd>
                            <dd layadmin-event="closeOtherTabs"><a href="javascript:;">关闭其它标签页</a></dd>
                            <dd layadmin-event="closeAllTabs"><a href="javascript:;">关闭全部标签页</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
            <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs">
                <ul class="layui-tab-title" id="LAY_app_tabsheader">
                    <li lay-id="<?php echo url('index/welcome'); ?>" lay-attr="<?php echo url('index/welcome'); ?>" class="layui-this">
                        <i class="layui-icon layui-icon-home"></i></li>
                </ul>
            </div>
        </div>


        <!-- 主体内容 -->
        <div class="layui-body" id="LAY_app_body">
            <div class="layadmin-tabsbody-item layui-show">
                <iframe src="<?php echo url('index/welcome'); ?>" frameborder="0" class="layadmin-iframe"></iframe>
            </div>
        </div>

        <!-- 辅助元素，一般用于移动设备下遮罩 -->
        <div class="layadmin-body-shade" layadmin-event="shade"></div>
    </div>
</div>



<script type="text/javascript" src="/static/layuiadmin/layui/layui.js"></script><script type="text/javascript" src="/static/js/jquery.min.js"></script><script type="text/javascript" src="/static/js/admin.js"></script><script type="text/javascript" src="/vendor/pace/js/pace.min.js"></script>

<script>
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use('index');
    $('.logout').click(function () {
        var url = "<?php echo url('Index/logout'); ?>";
        var loadAdd = layer.msg('退出中', {icon: 16, time: 0, shade: 0.1});
        $.post(url, function (data) {
            layer.close(loadAdd);
            if (data.code != 1) {
                layer.msg(data.msg, {
                    icon: 5
                });
            } else {
                layer.msg(data.msg, {
                    icon: 6
                },function(){
                    window.location.reload();
                });
            }
        });
    });
    $('.clearCache').click(function () {
        var url = "<?php echo url('Index/cleanCache'); ?>";
        var loadAdd = layer.msg('清除缓存中', {icon:16, time:0, shade:0.1});
        $.post(url, {type:$(this).data('type')}, function (data) {
            layer.close(loadAdd);
            if (data.code != 1) {
                layer.msg(data.msg, {
                    icon: 5
                });
            } else {
                layer.msg(data.msg, {
                    icon: 6
                });
            }
        });
    });
    $('.clear_test_data').click(function () {
        var url = "<?php echo url('DbManage/clearTestData'); ?>";
        layer.prompt({title:'请输入密码', formType:1}, function(value, index, elem){
            var loadAdd = layer.msg('清除中', {icon: 16, time: 0, shade: 0.1});
            $.post(url, {password:value}, function (data) {
                layer.close(loadAdd);
                if (data.code != 1) {
                    layer.msg(data.msg, {
                        icon: 5
                    });
                } else {
                    layer.close(index);
                    layer.msg(data.msg, {
                        icon: 6
                    });
                }
            });
        });
    });
    $(document).on('click', '.edit_info', function(){
        var url = '<?php echo url("AdminUser/editInfo"); ?>';
        layer.open({
            type: 2
            , title: '修改资料'
            , content: url
            , area: ['500px', '450px']
            , shadeClose: true
            , btnAlign: 'c'
            , btn: ['确定', '取消']
            , yes: function (index, layero) {
                var iframeWindow = window['layui-layer-iframe' + index]
                    , submit = layero.find('iframe').contents().find("#submitBtn");

                //监听提交
                iframeWindow.layui.form.on('submit(submitBtn)', function (data) {
                    var field = data.field; //获取提交的字段

                    var loadAdd = layer.msg('提交中', {icon: 16, time: 0, shade: 0.1, offset: '15px'});
                    $.post(url, field, function (res) {
                        layer.close(loadAdd);
                        if (res.code == 1) {
                            layer.close(index); //关闭弹层
                            layer.msg(res.msg, {icon: 6, offset: '15px'});
                        } else {
                            layer.msg(res.msg, {icon: 5, offset: '15px'});
                        }
                    });
                });

                submit.trigger('click');
            }
        });
    });

    var messageNum = 0;
    function getMessageNum()
    {
        $.get("<?php echo url('Index/getMessageNum'); ?>", function (res) {
            if(res.code == 1) {
                if(res.num > messageNum) {

                    $('#message_num').append('<span class="layui-badge-dot"></span>');
                }
                messageNum = res.num;
            } else {
                return layer.msg(res.msg, {icon: 5, offset: '15px'});
            }
        });
    }
    setInterval(getMessageNum, 10000);

    getMessageNum();
</script>
<!-- 赠送转盘劵-->
<script>
$('.manualGiveTurnDayNum').click(function () {
        var url = "<?php echo url('Index/autoGiveTurnDayNum'); ?>";
        var loadAdd = layer.msg('检测中', {icon:16, time:0, shade:0.1});
        $.post(url, {type:$(this).data('type')}, function (data) {
            layer.close(loadAdd);
            if (data.code != 1) {
                layer.msg(data.msg, {
                    icon: 5
                });
            } else {
                layer.msg(data.msg, {
                    icon: 6
                });
            }
        });
    });
</script>
<!-- 根据算力升级-->
<script>
$('.manualMoneyNumLevel').click(function () {
        var url = "<?php echo url('Index/autoMoneyNumLevel'); ?>";
        var loadAdd = layer.msg('检测中', {icon:16, time:0, shade:0.1});
        $.post(url, {type:$(this).data('type')}, function (data) {
            layer.close(loadAdd);
            if (data.code != 1) {
                layer.msg(data.msg, {
                    icon: 5
                });
            } else {
                layer.msg(data.msg, {
                    icon: 6
                });
            }
        });
    });
</script>
<!-- 根据算力升级-->
<script>
$('.manualMminingNum').click(function () {
        var url = "<?php echo url('Index/autoMaticMining'); ?>";
        var loadAdd = layer.msg('检测中', {icon:16, time:0, shade:0.1});
        $.post(url, {type:$(this).data('type')}, function (data) {
            layer.close(loadAdd);
            if (data.code != 1) {
                layer.msg(data.msg, {
                    icon: 5
                });
            } else {
                layer.msg(data.msg, {
                    icon: 6
                });
            }
        });
    });
</script>
<!-- 每日分红-->
<script>
$('.autoDayBonus').click(function () {
        var url = "<?php echo url('Index/autoDayBonusMoney'); ?>";
        var loadAdd = layer.msg('检测中', {icon:16, time:0, shade:0.1});
        $.post(url, {type:$(this).data('type')}, function (data) {
            layer.close(loadAdd);
            if (data.code != 1) {
                layer.msg(data.msg, {
                    icon: 5
                });
            } else {
                layer.msg(data.msg, {
                    icon: 6
                });
            }
        });
    });
</script>
<!-- 赠送摇一摇-->
<script>
$('.manualGiveShakeDayNum').click(function () {
        var url = "<?php echo url('Index/autoGiveTurnDayNum'); ?>";
        var loadAdd = layer.msg('检测中', {icon:16, time:0, shade:0.1});
        $.post(url, {type:$(this).data('type')}, function (data) {
            layer.close(loadAdd);
            if (data.code != 1) {
                layer.msg(data.msg, {
                    icon: 5
                });
            } else {
                layer.msg(data.msg, {
                    icon: 6
                });
            }
        });
    });
</script>
<!-- 看视频的次数-->
<script>
$('.manualGiveVideoDayNum').click(function () {
        var url = "<?php echo url('Index/autoGiveVideoDayNum'); ?>";
        var loadAdd = layer.msg('检测中', {icon:16, time:0, shade:0.1});
        $.post(url, {type:$(this).data('type')}, function (data) {
            layer.close(loadAdd);
            if (data.code != 1) {
                layer.msg(data.msg, {
                    icon: 5
                });
            } else {
                layer.msg(data.msg, {
                    icon: 6
                });
            }
        });
    });
</script>

</body>
</html>