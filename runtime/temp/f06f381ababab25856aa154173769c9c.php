<?php /*a:2:{s:59:"/www/wwwroot/tlsj/application/admin/view/index/welcome.html";i:1577103178;s:57:"/www/wwwroot/tlsj/application/admin/view/public/base.html";i:1577103178;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>后台</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    
    <link rel="stylesheet" type="text/css" href="/static/layuiadmin/layui/css/layui.css" /><link rel="stylesheet" type="text/css" href="/static/layuiadmin/style/admin.css" />
    
    <link rel="stylesheet" type="text/css" href="/vendor/pace/css/pace-theme-flash.css" />
    <link rel="icon" href="<?php echo get_img_show_url(zf_cache('web_info.web_ico')); ?>" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo get_img_show_url(zf_cache('web_info.web_ico')); ?>" type="image/x-icon"/>
    
</head>
<body>

<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-sm6 layui-col-md6">
                <div class="layui-card">
                    <div class="layui-card-header">用户量<span class="layui-badge layui-bg-blue layuiadmin-badge">总</span></div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font"><span id="total_user_num">0</span></p>
                        <p>冻结用户<span class="layuiadmin-span-color"><span id="lock_user_num">0</span><i class="layui-inline layui-icon layui-icon-user"></i></span></p>
                    </div>
                </div>
            </div>
            <div class="layui-col-sm6 layui-col-md6">
                <div class="layui-card">
                    <div class="layui-card-header">新增用户<span class="layui-badge layui-bg-cyan layuiadmin-badge">日</span></div>
                    <div class="layui-card-body layuiadmin-card-list">
                        <p class="layuiadmin-big-font"><span id="new_user_num">0</span></p>
                        <p>未审会员<span class="layuiadmin-span-color"><span id="no_activate_user_num">0</span><i class="layui-inline layui-icon layui-icon-user"></i></span></p>
                    </div>
                </div>
            </div>
        </div>



        <div class="layui-row layui-col-space15">



            <div class="layui-col-sm6">
                <div class="layui-card">
                    <div class="layui-card-header">操作日志 <span style="color:red;">IP地址查询: <a href="https://www.ip.cn" target="_blank">ip.cn</a></span><a lay-href="<?php echo url('Auth/adminLogList', ["type" => "console"]); ?>" lay-text='操作日志' class="layui-badge layui-bg-orange layuiadmin-badge">更多</a></div>
                    <div class="layui-card-body">
                        <table class="layui-table" lay-data="{height:315, url:'<?php echo url("Auth/adminLogList", ["type" => "console"]); ?>', page:false, request:{pageName:'p',limitName:'p_num'},limit:9, size: 'sm'}">
                            <thead>
                            <tr>
                                <th lay-data="{field:'equipment'}">设备型号</th>
                                <th lay-data="{field:'add_time'}">操作时间</th>
                                <th lay-data="{field:'note'}">操作信息</th>
                                <th lay-data="{field:'ip'}">操作IP</th>
                            </tr>
                            </thead>
                        </table>
                        <!--<table class="layui-table layuiadmin-page-table" lay-skin="line">
                            <thead>
                                <tr>
                                    <th>设备型号	</th>
                                    <th>最后登录时间</th>
                                    <th>登录IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="first">胡歌</span></td>
                                    <td><i class="layui-icon layui-icon-log"> 11:20</i></td>
                                    <td><span>在线</span></td>
                                </tr>
                                <tr>
                                    <td><span class="second">彭于晏</span></td>
                                    <td><i class="layui-icon layui-icon-log"> 10:40</i></td>
                                    <td><span>在线</span></td>
                                </tr>
                                <tr>
                                    <td><span class="third">靳东</span></td>
                                    <td><i class="layui-icon layui-icon-log"> 01:30</i></td>
                                    <td><i>离线</i></td>
                                </tr>
                                <tr>
                                    <td>吴尊</td>
                                    <td><i class="layui-icon layui-icon-log"> 21:18</i></td>
                                    <td><i>离线</i></td>
                                </tr>
                                <tr>
                                    <td>许上进</td>
                                    <td><i class="layui-icon layui-icon-log"> 09:30</i></td>
                                    <td><span>在线</span></td>
                                </tr>
                                <tr>
                                    <td>小蚊子</td>
                                    <td><i class="layui-icon layui-icon-log"> 21:18</i></td>
                                    <td><i>在线</i></td>
                                </tr>
                                <tr>
                                    <td>贤心</td>
                                    <td><i class="layui-icon layui-icon-log"> 09:30</i></td>
                                    <td><span>在线</span></td>
                                </tr>
                            </tbody>
                        </table>-->
                    </div>
                </div>
            </div>

            <div class="layui-col-sm6">
                <div class="layui-card">
                    <div class="layui-card-header">数据概览</div>
                    <div class="layui-card-body">

                        <div class="layui-carousel layadmin-carousel layadmin-dataview" data-anim="fade" lay-filter="LAY-index-dataview">
                            <div carousel-item id="LAY-index-dataview">
                                <div><i class="layui-icon layui-icon-loading1 layadmin-loading"></i></div>
                                <div></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="layui-row layui-col-space15">
            <div class="layui-col-sm12">
                <div class="layui-card">
                    <div class="layui-card-header">系统信息</div>
                    <div class="layui-card-body layui-text">
                        <table class="layui-table">
                            <tbody>
                                <tr>
                                    <td>系统运行环境</td>
                                    <td><?php echo htmlentities($sysInfo['os']); ?></td>
                                    <td>服务器IP</td>
                                    <td><?php echo htmlentities($sysInfo['ip']); ?></td>
                                </tr>
                                <tr>
                                    <td>PHP 版本</td>
                                    <td><?php echo htmlentities($sysInfo['phpv']); ?></td>
                                    <td>服务器环境</td>
                                    <td><?php echo htmlentities($sysInfo['web_server']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>绑定域名</strong></td>
                                    <td><?php echo htmlentities($sysInfo['domain']); ?></td>
                                    <td><strong>服务器时间</strong></td>
                                    <td><?php echo date('Y-m-d H:i:s'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript" src="/static/layuiadmin/layui/layui.js"></script><script type="text/javascript" src="/static/js/jquery.min.js"></script><script type="text/javascript" src="/static/js/admin.js"></script><script type="text/javascript" src="/vendor/pace/js/pace.min.js"></script>

<script>
    layui.config({
        base: '/static/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'console','carousel','echarts'], function(){
        var $ = layui.$
            ,carousel = layui.carousel
            ,echarts = layui.echarts
            ,elemDataView = $('#LAY-index-dataview').children('div')
            ,renderDataView = function(index){
            echartsApp[index] = echarts.init(elemDataView[index], layui.echartsTheme);
            echartsApp[index].setOption(options[index]);
            window.onresize = echartsApp[index].resize;
        };
        var echartsApp = [], options = [];
        function getUserData(){
            $.get("<?php echo url('getChartUserInfo'); ?>", function (res) {
                var userDatas = formatParams(res[1], 'count'),userTime = formatParams(res[1], 'time'),
                    pieUserLevelName = formatParams(res[2]['data'], 'name'),pieLevelColor = formatParams(res[2]['data'], 'color');
                options = [
                    //访客浏览器分布
                    {
                        title : {
                            text: '级别会员统计',
                            x: 'center',
                            textStyle: {
                                fontSize: 14
                            }
                        },
                        tooltip : {
                            trigger: 'item',
                            formatter: "{a} <br/>{b} : {c} ({d}%)"
                        },
                        color:pieLevelColor,
                        legend: {
                            orient : 'vertical',
                            x : 'left',
                            data:pieUserLevelName
                        },
                        series : [{
                            name:'会员数量',
                            type:'pie',
                            radius : '55%',
                            center: ['50%', '50%'],
                            data:res[2]['data']
                        }]
                    },

                    //新增的用户量
                    {
                        title: {
                            text: '最近一周新增的用户量',
                            x: 'center',
                            textStyle: {
                                fontSize: 14
                            }
                        },
                        tooltip : { //提示框
                            trigger: 'axis',
                            formatter: "{b}<br>新增用户：{c}"
                        },
                        xAxis : [{ //X轴
                            type : 'category',
                            data : userTime
                        }],
                        yAxis : [{  //Y轴
                            type : 'value'
                        }],
                        series : [{ //内容
                            type: 'line',
                            data:userDatas,
                        }]
                    }
                ];


                //没找到DOM，终止执行
                if(!elemDataView[0]) return;



                renderDataView(0);

                //监听数据概览轮播
                var carouselIndex = 0;
                carousel.on('change(LAY-index-dataview)', function(obj){
                    renderDataView(carouselIndex = obj.index);
                });

                //监听侧边伸缩
                layui.admin.on('side', function(){
                    setTimeout(function(){
                        renderDataView(carouselIndex);
                    }, 300);
                });

                //监听路由
                layui.admin.on('hash(tab)', function(){
                    layui.router().path.join('') || renderDataView(carouselIndex);
                });
            });
        }
        getUserData();
        function formatParams(datas, field = '') {
            var arr = [];
            for (elem in datas) {
                arr.push(datas[elem][field]);
            }

            return arr;
        }

        var isRequest = true;
        function getStatistics() {
            if(isRequest) {
                isRequest = false;
                $.get("<?php echo url('Index/getStatistics'); ?>", function (res) {
                    isRequest = true;
                    $('#total_user_num').html(res.user.total_user_num);
                    $('#lock_user_num').html(res.user.lock_user_num);
                    $('#new_user_num').html(res.user.new_user_num);
                    $('#no_activate_user_num').html(res.user.no_activate_user_num);
                    $('#total_expenditure').html(res.amount.total_expenditure);
                    $('#today_expenditure').html(res.amount.today_expenditure);
                    $('#total_income').html(res.amount.total_income);
                    $('#today_income').html(res.amount.today_income);
                });
            }
        }
        setInterval(getStatistics, 10000);

        getStatistics();
    });
</script>

</body>
</html>