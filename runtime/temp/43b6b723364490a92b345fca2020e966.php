<?php /*a:4:{s:81:"/www/wwwroot/tlsj/application/wap/config/../view/default/login/account_index.html";i:1577103174;s:73:"/www/wwwroot/tlsj/application/wap/config/../view/default/public/base.html";i:1577103176;s:75:"/www/wwwroot/tlsj/application/wap/config/../view/default/public/header.html";i:1577103176;s:75:"/www/wwwroot/tlsj/application/wap/config/../view/default/public/footer.html";i:1577103176;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>登录</title>
    <link rel="stylesheet" href="/template/wap/default/Static/font-awesome/css/font-awesome.min.css" media="all">
<link rel="stylesheet" type="text/css" href="/template/wap/default/Static/css/mui.min.css" />
<!--<link rel="stylesheet" type="text/css" href="/template/wap/default/Static/css/style.css" />-->
<link rel="stylesheet" type="text/css" href="/template/wap/default/Static/css/mui.showLoading.css" />
<!--<link rel="stylesheet" type="text/css" href="/template/wap/default/Static/css/intl-tel-input-master/build/css/demo.css">
<link rel="stylesheet" type="text/css" href="/template/wap/default/Static/css/intl-tel-input-master/build/css/intlTelInput.css">-->
<link rel="stylesheet" type="text/css" href="/template/wap/default/Static/css/centStyle.css?v=1.107" />
<link rel="stylesheet" type="text/css" href="/template/wap/default/Static/css/jquery.slider.css" />

    
<style>
    #captcha .geetest_holder {
        width:100% !important;
    }
    .mui-content{
        background: url("/static/images/login_bg1.png") no-repeat;
        background-size: 100% 100%;
    }
</style>

</head>
<body>

    <div class="mui-content">
        <div class="LoginImgbox">
            <img src="<?php echo get_img_domain(); ?><?php echo zf_cache('web_info.web_logo'); ?>" alt="" class="LoginImg_1">
        </div>
        <div class="LoginText">
            <form class="regForm">
                <div class="muiInputRow">
                    <div class="mui-input-row">
                        <input type="text" name="username" autocomplete="off" class="mui-input-clear muiInput" placeholder="会员账号">
                    </div>
                </div>
                <div class="muiInputRow">
                    <div class="mui-input-row">
                        <input type="password" name="password" autocomplete="off" class="mui-input-password muiInput" placeholder="会员密码">
                    </div>
                </div>
                <div class="muiInputRow">
                    <div class="mui-input-row" style="position: relative;">
                        <input type="text" name="verify_code" autocomplete="off" class="mui-input-clear muiInput" placeholder="验证码">
                        <img src="/verify?type=home_login" alt="" class="yzmaImg Home_Login_Yzm">
                    </div>
                </div>
                <div class="gonggButtonda" style="padding-top:20px;">
                    <button type="button" class="mui-btn accountLogin gonggButton">立即登录</button>
                </div>
                <div class="linkACZuo">
                    <span class="span_1 linkACZuobd likeA" data-url="<?php echo U('Reg/index'); ?>">立即注册</span>
                    <span class="likeA" data-url="<?php echo U('login/mobileIndex'); ?>">手机验证码登录</span>
                </div>
            </form>
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

<script src="/static/js/gt.js"></script>
<script>
    mui('body').on('tap', '.yzmaImg', refreshVerify);
    var mask=mui.createMask();
    mui('body').on('tap', '.accountLogin', function () {
        var obj = $(this);
        var data = $('.regForm').serialize();
        $(obj).attr('disabled', 'true');
        mui.showLoading("登录中","div");
        mask.show();//显示遮罩层
        $.ajax({
            type: 'post',
            url: '<?php echo U(""); ?>',
            data: data,
            dataType: 'json',
            success: function (res) {
                mask.close();//关闭遮罩层
                mui.hideLoading();
                if (res.code == 1) {
                    mui.toast('登录成功', {duration: '2000', type: 'div'});
                    setTimeout(function () {
                        window.location.href = "<?php echo U('User/Index'); ?>";
                    }, 2000);
                } else {
                    refreshVerify();
                    $(obj).removeAttr('disabled');
                    return mui.toast(res.msg, {duration: '2000', type: 'div'});
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
            }
        });
        $('.muiInput').blur();
    });
    function refreshVerify()
    {
        $('.Home_Login_Yzm').attr('src', "/verify?type=home_login&t="+Math.random());
    }
</script>


</body>
</html>