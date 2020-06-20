<?php /*a:4:{s:71:"/www/wwwroot/tlsj/application/wap/config/../view/default/error/404.html";i:1577103174;s:73:"/www/wwwroot/tlsj/application/wap/config/../view/default/public/base.html";i:1577103176;s:75:"/www/wwwroot/tlsj/application/wap/config/../view/default/public/header.html";i:1577103176;s:75:"/www/wwwroot/tlsj/application/wap/config/../view/default/public/footer.html";i:1577103176;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>404 页面不存在</title>
    <link rel="stylesheet" href="/template/wap/default/Static/font-awesome/css/font-awesome.min.css" media="all">
<link rel="stylesheet" type="text/css" href="/template/wap/default/Static/css/mui.min.css" />
<!--<link rel="stylesheet" type="text/css" href="/template/wap/default/Static/css/style.css" />-->
<link rel="stylesheet" type="text/css" href="/template/wap/default/Static/css/mui.showLoading.css" />
<!--<link rel="stylesheet" type="text/css" href="/template/wap/default/Static/css/intl-tel-input-master/build/css/demo.css">
<link rel="stylesheet" type="text/css" href="/template/wap/default/Static/css/intl-tel-input-master/build/css/intlTelInput.css">-->
<link rel="stylesheet" type="text/css" href="/template/wap/default/Static/css/centStyle.css?v=1.107" />
<link rel="stylesheet" type="text/css" href="/template/wap/default/Static/css/jquery.slider.css" />

    
</head>
<body>

<div class="mui-content">
    <div class="errorpromBox">
        <div class="errorpromimgda"><img src="/template/wap/default/Static/centImages/x_47.png" alt=""></div>
        <div class="errorpromimgxiao"><img src="/template/wap/default/Static/centImages/x_46.png" alt=""></div>
        <div class="errorpromtx">你所访问的页面找不到了</div>
        <div class="errorpromtx">去其它页面看看吧！</div>
        <div class="df errorpromAnBox">
            <div class="fx1 errorpromLeft">
                <div class="mui-action-back errorpromzuAn">返回上一页</div>
            </div>
            <div class="fx1 errorpromRight">
                <div class="errorpromyuAn backIndex likeA" data-url="<?php echo U('User/index'); ?>">返回首页</div>
            </div>
        </div>
    </div>
</div>



    <script src="/template/wap/default/Static/js/mui.min.js" type="text/javascript" charset="utf-8"></script>
<script src="/template/wap/default/Static/js/mui.showLoading.js" type="text/javascript" charset="utf-8"></script>
<script src="/template/wap/default/Static/js/style.js?v=2.0.1" type="text/javascript" charset="utf-8"></script>
<script src="/template/wap/default/Static/js/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<!--<script src="/template/wap/default/Static/css/intl-tel-input-master/build/js/intlTelInput.js"></script>-->
<script src="/template/wap/default/Static/js/jquery.slider.min.js" type="text/javascript" charset="utf-8"></script>
<script>
    function moneyTransformation(money)
    {
        var show_money = 0;
        if (money >= 1000000000000) {
            show_money = (parseInt(money) / 1000000000000).toFixed(2)+'t';
        } else if (money >= 1000000000) {
            show_money = (parseInt(money) / 1000000000).toFixed(2)+'b';
        } else if (money >= 1000000) {
            show_money = (parseInt(money) / 1000000).toFixed(2)+'m';
        } else if (money >= 1000) {
            show_money = (parseInt(money) / 1000).toFixed(2)+'k';
        }

        return show_money;
    }
    function getNoticeNum() {
        $.ajax({
            url: "<?php echo U('Message/getNoticeNum'); ?>",
            type: "post",
            dataType: 'json',
            success: function (res) {
                $('.noticeNum').html(res.num);
            },
            error: function (e) {
                console.log("网络错误，请重试！！");
            }
        });
    }
    function getUserOwnedMoney() {
        $.ajax({
            url: "<?php echo U('Money/getAjaxUserOwnedMoneyArr'); ?>",
            type: "post",
            dataType: 'json',
            success: function (res) {
                $('.user-money-two').attr('data-money', res.userOwnedMoneyAll[2]);
                $('.user-money-one').html(res.userOwnedMoneyAll[1]);
                $('.user-money-two').html(moneyTransformation(res.userOwnedMoneyAll[2]));
                $('.user-money-three').html(res.userOwnedMoneyAll[3]);
                if(res.webDayMoney) {
                    $('.web-day-money').html(res.userOwnedMoneyAll[3]);
                }
            },
            error: function (e) {
                console.log("网络错误，请重试！！");
            }
        });
    }
    function getUserOwnedBlock() {
        $.ajax({
            url: "<?php echo U('Block/getAjaxUserOwnedBlockArr'); ?>",
            type: "post",
            dataType: 'json',
            success: function (res) {
                $('.user-block-one').html(res.userOwnedBlockAll[1]);
            },
            error: function (e) {
                console.log("网络错误，请重试！！");
            }
        });
    }
    getNoticeNum();
    getUserOwnedMoney();
    getUserOwnedBlock();
    setInterval(function () {
        getNoticeNum();
        getUserOwnedMoney();
        getUserOwnedBlock();
    }, 10000);
</script>



</body>
</html>