<?php /*a:4:{s:83:"/www/wwwroot/tlsj/application/wap/config/../view/default/drumbeat/vide_ad_info.html";i:1577103174;s:73:"/www/wwwroot/tlsj/application/wap/config/../view/default/public/base.html";i:1577103176;s:75:"/www/wwwroot/tlsj/application/wap/config/../view/default/public/header.html";i:1577103176;s:75:"/www/wwwroot/tlsj/application/wap/config/../view/default/public/footer.html";i:1577103176;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>广告</title>
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

<style>
    .footer {
        position: fixed;
        width: 100%;
        bottom: 0;
        cursor: pointer;
    }
    img {
        width: 100%;
    }
    .ads{
        height: 80px;
        background: rgba(22,23,28,0.95);
    }
    .ads img{
        width: 65px;
        position: absolute;
        left: 30px;
        bottom: 7px;
    }
    .ads button{
        width: 100px;
        height: 40px;
        color: #fff;
        font-size: 16px;
        background: #FF2084;
        border-radius: 10px;
        border: 1px solid #FF2084;
        position: absolute;
        right: 30px;
        bottom: 17px;
    }
</style>
<div class="mui-content">
    <div class="advertiCont">
        <div id="videobox">
            <video webkit-playsinline playsinline x5-playsinline x-webkit-airplay="allow" <?php echo !empty($device_type) ? '' : ' muted'; ?> autoplay="autoplay" id="videoALL">
                <source src="<?php echo $adInfo['url']; ?>" type="video/mp4">
            </video>
        </div>
        <div class="footer pop ads" id="footer">
            <img src="<?php echo $adInfo['ico']; ?>" style="cursor: pointer;"/>
            <button>立即下载</button>
        </div>
        <script>
            var videoALL = document.getElementById('videoALL');
            var videobox = document.getElementById('videobox');
            var clientWidth = document.documentElement.clientWidth;
            var clientHeight = document.documentElement.clientHeight;
            function stylediv(divId){
                divId.style.width = clientWidth + 'px';
                divId.style.height = clientHeight + 'px';
            }
            stylediv(videobox);
            playcontr();
            function playcontr(){
                videoALL.style.width = '100%';
                videoALL.style.height = '100%';
                videobox.style.display = "block";
                videoALL.play();
            }
            document.getElementById("footer").onclick=function(){
                window.location.href = '<?php echo $adInfo['app']; ?>';
            };
        </script>
    </div>
    <div class="adverticz df">
        <div class="advertzbk">
            <div class="adverticzdjs">30</div>
            <!--<div class="advertzbkfh likeA" data-url="<?php echo U('videoAdPlayEnd', ['type' => $type]); ?>"><i class="mui-icon mui-icon-closeempty i"></i></div>-->
        </div>
        <div class="fx1"></div>
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

<script>
    $(function(){
        document.addEventListener("WeixinJSBridgeReady", function() {
            videoALL.play();
        }, false);
        var num = <?php echo htmlentities($adInfo['times']); ?>;
        var t=setInterval(function(){
            num--;
            $(".adverticzdjs").html(num);
            if(num == 0){
                clearInterval(t);
                $('.adverticzdjs').css('display','none');
                // $('.advertzbkfh').css('display','block');
                setTimeout(function () {
                    window.location.href = "<?php echo U('videoAdPlayEnd', ['type' => $type]); ?>";
                },1000);
            }
        },1100);
    });
</script>


</body>
</html>