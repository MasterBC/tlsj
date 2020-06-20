<?php /*a:5:{s:72:"/www/wwwroot/tlsj/application/wap/config/../view/default/user/index.html";i:1577929340;s:73:"/www/wwwroot/tlsj/application/wap/config/../view/default/public/base.html";i:1577103176;s:75:"/www/wwwroot/tlsj/application/wap/config/../view/default/public/header.html";i:1577103176;s:75:"/www/wwwroot/tlsj/application/wap/config/../view/default/public/footer.html";i:1577103176;s:79:"/www/wwwroot/tlsj/application/wap/config/../view/default/public/footer_nav.html";i:1577685340;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>首页</title>
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
img{
   pointer-events:none;
}
.mrythudHe{
    position: absolute;
    z-index: 9999;
}
.qipao{
    width: 23%;
    position: absolute;
    text-align: center;
}
.qipao1{
    left: 0px;
    top: 42px;
    
}
.mrythudHe1{
    left: -4px;
    top:-2vh;
}
.qipao2{
    left:25%;
    top:42px;
    
}
.mrythudHe2{
    left:24%;
    top:-2vh;
}
.qipao3{
    left: 50%;
    top:42px;
   
}
.mrythudHe3{
    left: 49%;
    top:-2vh;
}
.qipao4{
    left: 75%;
    top:42px;
    
}
.mrythudHe4{
    left: 74%;
    top:-2vh;
}
.qipao5{
    left: 0px;
    top:48%;
  
}
.mrythudHe5{
    left: -4px;
    top:29%;
}
.qipao6{
    left: 25%;
    top:48%;
  
}
.mrythudHe6{
    left: 24%;
    top:29%;
}

.qipao7{
    left: 50%;
    top:48%;
    
}
.mrythudHe7{
    left: 49%;
    top:29%;
}

.qipao8{
    left: 75%;
    top:48%;
  
}
.mrythudHe8{
    left: 74%;
    top:29%;
}

.qipao9{
    left: 0px;
    top:83%;
   
}
.mrythudHe9{
    left: -5px;
    top:64%;
}

.qipao10{
    left: 25%;
    top:83%;
   
}
.mrythudHe10{
    left: 24%;
    top:64%;
}

.qipao11{
    left: 50%;
    top:83%;
    
}
.mrythudHe11{
    left: 49%;
    top:64%;
}

.qipao12{
    left: 74%;
    top:83%;
}
.mrythudHe12{
    
    left: 74%;
    top:64%;
}
.img_word{
    position: absolute;
    top: -16px;
    right: -20px;
    width: 50% !important;
}
.mrythud{
    width: 100%;
}
@media screen and (max-width: 330px) {
    .mrythudHe1,.mrythudHe2,.mrythudHe3,.mrythudHe4{
        top: -1vh;
    }
}
.mask{
    position: fixed;
    z-index: 9000;
    width: 100%;
    height: 100%;
    top:0px;
    left: 0px;
    display: none;
    
}
</style>
<div class="mask">

</div>
<div class="mui-content unhomeBg" id="app" style="bottom:50px;">
    <div class="unhomeCont" style="overflow: hidden">
        <div class="mrytopBox">
            <div class="df">
                <!--<div class="mryjinbBox user-block-one likeA btneffectanfd" data-url="<?php echo U('Block/logList', ['bid' => 1]); ?>">0..00</div>-->
                <div class="fx1">
                    <div class="df mryhuibBox">
                        <div class="widbzimg"><img src="/template/wap/default/Static/centImages/2019a071_152.png" alt=""></div>
                        <div class=" fx1 likeA web-day-money btneffectanfd" data-url="<?php echo U('Money/webMoneyDayDetails'); ?>">0.00</div>
                    </div>
                </div>
                <div class="mrymreyhb fx1 homepaupbtn btneffectanfd">
                    <div class="df">
                        <div class="trtryjsBox">
                            <!--角色图 s-->
                            <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_<?php echo htmlentities($maxProductInfo['id']); ?>.png" alt="" class="jssimg">
                            <!--角色图 e-->
                            <img src="/template/wap/default/Static/centImages/2019a071sy_3.png" alt="" class="kkimg">
                        </div>
                        <div class="fx1">
                            <div class="mrymrmc user_max_product_name"><?php echo htmlentities($maxProductInfo['product_name']); ?></div>
                            <div class="mrymrdj">LV.<span class="user_max_product_number"><?php echo isset($maxProductInfo['number']) ? htmlentities($maxProductInfo['number']) : '0'; ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mrytnavBox">
            <div class="df">
                <div class="mrytnavHe rankinubtn btneffectanfd">
                    <img src="/template/wap/default/Static/centImages/2019a071_11.png" alt="">
                    <div class="mrytnavtx">排行榜</div>
                </div>
                <!-- <div class="mrytnavHe likeA btneffectanfd" data-url="<?php echo U('Help/helpIndex'); ?>">
                    <img src="/template/wap/default/Static/centImages/2019a071_12.png" alt="">
                    <div class="mrytnavtx">新手教程</div>
                </div> -->
                <div class="mrytnavHe settnszupbtn btneffectanfd">
                    <img src="/template/wap/default/Static/centImages/2019a071_17.png" alt="">
                    <div class="mrytnavtx">设置</div>
                </div>
                <div class="mrytnavHe ustratupbtn btneffectanfd">
                    <img src="/template/wap/default/Static/centImages/2019a071_13.png" alt="">
                    <div class="mrytnavtx">图鉴</div>
                </div>
                <div class="mrytnavHe lotteuptcan btneffectanfd">
                    <img src="/template/wap/default/Static/centImages/2019a071_14.png" alt="">
                    <div class="mrytnavtx">转盘</div>
                </div>
                <div class="mrytnavHe likeA btneffectanfd" data-url="<?php echo U('Notice/noticeIndex'); ?>">
                    <img src="/template/wap/default/Static/centImages/2019a071_15.png" alt="">
                    <div class="mrytnavtx">公告</div>
                </div>
<!--                <div class="fx1 mrytnavHe drawulkbtn" id="reward_stay_receive_status" style="display:none;">-->
                <div class="fx1 mrytnavHe receiveAward btneffectanfd" id="reward_stay_receive_status" style="display:none;">
                    <div class="mrytnavlqu"><img src="/template/wap/default/Static/centImages/2019a071_16.png" alt=""></div>
                    <div class="mrytnavtx">领取</div>
                </div>
                <div class="fx1 mrytnavHe btneffectanfd" id="reward_countdown_status" style="display:none;">
                    <div class="mrytnavlqu"><img src="/template/wap/default/Static/centImages/2019a071_16.png" alt=""></div>
                    <div class="mrytnavtx" id="reward_countdown_info">领取</div>
                </div>
            </div>
        </div>
        <div class="mrytnavBox">
            <div class="df">
                <div class="mryzhdb">
                    <div class="mryzhdbzi user-money-two">0</div>
                </div>
                <!-- <div class="mryzhjiny settnszupbtn btneffectanfd"><img src="/template/wap/default/Static/centImages/2019a071_17.png" alt=""></div> -->
                <div class="mryzhjinshu huodrandobtn btneffectanfd"><img src="/template/wap/default/Static/centImages/2019a071_21.png" alt=""></div>
            </div>
        </div>
        <!-- 滑动区域 【暂时还不能滑动】 s -->
        <div class="fx1 huangdquyeBox">
            <?php if($user['video_num'] > 0): ?>
            <!--宝箱 左右循环飞 s-->
            <div class="treasuflyBox">
                <div class="treasufly buoy_ad_btn">
                    <img src="/template/wap/default/Static/centImages/0111.gif" alt="">
                </div>
            </div>
            <!--宝箱 左右循环飞 e-->
            <?php endif; ?>
            <div class="huangdquye">
                <div class="fx1"></div>
                <div class="mrythudBox clearfix" style="position: relative;height: 45vh;">
                    <div class="qipao qipao1"><img width="80%" src="/template/wap/default/Static/centImages/2019a071_39_1.png" alt="" srcset=""></div>
                    <div class="qipao qipao2"><img width="80%" src="/template/wap/default/Static/centImages/2019a071_39_1.png" alt="" srcset=""></div>
                    <div class="qipao qipao3"><img width="80%" src="/template/wap/default/Static/centImages/2019a071_39_1.png" alt="" srcset=""></div>
                    <div class="qipao qipao4"><img width="80%" src="/template/wap/default/Static/centImages/2019a071_39_1.png" alt="" srcset=""></div>
                    <div class="qipao qipao5"><img width="80%" src="/template/wap/default/Static/centImages/2019a071_39_1.png" alt="" srcset=""></div>
                    <div class="qipao qipao6"><img width="80%" src="/template/wap/default/Static/centImages/2019a071_39_1.png" alt="" srcset=""></div>
                    <div class="qipao qipao7"><img width="80%" src="/template/wap/default/Static/centImages/2019a071_39_1.png" alt="" srcset=""></div>
                    <div class="qipao qipao8"><img width="80%" src="/template/wap/default/Static/centImages/2019a071_39_1.png" alt="" srcset=""></div>
                    <div class="qipao qipao9"><img width="80%" src="/template/wap/default/Static/centImages/2019a071_39_1.png" alt="" srcset=""></div>
                    <div class="qipao qipao10"><img width="80%" src="/template/wap/default/Static/centImages/2019a071_39_1.png" alt="" srcset=""></div>
                    <div class="qipao qipao11"><img width="80%" src="/template/wap/default/Static/centImages/2019a071_39_1.png" alt="" srcset=""></div>
                    <div class="qipao qipao12"><img width="80%" src="/template/wap/default/Static/centImages/2019a071_39_1.png" alt="" srcset=""></div>
                    <div id="user_product_content">
                    </div>
                </div>
                <div class="fx1"></div>
            </div>
        </div>
        <!-- 滑动区域 e -->

        <!-- 尾部 -->
        <div class="mrydwbtmBox">
            <div class="df mrybtmNavBox">
                <div class="mrybtmNav btneffectansx yaoqingjuan"><img src="/template/wap/default/Static/centImages/2019a071_24.png" alt=""></div>
                <div class="mrybtmNav expediteupan btneffectansx jsudhsBox">
                    <div class="jsudhs">200秒</div>
                    <img src="/template/wap/default/Static/centImages/2019a071_25.png" alt="">
                </div>
                <div class="mrybtmlvq fx1 buyProduct buy_max_product_id btneffectansx" data-id data-is_show_list="2">
                    <div class="df">
                        <div class="mrybtmlvqimg"><img class="buy_max_product_picture" src="" alt=""></div>
                        <div class="fx1">
                            <div class="mrybtmlvdji buy_max_product_name"></div>
                            <div class="mrylvqmm buy_max_product_price" style="background:unset;font-size: 14px;padding-left:0;"></div>
                        </div>
                    </div>
                </div>
                <div class="mrybtmNav storeupbtn btneffectansx"><img src="/template/wap/default/Static/centImages/2019a071_26.png" alt=""></div>
                <div class="mrybtmhuis huishouupbtn btneffectansx" data-id="ljt"><img src="/template/wap/default/Static/centImages/2019a071_36.png" alt=""></div>
            </div>
        </div>
    </div>
</div>

<!--我的宠物弹窗 s-->
<div class="homepaupowBox">
    <div class="homepaupCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">我的宠物</div>
            </div>
            <div class="homepauptzw btneffectansx homepachadan"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="homepwdcw"><img class="user_max_product_picture" src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_<?php echo htmlentities($maxProductInfo['id']); ?>.png" alt=""></div>
        <div class="homepwdcwmc user_max_product_name"><?php echo htmlentities($maxProductInfo['product_name']); ?></div>
        <div class="homepwdshjBox df">
            <div class="fx1">
                <div class="homepwdshsu user_max_product_income"><?php echo htmlentities($maxProductInfo['income']); ?></div>
                <div class="homepwdshtx">在线金币</div>
            </div>
            <div class="fx1">
                <div class="homepwdshsu user_max_product_offline_income"><?php echo htmlentities($maxProductInfo['offline_income']); ?></div>
                <div class="homepwdshtx">离线金币</div>
            </div>
        </div>
    </div>
</div>
<!--我的宠物弹窗 e-->

<!--加速弹窗 s-->
<div class="expediteupBox">
    <div class="homepaupCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">加速</div>
            </div>
            <div class="homepauptzw btneffectansx expediteupqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="expedibtnBox">
            <div class="expedibtn btneffectansx js60">
                <div class="expedibtntx">加速60秒</div>
                <div class="expedibtnms"><img src="/template/wap/default/Static/centImages/2019a071_17.png" alt=""> X10</div>
            </div>
            <?php if($isvideo): ?>
            <div class="expedibtn btneffectansx likeA" data-url="<?php echo U('Drumbeat/videoAd', ['type' => 'balance_bz']); ?>">
            <?php else: ?>
            <div class="expedibtn btneffectansx videos">
            <?php endif; ?>
                <div class="expedibtntx">加速200秒</div>
                <div class="expedibtnms">观看广告立即加速</div>
            </div>
            <!--<div class="expedibtn likeA"
                 data-url="https://i.fawulu.com/activities/?appKey=70b12e47b4d84b1b87b088bb0f741add&appEntrance=3&business=money">
                <div class="expedibtntx">加速200秒</div>
                <div class="expedibtnms">观看广告立即加速</div>
            </div>-->
        </div>
    </div>
</div>
<!--加速弹窗 e-->


<!--领取弹窗 s-->
<div class="drawupBox">
    <div class="homepaupCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">领取免费宝箱</div>
            </div>
            <div class="homepauptzw btneffectansx drawupqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="drawupimgzt"><img src="/template/wap/default/Static/centImages/2019a071_84.png" alt=""></div>
        <div class="drawupitj">+<span id="reward_money">0K</span></div>
        <div class="drawupiqd drawupqx btneffectansx">确定</div>
    </div>
</div>
<!--领取弹窗 e-->

<!--排行榜弹窗 s-->
<div class="rankinupBox">
    <div class="rankinupCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">世界排行榜</div>
            </div>
            <div class="homepauptzw btneffectansx rankinupqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="ranklistBox" id="leaderboard_content">
        </div>
<!--        <div class="rankyqhyan">邀请好友</div>-->
    </div>
</div>
<!--排行榜弹窗 e-->



<!--商店弹窗 s-->
<div class="storeupBox">
    <div class="rankinupCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">商店</div>
            </div>
            <div class="homepauptzw btneffectansx storeupqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="storetjin df">
            <div class="fx1 storetjinsu user-money-two">0</div>
        </div>
        <div class="ranklistBox" id="product_content">
        </div>
    </div>
</div>
<!--商店弹窗 e-->

<!--图鉴弹窗 s-->
<div class="ustratupBox">
    <div class="rankinupCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">图鉴</div>
            </div>
            <div class="homepauptzw btneffectansx ustratupqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="ustralistBox">
            <div class="ustralistHe df">
                <div class="ustrajtuimg">
                    <?php if(isset($userProductCount[45])): ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_45.png" alt="">
                    <?php else: ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_45_no.png" alt="">
                    <?php endif; ?>
                    <div><?php echo isset($productNameArr[45]) ? htmlentities($productNameArr[45]) : '牛魔王'; ?></div>
                </div>
                
                <div class="fx1 ustrmchBox">
                    <div class="ustrmch_1">技能</div>
                    <div class="ustrmch_2">每天获得宠物世界广告收益分红20%</div>
                    <div class="ustrmch_1">获得途径</div>
                    <div class="ustrmch_3">1.两只37级牛合成有概率获得</div>
                    <div class="ustrmch_3">2.金木水火土五牛合体100%获得</div>
                </div>
            </div>
            <div class="ustralistHe df">
                <div class="ustrajtuimg">
                    <?php if(isset($userProductCount[44])): ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_44.png" alt="">
                    <?php else: ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_44_no.png" alt="">
                    <?php endif; ?>
                    <div><?php echo isset($productNameArr[44]) ? htmlentities($productNameArr[44]) : '织女'; ?></div>
                </div>
                <div class="fx1 ustrmchBox">
                    <div class="ustrmch_1">技能</div>
                    <div class="ustrmch_2">织女与牛郎合成必得52元现金红包</div>
                    <div class="ustrmch_1">获得途径</div>
                    <div class="ustrmch_3">1.两只37级牛合成有概率获得</div>
                </div>
            </div>
            <div class="ustralistHe df">
                <div class="ustrajtuimg">
                    <?php if(isset($userProductCount[43])): ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_43.png" alt="">
                    <?php else: ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_43_no.png" alt="">
                    <?php endif; ?>
                    <div><?php echo isset($productNameArr[43]) ? htmlentities($productNameArr[43]) : '牛郞'; ?></div>
                </div>
                <div class="fx1 ustrmchBox">
                    <div class="ustrmch_1">技能</div>
                    <div class="ustrmch_2">牛郎与织女合成必得52元现金红包</div>
                    <div class="ustrmch_1">获得途径</div>
                    <div class="ustrmch_3">1.两只37级牛合成有概率获得</div>
                </div>
            </div>
            <div class="ustralistHe df">
                <div class="ustrajtuimg">
                    <?php if(isset($userProductCount[42])): ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_42.png" alt="">
                    <?php else: ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_42_no.png" alt="">
                    <?php endif; ?>
                    <div><?php echo isset($productNameArr[42]) ? htmlentities($productNameArr[42]) : '土牛'; ?></div>
                </div>
                <div class="fx1 ustrmchBox">
                    <div class="ustrmch_1">技能</div>
                    <div class="ustrmch_2">每天活得宠物世界广告收益分红20%</div>
                    <div class="ustrmch_1">获得途径</div>
                    <div class="ustrmch_3">1.两只37级牛合成有概率获得</div>
                    <div class="ustrmch_3">2.金木水火土五牛合体100%获得</div>
                    <div class="ustrmch_3">3.土牛与土牛合成得随机红包</div>
                </div>
            </div>
            <div class="ustralistHe df">
                <div class="ustrajtuimg">
                    <?php if(isset($userProductCount[41])): ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_41.png" alt="">
                    <?php else: ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_41_no.png" alt="">
                    <?php endif; ?>
                    <div><?php echo isset($productNameArr[41]) ? htmlentities($productNameArr[41]) : '火牛'; ?></div>
                </div>
                <div class="fx1 ustrmchBox">
                    <div class="ustrmch_1">技能</div>
                    <div class="ustrmch_2">每天活得宠物世界广告收益分红20%</div>
                    <div class="ustrmch_1">获得途径</div>
                    <div class="ustrmch_3">1.两只37级牛合成有概率获得</div>
                    <div class="ustrmch_3">2.金木水火土五牛合体100%获得</div>
                    <div class="ustrmch_3">3.火牛与火牛合成得随机红包</div>
                </div>
            </div>
            <div class="ustralistHe df">
                <div class="ustrajtuimg">
                    <?php if(isset($userProductCount[40])): ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_40.png" alt="">
                    <?php else: ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_40_no.png" alt="">
                    <?php endif; ?>
                    <div><?php echo isset($productNameArr[40]) ? htmlentities($productNameArr[40]) : '水牛'; ?></div>
                </div>
                <div class="fx1 ustrmchBox">
                    <div class="ustrmch_1">技能</div>
                    <div class="ustrmch_2">每天活得宠物世界广告收益分红20%</div>
                    <div class="ustrmch_1">获得途径</div>
                    <div class="ustrmch_3">1.两只37级牛合成有概率获得</div>
                    <div class="ustrmch_3">2.金木水火土五牛合体100%获得</div>
                    <div class="ustrmch_3">3.水牛与水牛合成得随机红包</div>
                </div>
            </div>
            <div class="ustralistHe df">
                <div class="ustrajtuimg">
                    <?php if(isset($userProductCount[39])): ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_39.png" alt="">
                    <?php else: ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_39_no.png" alt="">
                    <?php endif; ?>
                    <div><?php echo isset($productNameArr[39]) ? htmlentities($productNameArr[39]) : '木牛'; ?></div>
                </div>
                <div class="fx1 ustrmchBox">
                    <div class="ustrmch_1">技能</div>
                    <div class="ustrmch_2">每天活得宠物世界广告收益分红20%</div>
                    <div class="ustrmch_1">获得途径</div>
                    <div class="ustrmch_3">1.两只37级牛合成有概率获得</div>
                    <div class="ustrmch_3">2.金木水火土五牛合体100%获得</div>
                    <div class="ustrmch_3">3.木牛与木牛合成得随机红包</div>
                </div>
            </div>
            <div class="ustralistHe df">
                <div class="ustrajtuimg">
                    <?php if(isset($userProductCount[38])): ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_38.png" alt="">
                    <?php else: ?>
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_38_no.png" alt="">
                    <?php endif; ?>
                    <div><?php echo isset($productNameArr[38]) ? htmlentities($productNameArr[38]) : '金牛'; ?></div>
                </div>
                <div class="fx1 ustrmchBox">
                    <div class="ustrmch_1">技能</div>
                    <div class="ustrmch_2">每天活得宠物世界广告收益分红20%</div>
                    <div class="ustrmch_1">获得途径</div>
                    <div class="ustrmch_3">1.两只37级牛合成有概率获得</div>
                    <div class="ustrmch_3">2.金木水火土五牛合体100%获得</div>
                    <div class="ustrmch_3">3.金牛与金牛合成得随机红包</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--图鉴弹窗 e-->

<!--设置弹窗 s-->
<div class="settnszBox">
    <div class="homepaupCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">设置</div>
            </div>
            <div class="homepauptzw btneffectansx settnupqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="settnsCont">

            <div class="settnsLsthe">
                <div class="settnsLstnr df">
                    <div class="fx1">
                        <div class="settnsLstmc">离线金币奖励通知</div>
                        <div class="settnsLstms">离线2小时后触发提醒功能</div>
                    </div>
                    <div class="settnsLszt settnsLsztan btneffectanfd">
                        <img src="/template/wap/default/Static/centImages/2019a071_102.png" alt="" class="img_gb">
                        <img src="/template/wap/default/Static/centImages/2019a071_101.png" alt="" class="img_dk">
                    </div>
                </div>
            </div>
<!--            <div class="settnsLsthe">
                <div class="settnsLstnr df">
                    <div class="fx1">
                        <div class="settnsLstmc">TLBC收取通知</div>
                        <div class="settnsLstms">超出2天时间未收取触发提醒功能</div>
                    </div>
                    <div class="settnsLszt settnsLsztan btneffectanfd">
                        <img src="/template/wap/default/Static/centImages/2019a071_102.png" alt="" class="img_gb">
                        <img src="/template/wap/default/Static/centImages/2019a071_101.png" alt="" class="img_dk">
                    </div>
                </div>
            </div>-->
            <div class="settnsLsthe">
                <div class="settnsLstnr df">
                    <div class="settnsLstmcone fx1">音效</div>
                    <div class="settnsLszt settnsLsztan btneffectanfd" data-music="1">
                        <img src="/template/wap/default/Static/centImages/2019a071_102.png" alt="" class="img_gb" id="music_1">
                        <img src="/template/wap/default/Static/centImages/2019a071_101.png" alt="" class="img_dk" id="music_2">
                    </div>
                </div>
            </div>
            <div class="settnsLngms">
                <div class="settnsLngms_1">提示：接受通知需关注“牛气冲天”服务号并绑定账号。</div>
                <div class="settnsLngms_2">如何绑定账号？</div>
            </div>
        </div>
    </div>
</div>
<!--设置弹窗 e-->


<!--转盘弹窗 s-->
<div class="lotterynrBox">
    <div class="lotteryContHe">
        <div class="df lotterytleBox">
            <div class="lotterytlezw"></div>
            <div class="lotterytle fx1">
                <img src="/template/wap/default/Static/turn/images/2019a071_114.png" alt="">
            </div>
            <div class="lotterytlezw btneffectansx lotteuptqx">
                <img src="/template/wap/default/Static/centImages/2019a071_64.png" alt="">
            </div>
        </div>
        <div class="lotteryzpan">
            <center>
                <div class="lottery">
                    <img src="/template/wap/default/Static/turn/images/2019a071_146.png" alt="" class="lotterybg">
                    <div class="myCanvasBox">
                        <canvas id="myCanvas" width="275" height="275">
                            当前浏览器版本过低，请使用其他浏览器尝试
                        </canvas>
                    </div>
                    <div class="clikoper"><img src="/template/wap/default/Static/turn/images/2019a071_145.png" alt=""></div>
                </div>
            </center>
        </div>
        <div class="lotterydizuo">
            <div class="lotterydizuoCnt">
                <div class="lotterydizshu">转盘卷 <?php echo isset($user['turn_num']) ? htmlentities($user['turn_num']) : '0'; ?></div>
                <div class="lotterydims">每日凌晨赠送<?php echo zf_cache('security_info.turn_day_give_num') ?? '0'; ?>张转盘卷</div>
            </div>
        </div>
        <div class="lotteryxzbtm">
            <?php if(($user['turn_num'] > 0)): ?>
                <div id="lotteryxzan">
                    <img src="/template/wap/default/Static/turn/images/2019a071_143.png" alt="" class="xsimg">
                    <img src="/template/wap/default/Static/turn/images/2019a071_143_1.png" alt="" class="ycimg">
                </div>
            <?php else: ?>
                <div id="lotterywal">
                    <img src="/template/wap/default/Static/turn/images/2019a071_143_1.png" alt=""><!--券已用完时 s-->
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!--转盘弹窗 e-->

<!--转盘抽奖获得弹窗 s-->
<div class="huodelotupBox">
    <div class="homepaupCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">恭喜获得</div>
            </div>
            <div class="homepauptzw btneffectansx homepaupqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="drawupimgzt"><img src="/template/wap/default/Static/centImages/2019a071_84.png" alt=""></div>
        <div id="message" class="drawupitj">0</div>
        <div class="drawupiqd homepaupqx btneffectansx">确定</div>
    </div>
</div>
<!--转盘抽奖获得弹窗 e-->


<!--随机合成 s-->
<div class="huodrandoBox">
    <div class="homepaupCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">随机合成</div>
            </div>
            <div class="homepauptzw btneffectansx huodrandoqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div id="raffle">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="raffle-unit raffle-unit-0">
                        <img src="<?php echo htmlentities($lotteryProducts[0]['picture']); ?>" alt="">
                        <div class="aiyasxbtxBa"><div class="aiyasxbtx"><?php echo htmlentities($lotteryProducts[0]['product_name']); ?></div></div>
                    </td>
                    <td class="raffle-unit raffle-unit-1">
                        <img src="<?php echo htmlentities($lotteryProducts[1]['picture']); ?>" alt="">
                        <div class="aiyasxbtxBa"><div class="aiyasxbtx"><?php echo htmlentities($lotteryProducts[1]['product_name']); ?></div></div>
                    </td>
                    <td class="raffle-unit raffle-unit-2">
                        <img src="<?php echo htmlentities($lotteryProducts[2]['picture']); ?>" alt="">
                        <div class="aiyasxbtxBa"><div class="aiyasxbtx"><?php echo htmlentities($lotteryProducts[2]['product_name']); ?></div></div>
                    </td>
                    <td class="raffle-unit raffle-unit-3">
                        <img src="<?php echo htmlentities($lotteryProducts[3]['picture']); ?>" alt="">
                        <div class="aiyasxbtxBa"><div class="aiyasxbtx"><?php echo htmlentities($lotteryProducts[3]['product_name']); ?></div></div>
                    </td>
                </tr>
                <tr>
                    <td class="raffle-unit raffle-unit-11">
                        <img src="<?php echo htmlentities($lotteryProducts[11]['picture']); ?>" alt="">
                        <div class="aiyasxbtxBa"><div class="aiyasxbtx"><?php echo htmlentities($lotteryProducts[11]['product_name']); ?></div></div>
                    </td>
                    <td colspan="2" rowspan="2" class="nageean"><a href="#"><img src="/template/wap/default/Static/centImages/2019a071_139.png" alt=""></a></td>
                    <td class="raffle-unit raffle-unit-4">
                        <img src="<?php echo htmlentities($lotteryProducts[4]['picture']); ?>" alt="">
                        <div class="aiyasxbtxBa"><div class="aiyasxbtx"><?php echo htmlentities($lotteryProducts[4]['product_name']); ?></div></div>
                    </td>
                </tr>
                <tr>
                    <td class="raffle-unit raffle-unit-10">
                        <img src="<?php echo htmlentities($lotteryProducts[10]['picture']); ?>" alt="">
                        <div class="aiyasxbtxBa"><div class="aiyasxbtx"><?php echo htmlentities($lotteryProducts[10]['product_name']); ?></div></div>
                    </td>
                    <td class="raffle-unit raffle-unit-5">
                        <img src="<?php echo htmlentities($lotteryProducts[5]['picture']); ?>" alt="">
                        <div class="aiyasxbtxBa"> <div class="aiyasxbtx"><?php echo htmlentities($lotteryProducts[5]['product_name']); ?></div></div>
                    </td>
                </tr>
                <tr>
                    <td class="raffle-unit raffle-unit-9">
                        <img src="<?php echo htmlentities($lotteryProducts[9]['picture']); ?>" alt="">
                        <div class="aiyasxbtxBa"> <div class="aiyasxbtx"><?php echo htmlentities($lotteryProducts[9]['product_name']); ?></div></div>
                    </td>
                    <td class="raffle-unit raffle-unit-8">
                        <img src="<?php echo htmlentities($lotteryProducts[8]['picture']); ?>" alt="">
                        <div class="aiyasxbtxBa"><div class="aiyasxbtx"><?php echo htmlentities($lotteryProducts[8]['product_name']); ?></div></div>
                    </td>
                    <td class="raffle-unit raffle-unit-7">
                        <img src="<?php echo htmlentities($lotteryProducts[7]['picture']); ?>" alt="">
                        <div class="aiyasxbtxBa"><div class="aiyasxbtx"><?php echo htmlentities($lotteryProducts[7]['product_name']); ?></div></div>
                    </td>
                    <td class="raffle-unit raffle-unit-6">
                        <img src="<?php echo htmlentities($lotteryProducts[6]['picture']); ?>" alt="">
                        <div class="aiyasxbtxBa"><div class="aiyasxbtx"><?php echo htmlentities($lotteryProducts[6]['product_name']); ?></div></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<!--随机合成 e-->

<!--随机合成获得弹窗 s-->
<div class="husuijihdupBox">
    <div class="homepaupCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">恭喜获得</div>
            </div>
            <div class="homepauptzw btneffectansx husuijihdupqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="drawupimgzt"><img src="aiyasimg" alt="" class="showImg"></div>
        <div id="aiyasxbtshuz" class="drawupitj">0</div>
        <div class="drawupiqd husuijihdupqx btneffectansx">确定</div>
    </div>
</div>
<!--随机合成获得弹窗 e-->


<!--回收弹窗 s-->
<div class="huishouupBox">
    <div class="homepaupCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">回收</div>
            </div>
            <div class="homepauptzw btneffectansx huishouupqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="drawupimgzt"><img src="/template/wap/default/Static/centImages/2019a071_66.png" alt=""></div>
        <div class="drawupitj">+<span id="huishou_money"></span></div>
        <div class="df drawupiqxxdav">
            <div class="fx1">
                <div class="drawupiqxx huishouupqx">取消</div>
            </div>
            <div class="fx1">
                <div class="drawupiqhus huishouupcr btneffectansx">确定</div>
            </div>
        </div>
    </div>
</div>
<!--回收弹窗 e-->

<!--离线收益 s-->
<!--<div class="offlineincomeBox" style="display: none;">
    <div class="homepaupCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">离线收益</div>
            </div>
            <div class="homepauptzw btneffectansx offlineincomeqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="drawupimgzt"><img src="/template/wap/default/Static/centImages/2019a071_66.png" alt=""></div>
        <div class="drawupitj">+<span id="offline_income_money"></span></div>
    </div>
</div>-->
<!--离线收益 e-->

<!--红包弹窗 s-->
<div class="redenvpepopBox mergeredenvpepopBox">
    <div class="redenvpepoyin"></div>
    <div class="redenvpCont">
        <img src="/template/wap/default/Static/centImages/2019a071_124.png" alt="" class="imgbbg">
        <div class="redenvpCodata">
            <div class="redenvpCodacaca">
                <div class="nvpCodacacaan mregenvpCodacacaan"><i class="mui-icon mui-icon-closeempty i"></i></div>
            </div>
            <div class="redenvcodlogo"><img src="<?php echo get_img_domain(); ?><?php echo zf_cache('web_info.web_logo'); ?>" alt=""></div>
            <div class="redenvcodmc"><?php echo zf_cache('web_info.web_name'); ?></div>
            <div class="redenvcodmss">合成红包</div>
        </div>
        <div class="redenvplinan">
            <span class="mergeredenvpepop_amount">0</span>
        </div>
        <div class="redenvplitixinBox">
            <div class="redenvplitixinn">可提现</div>
        </div>
    </div>
</div>
<div class="redenvpepopBox upgraderedenvpepopBox">
    <div class="redenvpepoyin"></div>
    <div class="redenvpCont">
        <img src="/template/wap/default/Static/centImages/2019a071_124.png" alt="" class="imgbbg">
        <div class="redenvpCodata">
            <div class="redenvpCodacaca">
                <div class="nvpCodacacaan upgradenvpCodacacaan"><i class="mui-icon mui-icon-closeempty i"></i></div>
            </div>
            <div class="redenvcodlogo"><img src="<?php echo get_img_domain(); ?><?php echo zf_cache('web_info.web_logo'); ?>" alt=""></div>
            <div class="redenvcodmc"><?php echo zf_cache('web_info.web_name'); ?></div>
            <div class="redenvcodmss">奖励你一个红包</div>
        </div>
        <div class="redenvplinan upgraderedenvpepoplin">
            <img src="/template/wap/default/Static/centImages/2019a071_123_1.png" alt="" class="redenvplinanbg">
            <img src="/template/wap/default/Static/centImages/2019a071_123_2.png" alt=""  class="redenvplinanan">
        </div>
        <div class="redenvplitixinBox">
            <div class="redenvplitixinn">可提现</div>
        </div>
    </div>
</div>
<!--红包弹窗 e-->


<!--五福合成弹窗 s-->
<div class="compoundhcBox">
    <div class="compoundCont">
        <div class="df lotterytleBox">
            <div class="lotterytlezw"></div>
            <div class="compoundtle fx1">
                <img src="/template/wap/default/Static/centImages/2019a071wf_5.png" alt="">
            </div>
            <div class="lotterytlezw btneffectansx compoundhcqx">
                <img src="/template/wap/default/Static/centImages/2019a071_64.png" alt="">
            </div>
        </div>
        <div class="compoundNr">
            <!--背景 s-->
            <img src="/template/wap/default/Static/centImages/2019a071wf_6.png" alt="" class="nmddbg">
            <!--背景 e-->
            <!--1 s-->
            <div class="compounoneBox heniubox" data-id="38">
                <div class="manzu" style="display:none">
                    <img src="/template/wap/default/Static/centImages/2019a071wf_2.png" alt="" class="rwebeijing">
                    <!--角色图 s-->
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_38.png" alt="" class="juesetu">
                    <!--角色图 e-->
                    <div class="compotext">金牛</div>
                </div>
                <div class="bumanzu">
                    <img src="/template/wap/default/Static/centImages/2019a071wf_2.png" alt="" class="rwebeijing">
                    <!--角色图 s-->
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_38_no.png" alt="" class="juesetu">
                    <!--角色图 e-->
                    <div class="compotext">金牛</div>
                </div>
            </div>
            <!--1 e-->
            <!--2 s-->
            <div class="compountwoBox heniubox" data-id="39">
                <div class="manzu" style="display:none">
                    <img src="/template/wap/default/Static/centImages/2019a071wf_2.png" alt="" class="rwebeijing">
                    <!--角色图 s-->
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_39.png" alt="" class="juesetu">
                    <!--角色图 e-->
                    <div class="compotext">木牛</div>
                </div>
                <div class="bumanzu">
                    <img src="/template/wap/default/Static/centImages/2019a071wf_2.png" alt="" class="rwebeijing">
                    <!--角色图 s-->
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_39_no.png" alt="" class="juesetu">
                    <!--角色图 e-->
                    <div class="compotext">木牛</div>
                </div>
            </div>
            <!--2 e-->
            <!--3 s-->
            <div class="compounthreeBox heniubox" data-id="40">
                    <div class="manzu" style="display:none">
                <img src="/template/wap/default/Static/centImages/2019a071wf_2.png" alt="" class="rwebeijing">
                <!--角色图 s-->
                <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_40.png" alt="" class="juesetu">
                <!--角色图 e-->
                <div class="compotext">水牛</div>
                </div>
                <div class="bumanzu">
                    
                <img src="/template/wap/default/Static/centImages/2019a071wf_2.png" alt="" class="rwebeijing">
                <!--角色图 s-->
                <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_40_no.png" alt="" class="juesetu">
                <!--角色图 e-->
                <div class="compotext">水牛</div>
                </div>
            </div>
            <!--3 e-->
            <!--4 s-->
            <div class="compounfourBox heniubox" data-id="41">
                    <div class="manzu" style="display:none">
                <img src="/template/wap/default/Static/centImages/2019a071wf_2.png" alt="" class="rwebeijing">
                <!--角色图 s-->
                <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_41.png" alt="" class="juesetu">
                <!--角色图 e-->
                <div class="compotext">火牛</div>
                </div>
                <div class="bumanzu">
                <img src="/template/wap/default/Static/centImages/2019a071wf_2.png" alt="" class="rwebeijing">
                <!--角色图 s-->
                <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_41_no.png" alt="" class="juesetu">
                <!--角色图 e-->
                <div class="compotext">火牛</div>
                </div>
            </div>
            <!--4 e-->
            <!--5 s-->
            <div class="compounfiveBox heniubox" data-id="42">
                <div class="manzu" style="display:none">
                    <img src="/template/wap/default/Static/centImages/2019a071wf_2.png" alt="" class="rwebeijing">
                    <!--角色图 s-->
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_42.png" alt="" class="juesetu">
                    <!--角色图 e-->
                    <div class="compotext">土牛</div>
                </div>
                <div class="bumanzu">
                    <img src="/template/wap/default/Static/centImages/2019a071wf_2.png" alt="" class="rwebeijing">
                    <!--角色图 s-->
                    <img src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_42_no.png" alt="" class="juesetu">
                    <!--角色图 e-->
                    <div class="compotext">土牛</div>
                </div>
            </div>
            <!--5 e-->
            <!--按钮 s-->
            <div class="comphchan">
                <!--条件未达成时 s-->
                 <div class="comphchandac tjwdd" style="display: inline-block;">
                     <img src="/template/wap/default/Static/centImages/2019a071wf_4_1.png" alt="" class="comphchandacbg comphchanbgaa">
                     <img src="/template/wap/default/Static/centImages/2019a071wf_3_1.png" alt="" class="comphchandactx">
                 </div>
                <!--条件未达成时 e-->
                <!--条件达成时 s-->
                <div class="comphchandac composite tjydd" style="display: none;">
                    <img src="/template/wap/default/Static/centImages/2019a071wf_4.png" alt="" class="comphchandacbg comphchanbgaa">
                    <img src="/template/wap/default/Static/centImages/2019a071wf_3.png" alt="" class="comphchandactx">
                </div>
                <!--条件达成时 e-->
            </div>
            <!--按钮 e-->
        </div>
    </div>
</div>
<!--五福合成弹窗 e-->



<!--离线收益弹窗    lixianshyupBoxActive        s-->
<div class="lixianshyupBox">
    <div class="homepsyiCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">离线收益</div>
            </div>
            <div class="homepauptzw btneffectansx lixianshyupqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="drawupimgzt"><img src="/template/wap/default/Static/centImages/2019a071_66.png" alt=""></div>
        <div class="drawupitj">+<span id="offline_income_money">0</span></div>
        <div class="homepsyibtn likeA" data-url="<?php echo url('Drumbeat/videoAd', ['type' => 'offline_income']); ?>"><img src="/template/wap/default/Static/centImages/2019a071_157.png" alt=""></div>
        <div class="homepsyimashu">每晚20点整重置视频次数（剩余<?php echo htmlentities($user['video_num']); ?>次）</div>
    </div>
</div>
<!--离线收益弹窗 e-->

<!--金币不足弹窗    jinbibzuupBoxActive        s-->
<div class="jinbibzuupBox">
    <div class="homepsyiCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">金币不足</div>
            </div>
            <div class="homepauptzw btneffectansx jinbibzuupqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="drawupimgzt"><img src="/template/wap/default/Static/centImages/2019a071_66.png" alt=""></div>
        <div class="drawupitj">+<span class="buy_max_product_price"></span></div>
        <div class="homepsyibtn likeA" data-url="<?php echo url('Drumbeat/videoAd', ['type' => 'coin']); ?>"><img src="/template/wap/default/Static/centImages/2019a071_156.png" alt=""></div>
        <div class="homepsyimashu">每晚20点整重置视频次数（剩余<?php echo htmlentities($user['video_num']); ?>次）</div>
    </div>
</div>
<!--金币不足弹窗 e-->

<?php if($user['last_video_income']): ?>
<!--恭喜获得金币弹窗   gxhdjbupBoxActive        s-->
<div class="gxhdjbupBox gxhdjbupBoxActive">
    <div class="homepsyiCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">恭喜获得金币</div>
            </div>
            <div class="homepauptzw btneffectansx gxhdjbupqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="drawupimgzt"><img src="/template/wap/default/Static/centImages/2019a071_66.png" alt=""></div>
        <div class="drawupitj">+<span class=""><?php echo moneyTransformation($user['last_video_income']); ?></span></div>
        <div class="drawupiqd gxhdjbupqxqr btneffectansx">确定</div>
    </div>
</div>
<!--恭喜获得金币弹窗 e-->
<?php endif; ?>


<!--获得免费宝箱弹窗   hdmfbxupBoxActive        s-->
<div class="hdmfbxupBox">
    <div class="homepsyiCont">
        <div class="homepauptleHe df">
            <div class="homepauptzw"></div>
            <div class="homepauptle fx1">
                <div class="homepauptletx">获得免费宝箱</div>
            </div>
            <div class="homepauptzw btneffectansx hdmfbxupqx"><img src="/template/wap/default/Static/centImages/2019a071_64.png" alt=""></div>
        </div>
        <div class="drawupimgzt"><img src="/template/wap/default/Static/centImages/2019a071_84.png" alt=""></div>
        <div class="drawupitj">+<span class="buy_max_product_price">534</span></div>
        <div class="homepsyibtn likeA" data-url="<?php echo url('Drumbeat/videoAd', ['type' => 'coin']); ?>"><img src="/template/wap/default/Static/centImages/2019a071_156.png" alt=""></div>
        <div class="homepsyimashu">每晚20点整重置视频次数（剩余<?php echo htmlentities($user['video_num']); ?>次）</div>
    </div>
</div>
<!--获得免费宝箱弹窗 e-->




<audio src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/web-mp/add_mp.m4a" controls="controls" preload id="online_income_music" hidden>
</audio>
<audio src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/web-mp/close_mp.m4a" controls="controls" preload id="merge_close_music" hidden>
</audio>
<audio src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/web-mp/shop_mp.m4a" controls="controls" preload id="buy_shop_music" hidden>
</audio>
<audio src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/web-mp/five.m4a" controls="controls" preload id="wufu_composite_music" hidden>
</audio>
<audio src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/web-mp/thirty-seven.m4a" controls="controls" preload id="merge37_music" hidden>
</audio>

<script>
var flage=false;
var productData = <?php echo json_encode($productList, JSON_UNESCAPED_UNICODE); ?>;
var div = document.getElementsByClassName('mrythudHe');
var ljt = document.getElementsByClassName('mrybtmhuis'); 
var box_top=document.getElementsByClassName('huangdquyeBox')[0].offsetTop;
var qipao_=document.getElementsByClassName('qipao');

var ljt_obj = {
        w: ljt[0].offsetHeight,
        h: ljt[0].offsetWidth,
        x: ljt[0].offsetLeft,
        y: ljt[0].offsetTop,
        id: ljt[0].dataset.id,
    };
setTimeout(function () {
    init();
    get_address();
}, 1000);

//获取气泡位置信息
function get_address(){
    qipao_arr=[];
    for (var i = 0; i < qipao_.length; i++) {
      var obj = {
          h: qipao_[i].offsetHeight,
          w: qipao_[i].offsetWidth,
          x: qipao_[i].offsetLeft,
          y: qipao_[i].offsetTop,
      };
      qipao_arr.push(obj);
    }
}
//数据初始化函数
function init(){
    flage=false;
   $(".mrythudHe").unbind();
    arr = [];//存放每个宠物的位置信息  ID==>自身唯一标识符,num判断碰撞检测时避免与自身发生碰撞
    for (var i = 0; i < div.length; i++) {
        var obj = {
            h: div[i].offsetHeight,
            w: div[i].offsetWidth,
            x: div[i].offsetLeft,
            y: div[i].offsetTop,
            class:div[i].classList[1],
            id: div[i].dataset.id,
            infoid: div[i].dataset.infoid,
            num: i,
        };
        div[i].dataset.num = i;
        arr.push(obj);
        drag(div[i]);
    }
}

function drag(div1) {
    //限制最大宽高，不让滑块出去
    var maxW = document.body.clientWidth - div1.offsetWidth;
    var maxH = document.documentElement.clientHeight - div1.offsetHeight;
    var oL = 0;
    var oT = 0;
    //手指触摸开始，记录div的初始位置
    div1.addEventListener('touchstart', function (e) {
        $(".mask").show();
        flage=false;
        var ev = e || window.event;
        var touch = ev.targetTouches[0];

        oL = touch.clientX - div1.offsetLeft;
        oT = touch.clientY - div1.offsetTop;
        L = touch.clientX - div1.offsetWidth/2;
        T = touch.clientY - div1.offsetHeight-box_top;
      
        div1.style.left = L + 'px';
        div1.style.top = T + 'px';
        div1.style.zIndex =9999;
            document.addEventListener('touchmove', defaultEvent, false);
            flage_1=true;
    });
    //触摸中的，位置记录
    div1.addEventListener('touchmove', function (e) {
        var ev = e || window.event;
        var touch = ev.targetTouches[0];
        var oLeft = touch.clientX - oL;
        var oTop = touch.clientY - oT;
        if (oLeft < 0) {
            oLeft = 0;
        } else if (oLeft >= maxW) {
            oLeft = maxW;
        }
        if (oTop < 0) {
            oTop = 0;
        } else if (oTop >= maxH) {
            oTop = maxH;
        }
        div1.style.left = oLeft + 'px';
        div1.style.top = oTop + 'px';
        //checkPos
    });
    //触摸结束时的处理
    div1.addEventListener('touchend', chexk_);
    function chexk_(){
        if(flage){
        return;
    }
    var left=div1.offsetLeft;
        var top=div1.offsetTop;
        console.log(left,top)
        if(left>ljt_obj.x-80&&top>=(ljt_obj.y-box_top-ljt_obj.h)-50){
            if(div1.dataset.id != 45) {
                showRecycleBin(div1);
            }
             // alert("进入垃圾桶")
        }
        //触摸结束进行碰撞检测
        checkPos(left, top, div1);
        init();
        document.removeEventListener('touchmove', defaultEvent);
        for (var i = 0; i < div.length; i++) {
            div[i].removeEventListener("touchend",chexk_,true)
        }
        div1.style.zIndex =8000;
        $(".mask").hide();
       flage=true;
        // init();
    }
}


//阻止默认事件
function defaultEvent(e) {
    e.preventDefault();
}
//碰撞检测
function checkPos(x, y,div1) {  
    var w = div1.offsetWidth;
    var h = div1.offsetHeight;
    var id = div1.dataset.id;
    var num = div1.dataset.num;
    //处理一个宠物移动问题
    if(arr.length<=1){
        qipao_arr.forEach((obj_, index_) => {
            if (Math.abs(obj_.x - x) < obj_.w /2  + w / 2 && Math.abs(obj_.y - y) < obj_.h / 2 + h / 2) {
            //    console.log(document.getElementsByClassName(`mrythudHe${index_+1}`)[0])
               if(document.getElementsByClassName(`mrythudHe${index_+1}`).length!=1){
                div1.classList.remove(div1.classList[1])
                movePosition({
                    type:2,
                    infoid:div1.dataset.infoid,
                    position:index_+1
                });
                div1.style.removeProperty("left");
                div1.style.removeProperty("top");
                div1.classList.add(`mrythudHe${index_+1}`)
            //     document.getElementsByClassName(`mrythudHe${index_+1}`)[0].style.left=div1.offsetLeft+"px";
            //     document.getElementsByClassName(`mrythudHe${index_+1}`)[0].style.top=div1.offsetTop+"px";
            //     div1.style.left=document.getElementsByClassName(`mrythudHe${index_+1}`)[0].offsetLeft+"px";
            //     div1.style.top=document.getElementsByClassName(`mrythudHe${index_+1}`)[0].offsetTop+"px";
            // //     if(div1.classList[1]!="undefined"){
            // //     console.log(div1.classList[1]!="undefined")
            // //   document.getElementsByClassName(`mrythudHe${index_+1}`)[0].classList.remove( document.getElementsByClassName(`mrythudHe${index_+1}`)[0].classList[1]);
            // //    console.log(div1.classList[1])
            // //     document.getElementsByClassName(`mrythudHe${index_+1}`)[0].classList.add(div1.classList[1]);
            // //     }
                }
            } else {
                //不是相同的返回原位
                div1.style.removeProperty("left");
                div1.style.removeProperty("top");
                // div1.style.left = arr[num].x + "px";
                // div1.style.top = arr[num].y + "px";
            }
        });
    } try {
    arr.forEach((obj, index) => {
        if (obj.num == num) {
        //自身碰撞不做任何处理
        } else {
            if (Math.abs(obj.x - x) < obj.w / 2 + w / 2 && Math.abs(obj.y - y) < obj.h / 4 + h / 4) {
                // console.log(id, arr[index].id)
              
                if (id == arr[index].id) {
                    
                    let nextProductId = parseInt(id)+1;
                    // let nextProductInfo = productData[nextProductId];

                    if(nextProductId == 38) {
                        $('.huodrandoBox').addClass('huodrandoBoxActive');
                        $("#raffle a").attr('data-infoid1', div1.dataset.infoid);
                        $("#raffle a").attr('data-infoid2', obj.infoid);
                    } else {
                        if(nextProductId == 46) {
                        // div[index].style.left=arr[num].x+"px";
                        // div[index].style.top=arr[num].y+"px";
                        // div1.style.left=arr[index].x+"px";
                        // div1.style.top=arr[index].y+"px";
                        div[index].classList.remove(div[index].classList[1])
                      div[index].classList.add( arr[num].class)
                      div1.classList.remove(div1.classList[1])
                      div1.classList.add(arr[index].class)
                            return ;
                        }
                        //两个宠物ID相同   在这里替换图片 并初始化 init
                        // div[index].children[0].children[0].src=nextProductInfo.picture;
                        // div[index].children[0].children[0].src='/template/wap/default/Static/centImages/v_'+nextProductId+'.png';
                        mergeProduct(div[index], div1, div1.dataset.infoid, obj.infoid);
                        throw new Error("");
                    }
                    throw new Error("");
                } else {
                try {
                    if((id == 43 || id == 44) && (arr[index].id == 43 || arr[index].id == 44)) {
                        mergeProduct(div[index], div1, div1.dataset.infoid, obj.infoid);
                        return'';
                    }
                    if((id >= 38 && id <= 42) && (arr[index].id >= 38 && arr[index].id <= 42)) {
                    
                    showWufuCompositePage();
                  
                    // div1.style.left = arr[num].x + "px";
                    //  div1.style.top = arr[num].y + "px";
                     div1.classList.remove(div1.classList[1])
                      div1.classList.add(arr[num].class)
                     throw new Error("");
                }  
                } catch (error) {
                    
                }
                    //不是相同的交换位置
                    //   console.log(index,num);
                      div1.style.removeProperty("left");
                    div1.style.removeProperty("top");
                               
                    movePosition({
                        type:1,
                        infoid1:div[index].dataset.infoid,
                        infoid2:div1.dataset.infoid
                    });
                    // console.log('aaa',arr[num].class);
                    // console.log(div[index].dataset.id,div1.dataset.id);
                      div[index].classList.remove(div[index].classList[1])
                      div[index].classList.add( arr[num].class)
                      div1.classList.remove(div1.classList[1])
                      div1.classList.add(arr[index].class)
                    //    div[index].style.left=arr[num].x+"px";
                    //    div[index].style.top=arr[num].y+"px";
                    //    div1.style.left=arr[index].x+"px";
                    //    div1.style.top=arr[index].y+"px";
                  
                       throw new Error("");
                       
                    // div1.style.left=arr[num].x+"px";
                    // div1.style.top=arr[num].y+"px";
                   
                }
            } else {
                try {
                    qipao_arr.forEach((obj_, index_) => {
                            if (Math.abs(obj_.x - x-30) < obj_.w /2  + w / 2 && Math.abs(obj_.y - y-60) < obj_.h / 2 + h / 2) {
                            //    console.log(document.getElementsByClassName(`mrythudHe${index_+1}`)[0])
                               if(document.getElementsByClassName(`mrythudHe${index_+1}`).length!=1){
                                div1.classList.remove(div1.classList[1])
                                
                                console.log('进来了');
                                movePosition({
                                    type:2,
                                    infoid:div1.dataset.infoid,
                                    position:index_+1
                                });
                                div1.style.removeProperty("left");
                                div1.style.removeProperty("top");
                                div1.classList.add(`mrythudHe${index_+1}`)
                                throw new Error("");
                            //     document.getElementsByClassName(`mrythudHe${index_+1}`)[0].style.left=div1.offsetLeft+"px";
                            //     document.getElementsByClassName(`mrythudHe${index_+1}`)[0].style.top=div1.offsetTop+"px";
                            //     div1.style.left=document.getElementsByClassName(`mrythudHe${index_+1}`)[0].offsetLeft+"px";
                            //     div1.style.top=document.getElementsByClassName(`mrythudHe${index_+1}`)[0].offsetTop+"px";
                            // //     if(div1.classList[1]!="undefined"){
                            // //     console.log(div1.classList[1]!="undefined")
                            // //   document.getElementsByClassName(`mrythudHe${index_+1}`)[0].classList.remove( document.getElementsByClassName(`mrythudHe${index_+1}`)[0].classList[1]);
                            // //    console.log(div1.classList[1])
                            // //     document.getElementsByClassName(`mrythudHe${index_+1}`)[0].classList.add(div1.classList[1]);
                            // //     }
                               
                                }
                      
                                throw new Error("");
                            
                            } else {
                                // console.log(index, num);
                                //不是相同的返回原位
                              
                                div1.style.removeProperty("left");
                                div1.style.removeProperty("top");
                               
                                // div1.style.left = arr[num].x + "px";
                                // div1.style.top = arr[num].y + "px";
                            }
                        })
                } catch (error) {
                    
                }
                }
        }
    })
}catch (error) {
                          
                        }
  
}

function movePosition(params) {
    $.ajax({
        type: 'post',
        url: '<?php echo U("Product/movePosition"); ?>',
        data: params,
        dataType: 'json',
        success: function (res) {
            console.log(res);
        }
    });
}

function mergeProduct(div,div1, infoid1, infoid2) {
    $.ajax({
        type: 'post',
        url: '<?php echo U("Product/mergeProduct"); ?>',
        data: {infoid1: infoid1, infoid2: infoid2},
        dataType: 'json',
        success: function (res) {
            mergeCloseAudio.play();
            if(res.code == 1) {
                if(res.data.productInfo.reward_red_envelope > 0) {
                    $('.mergeredenvpepop_amount').html(res.data.productInfo.reward_red_envelope);
                    $('.mergeredenvpepopBox').addClass('redenvpepopBoxActive');
                }
                if(res.data.rewardId > 0) {
                    $('.upgraderedenvpepopBox').addClass('redenvpepopBoxActive');
                }
                if(res.data.productInfo.id < 38) {
                    div.children[0].children[0].src='https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_'+res.data.productInfo.id+'.png';
                    div.dataset.id = res.data.productInfo.id;
                    div1.remove();//移除被移动的宠物
                    div.dataset.infoid = res.data.infoid;
                    if(div.children[0].children[3]) {
                        div.children[0].children[2].innerHTML=res.data.productInfo.number;
                        div.children[0].children[3].innerHTML=res.data.productInfo.show_income;
                        div.children[0].children[3].dataset.income=res.data.productInfo.income;
                    } else {
                        div.children[0].children[1].innerHTML=res.data.productInfo.number;
                        div.children[0].children[2].innerHTML=res.data.productInfo.show_income;
                        div.children[0].children[2].dataset.income=res.data.productInfo.income;
                    }
                    getUserMaxProductData();
                    getUserBuyMaxProductData();
                    checkIsWufuComposite();
                } else {
                    div1.remove();//移除被移动的宠物
                    div.remove();//移除被移动的宠物
                }
                init();
            } else {
                init();
                return mui.toast(res.msg, {duration: '2000', type: 'div'});    
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
        }
    });
}
/**
 * 领取红包
 */
function receivingARedEnvelope() {
    $.ajax({
        type: 'post',
        url: '<?php echo U("User/receivingARedEnvelope"); ?>',
        data: {type: 'upgrade'},
        dataType: 'json',
        success: function (res) {
            $('.upgraderedenvpepopBox').removeClass('redenvpepopBoxActive');
            $('.upgraderedenvpepoplin').find('.redenvplinanbg').removeClass('otationsz');
            if(res.code == 1) {
                mui.alert('成功领取到'+res.data.money,'温馨提示');
            } else {
                return mui.toast(res.msg, {duration: '2000', type: 'div'});
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
        }
    });
}

function showRecycleBin(div) {
    let productInfo = productData[div.dataset.id];
    $('#huishou_money').html(productInfo.recovery_amount);
    $('.huishouupBox').addClass('huishouupBoxActive');
    $('.huishouupcr').attr('data-id', div.dataset.infoid);
}

function deleteProduct(id) {
    $.ajax({
        type: 'post',
        url: '<?php echo U("Product/deleteProduct"); ?>',
        data: {id: id},
        dataType: 'json',
        success: function (res) {
            if(res.code == 1) {
                mergeCloseAudio.play();
                $('.huishouupBox').removeClass('huishouupBoxActive');
                getUserProductData();
                goldChange($('#huishou_money').html());
            } else {
                return mui.toast(res.msg, {duration: '2000', type: 'div'});
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
        }
    });
}
</script>
<!--设置弹窗 e-->





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

<script src="/template/wap/default/Static/turn/js/turntable.js?v=1.11"></script>
<script src="/template/wap/default/Static/js/jquery.arctext.js" type="text/javascript" charset="utf-8"></script>


<script>
    mui('body').on('tap', '.mregenvpCodacacaan', function() {
        $('.mergeredenvpepopBox').removeClass('redenvpepopBoxActive');
    });
    mui('body').on('tap', '.upgradenvpCodacacaan', function() {
        $('.upgraderedenvpepopBox').removeClass('redenvpepopBoxActive');
    });
    mui('body').on('tap', '.upgraderedenvpepoplin', function() {
        $(this).find('.redenvplinanbg').addClass('otationsz');
        receivingARedEnvelope();
    });
    // 扇形标题 s
    $(function () {
        $(".homepauptletx").arctext({
            radius: 170
        });
        onlineIncomeAudio = document.getElementById('online_income_music');
        mergeCloseAudio = document.getElementById('merge_close_music');
        buyShopAudio = document.getElementById('buy_shop_music');
        wufuCompositeAudio = document.getElementById('wufu_composite_music');
        merge37Audio = document.getElementById('merge37_music');
    });
    // 扇形标题 e

    // 实名制跳转
    mui('body').on('tap', ".mryzhjinshu", function () {
        window.location.href = '<?php echo url("User/realnameauth"); ?>';
    });
    // 实名制跳转

    // 邀请劵
    mui('body').on('tap', ".yaoqingjuan", function () {
        window.location.href = '<?php echo url("Team/tjrfriendadd"); ?>';
    });
    // 邀请劵

    // 我的宠物弹窗 s
    mui('body').on('tap', ".homepaupbtn", function () {
        $('.homepaupowBox').addClass('homepaupowBoxActive');
    });
    mui('body').on('tap', ".homepachadan", function () {
        $('.homepaupowBox').removeClass('homepaupowBoxActive');
    });
    // 我的宠物弹窗 e

    // 加速弹窗 s
    mui('body').on('tap', ".expediteupan", function () {
        $('.expediteupBox').addClass('expediteupBoxActive');
    });
    mui('body').on('tap', ".expediteupqx", function () {
        $('.expediteupBox').removeClass('expediteupBoxActive');
    });
    // 加速弹窗 e

    //加速60秒提示
    mui('body').on('tap', ".js60", function () {
        return mui.toast('暂未开放');
        //window.location.href = 'http://nqct.xmlianke.top/drumbeat/ad';
    });
    //加速60秒提示

    //视频次数用完提示
    mui('body').on('tap', ".videos", function () {
        return mui.toast('今日广告次数已用完');
    });
    //视频次数用完提示

    // 领取弹窗 s
    /*mui('body').on('tap', ".drawulkbtn", function () {
        $('.drawupBox').addClass('drawupBoxActive');
    });*/
    mui('body').on('tap', ".drawupqx", function () {
        $('.drawupBox').removeClass('drawupBoxActive');
    });
    var rewardCountdownmaxtime = 0; //
    function CountDown() {
        if (rewardCountdownmaxtime > 0) {
            $('#reward_stay_receive_status').hide();
            $('#reward_countdown_status').show();
            minutes = Math.floor(rewardCountdownmaxtime / 60);
            seconds = Math.floor(rewardCountdownmaxtime % 60);
            if(minutes <10) {
                minutes = '0'+minutes;
            }
            if(seconds <10) {
                seconds = '0'+seconds;
            }
            msg = minutes + ":" + seconds;
            $('#reward_countdown_info').html(msg);
            --rewardCountdownmaxtime;
        } else{
            $('#reward_stay_receive_status').show();
            $('#reward_countdown_status').hide();
            clearInterval(rewardCountdowntimer);
        }
    }
    mui('body').on('tap', '.receiveAward', function() {
        console.log('点击了领取');
        ws_send_content({
            "type": "receive_reward_money"
        });
    });
    // 领取弹窗 e
    // 排行榜弹窗 s
    mui('body').on('tap', ".rankinubtn", function () {
        getLeaderboardData();
    });
    mui('body').on('tap', ".rankinupqx", function () {
        $('.rankinupBox').removeClass('rankinupBoxActive');
    });
    // 排行榜弹窗 e
    // 商店弹窗 s
    mui('body').on('tap', ".storeupbtn", function () {
        getStoreProductData();
    });
    mui('body').on('tap', ".storeupqx", function () {
        $('.storeupBox').removeClass('storeupBoxActive');
    });
    // 商店弹窗 e
    // 图鉴弹窗 s
    mui('body').on('tap', ".ustratupbtn", function () {
        $('.ustratupBox').addClass('ustratupBoxActive');
    });
    mui('body').on('tap', ".ustratupqx", function () {
        $('.ustratupBox').removeClass('ustratupBoxActive');
    });
    // 图鉴弹窗 e


    // 回收弹窗 s
    /*mui('body').on('tap', ".huishouupbtn", function () {
        $('.huishouupBox').addClass('huishouupBoxActive');
    });*/
    mui('body').on('tap', ".huishouupqx", function () {
        $('.huishouupBox').removeClass('huishouupBoxActive');
    });
    mui('body').on('tap', ".huishouupcr", function () {
        deleteProduct($(this).attr('data-id'));
    });
    // 回收弹窗 e
    // 离线收益弹窗 s
    mui('body').on('tap', ".offlineincomeqx", function () {
        $('.offlineincomeBox').hide();
    });
    // 离线收益弹窗 e

//     <audio src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/web-mp/add_mp.m4a" controls="controls" preload id="online_income_music" hidden>
// </audio>
// <audio src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/web-mp/close_mp.m4a" controls="controls" preload id="merge_close_music" hidden>
// </audio>
// <audio src="https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/web-mp/shop_mp.m4a" controls="controls" preload id="buy_shop_music" hidden>
// </audio>

    // 设置弹窗 s
 
   if(window.localStorage.getItem("music")==1){
    $("#online_income_music")[0].volume=1;
    $("#merge_close_music")[0].volume=1;
    $("#buy_shop_music")[0].volume=1;
    $("#music_2").hide();
    $("#music_1").show();
   }

   if(window.localStorage.getItem("music")==0){
    $("#online_income_music")[0].volume=0;
    $("#merge_close_music")[0].volume=0;
    $("#buy_shop_music")[0].volume=0;
    $("#music_2").show();
    $("#music_1").hide();
   }

    mui('body').on('tap', ".settnsLsztan", function () {
        if ($(this).find('.img_dk').css('display') == 'none') {
   
            if($(this)[0].dataset.music==1){
            
             $("#online_income_music")[0].volume=0;
             $("#merge_close_music")[0].volume=0;
             $("#buy_shop_music")[0].volume=0;

             window.localStorage.setItem("music",0)
            }
            $(this).find('.img_gb').hide();
            $(this).find('.img_dk').show();
        } else {
            if($(this)[0].dataset.music==1){
             $("#online_income_music")[0].volume=1;
             $("#merge_close_music")[0].volume=1;
             $("#buy_shop_music")[0].volume=1;
             window.localStorage.setItem("music",1)
            }
            $(this).find('.img_gb').show();
            $(this).find('.img_dk').hide();
        }
    });

    mui('body').on('tap', ".settnszupbtn", function () {
        $('.settnszBox').addClass('settnszBoxActive');
    });
    mui('body').on('tap', ".settnupqx", function () {
        $('.settnszBox').removeClass('settnszBoxActive');
    });
    // 设置弹窗 e


    //随机合成 s

    var raffltwidth = $('#raffle table td').width();
    $('#raffle table td').height(raffltwidth);

    // mui('body').on('tap', ".huodrandobtn", function () {
    //     $('.huodrandoBox').addClass('huodrandoBoxActive');
    // });
    mui('body').on('tap', ".huodrandoqx", function () {
        $('.huodrandoBox').removeClass('huodrandoBoxActive');
    });

    mui('body').on('tap', ".husuijihdupqx", function () {
        $('.husuijihdupBox').removeClass('husuijihdupBoxActive');
    });



    var raffle={
        index:-1,	//当前转动到哪个位置，起点位置
        count:0,	//总共有多少个位置
        timer:0,	//setTimeout的ID，用clearTimeout清除
        speed:100,	//初始转动速度
        times:0,	//转动次数
        cycle:150,	//转动基本次数：即至少需要转动多少次再进入抽奖环节
        prize:-1,	//中奖位置
        init:function(id){
            if ($("#"+id).find(".raffle-unit").length>0) {
                $raffle = $("#"+id);
                $units = $raffle.find(".raffle-unit");
                this.obj = $raffle;
                this.count = $units.length;
                $raffle.find(".raffle-unit-"+this.index).addClass("niuactive");
            }
        },
        roll:function(){
            var index = this.index;
            var count = this.count;
            var raffle = this.obj;
            $(raffle).find(".raffle-unit-"+index).removeClass("niuactive");
            index += 1;
            if (index>count-1) {
                index = 0;
            }
            $(raffle).find(".raffle-unit-"+index).addClass("niuactive");
            this.index=index;
            return false;
        },
        stop:function(index){
            this.prize=index;
            return false;
        }
    };

    function roll(infoid1, infoid2){
        raffle.times += 1;
        raffle.roll();
        if (raffle.times > raffle.cycle+10 && raffle.prize==raffle.index) {
            clearTimeout(raffle.timer);
            raffle.prize=-1;
            raffle.times=0;
            click=false;

            //关闭随机合成并 弹出 获取牛
            var aiyas = $('.niuactive').find('.aiyasxbtx').html();
            var aiyasimg = $('.niuactive').find('img').attr('src');
            $('.showImg').attr('src',aiyasimg);
            $('.huodrandoBox').removeClass('huodrandoBoxActive');
            $('.husuijihdupBox').addClass('husuijihdupBoxActive');
            $('#aiyasxbtshuz').html(aiyas);
            getUserProductData();

        }else{
            if (raffle.times<raffle.cycle) {
                raffle.speed -= 10;
            }else if(raffle.times==raffle.cycle) {
                var index = Math.random()*(raffle.count)|0;
                raffle.prize = -1;
                $.ajax({
                    type: 'post',
                    url: '<?php echo U("Product/randomExtractionProduct"); ?>',
                    data: {infoid1: infoid1, infoid2: infoid2},
                    success: function (res) {
                        // merge37Audio.pause();
                        console.log(res);
                        if(res.code == 1) {
                            raffle.prize = res.data.key;
                        } else {
                            return mui.toast(res.msg, {duration: '2000', type: 'div'});
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
                    }
                });
            }else{
                if (raffle.times > raffle.cycle+10 && ((raffle.prize==0 && raffle.index==7) || raffle.prize==raffle.index+1)) {
                    raffle.speed += 110;
                }else{
                    raffle.speed += 20;
                }
            }
            if (raffle.speed<40) {
                raffle.speed=40;
            }
            //console.log(raffle.times+'^^^^^^'+raffle.speed+'^^^^^^^'+raffle.prize);
            // raffle.timer = setTimeout(roll,raffle.speed);
            raffle.timer = setTimeout("roll("+infoid1+","+infoid2+")",raffle.speed);

        }
        return false;
    }

    var click=false;

    window.onload=function(){
        raffle.init('raffle');
        $("#raffle a").click(function(){
            if (click) {
                return false;
            }else{
                raffle.speed=100;
                merge37Audio.play();
                roll($(this).attr('data-infoid1'), $(this).attr('data-infoid2'));
                click=true;
                return false;

            }
        });
    };




    //随机合成 e



    //设置盒子高度
    var mrythudwid = $('.mrythud').width() / 0.76;
    $('.mrythud').height(mrythudwid);


    // 幸运转盘 s
    mui('body').on('tap', '.lotteuptcan', function () {
        $('.lotterynrBox').addClass('lotterynrBoxActive');
    });
    mui('body').on('tap', '.lotteuptqx', function () {
        $('.lotterynrBox').removeClass('lotterynrBoxActive');
    });

    mui('body').on('tap', '.homepaupqx', function () {
        $('.huodelotupBox').removeClass('huodelotupBoxActive');
    });

    mui('body').on('tap', '#lotterywal', function () {
        mui.toast('券已使用完',{ duration:'long', type:'div' })
    });

    var wheelSurf;
    // 初始化装盘数据 正常情况下应该由后台返回
    var initData = {
        "success": true,
        "list": <?php echo $prizeArr ?>
    };
    var list = {};

    var angel = 360 / initData.list.length;
    // 格式化成插件需要的奖品列表格式
    for (var i = 0, l = initData.list.length; i < l; i++) {
        list[initData.list[i].id] = {
            id: initData.list[i].id,
            name: initData.list[i].name,
            image: initData.list[i].image
        }
    }
    // 查看奖品列表格式

    // 定义转盘奖品
    wheelSurf = $('#myCanvas').WheelSurf({
        list: list, // 奖品 列表，(必填)
        outerCircle: {
            color: '#885907' // 外圈颜色(可选)
        },
        innerCircle: {
            color: '#A0F78B' // 里圈颜色(可选)
        },
        dots: ['#FAFFFE', '#FFD700'], // 装饰点颜色(可选)
        disk: ['#0BCFC8', '#E0D16A', '#0BCFC8', '#E0D16A', '#0BCFC8', '#E0D16A', '#0BCFC8', '#E0D16A'], //中心奖盘的颜色，默认7彩(可选)
        title: {
            color: '#000000',
            // font: '19px Arial'
        } // 奖品标题样式(可选)
    });

    // 初始化转盘
    wheelSurf.init();
    // 劵数量
    var lottery_num = parseInt($('.lotterydizshu').text().match(/\d+/));
    // 抽奖
    var throttle = true;
    mui('body').on('tap', '#lotteryxzan', function () {
        if (!throttle) {
            return false;
        }
        throttle = false;
        $("#message").html('抽奖中...');
        $.ajax({
            type: 'post',
            url: '<?php echo U("Turn/turnIndex"); ?>',
            dataType: 'json',
            success: function (res) {
                if (res.code == 1) {
                    // 按效果 s
                    if($('#lotteryxzan').hasClass('.lotteryxzan') == false){
                        $('#lotteryxzan').addClass('lotteryxzan');
                    };
                    setTimeout(function () {
                        $('#lotteryxzan').find('.xsimg').css('display' ,'none');
                        $('#lotteryxzan').find('.ycimg').css('display' ,'inline-block');
                    },150);
                    setTimeout(function () {
                        $('#lotteryxzan').removeClass('lotteryxzan');
                        $('#lotteryxzan').find('.xsimg').css('display' ,'inline-block');
                        $('#lotteryxzan').find('.ycimg').css('display' ,'none');
                    },4500);
                    // 按效果 e

                    var count = 0;
                    // 计算奖品角度
                    for (const key in list) {
                        if (list.hasOwnProperty(key)) {
                            if (res.msg.id == list[key].id) {
                                break;
                            }
                            count++
                        }
                    }

                    // 转盘抽奖，
                    wheelSurf.lottery((count * angel + angel / 2), function () {
                        $("#message").html(res.msg.message);
                        throttle = true;
                        //打开获得金币弹窗
                        $('.huodelotupBox').addClass('huodelotupBoxActive');
                    });
                    if (lottery_num != 0){
                        lottery_num--;
                        $('.lotterydizshu').html('转盘卷 ' + lottery_num);
                    }
                } else {
                    throttle = true;
                    if (res.code == -1) {
                        $("#message").html(res.msg);
                        return mui.toast(res.msg);
                    }
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
            }
        });
    });

    // 幸运转盘 e


    /**
     * 获取商店产品信息
     */
    function getStoreProductData() {
        $.ajax({
            type: 'get',
            url: '<?php echo U("Product/getProductList"); ?>',
            success: function (res) {
                $('#product_content').html(res);
                $('.storeupBox').addClass('storeupBoxActive');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
            }
        });
    }

    /**
     * 获取排行榜信息
     */
    function getLeaderboardData() {
        $.ajax({
            type: 'get',
            url: '<?php echo U("Product/getLeaderboardList"); ?>',
            success: function (res) {
                $('#leaderboard_content').html(res);
                $('.rankinupBox').addClass('rankinupBoxActive');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
            }
        });
    }

    /**
     * 获取会员产品
     */
    function getUserProductData() {
        $.ajax({
            type: 'get',
            url: '<?php echo U("Product/getUserProductList"); ?>',
            success: function (res) {
                $('#user_product_content').html(res);
                add_();
                init();
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
            }
        });
    }
    /**
     * 获取会员最大产品信息
     */
    function getUserMaxProductData() {
        $.ajax({
            type: 'get',
            url: '<?php echo U("Product/getUserMaxProductInfo"); ?>',
            success: function (res) {
                if(res.productInfo) {
                    $('.user_max_product_name').html(res.productInfo.product_name);
                    $('.user_max_product_number').html(res.productInfo.number);
                    $('.user_max_product_income').html(res.productInfo.income);
                    $('.user_max_product_offline_income').html(res.productInfo.offline_income);
                    $('.user_max_product_picture').attr('src', 'https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_'+res.productInfo.id+'.png');
                    // $('.max_product_id').attr('data-id', res.productInfo.id);
                    // $('.max_product_id').attr('data-amount', res.productInfo.amount);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
            }
        });
    }
    /**
     * 获取会员最大能购买产品信息
     */
    function getUserBuyMaxProductData() {
        $.ajax({
            type: 'get',
            url: '<?php echo U("Product/getUserBuyMaxProductInfo"); ?>',
            success: function (res) {
                if(res.productInfo) {
                    $('.buy_max_product_picture').attr('src', 'https://2019-a071-img.oss-cn-shenzhen.aliyuncs.com/2019-a071-niuB/product-logo/v_'+res.productInfo.id+'.png');
                    $('.buy_max_product_price').html(res.productInfo.show_amount);
                    $('.buy_max_product_name').html('LV' + res.productInfo.number + res.productInfo.product_name);
                    $('.buy_max_product_id').attr('data-id', res.productInfo.id);
                    $('.buy_max_product_id').attr('data-amount', res.productInfo.amount);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
            }
        });
    }
    getUserProductData();
    getUserMaxProductData();
    getUserBuyMaxProductData();

    //增加金币
      function add_(){
        $(".maopap_1").on("animationiteration",function(){
          // console.log(parseInt($(this).html())+parseInt( $(".user-money-two").html()));
          // $(".user-money-two").html(parseInt($(this).html())+parseInt( $(".user-money-two").html()));
            goldChange(parseInt($(this).data('income')));
            onlineIncomeAudio.play();
            $('.mryzhdbzi').css({
                'animation':'mryzhdblink 5s 0s linear infinite',
                '-webkit-animation':'mryzhdblink 5s 0s linear infinite'
            });
            $('.img').css({
                'animation':'mryzhdblink 5s 0s linear infinite',
                '-webkit-animation':'mryzhdblink 5s 0s linear infinite'
            });
            setTimeout(function(){
                $('.mryzhdbzi').css({
                    'animation':'none',
                    '-webkit-animation':'none'
                });
                $('.img').css({
                    'animation':'none',
                    '-webkit-animation':'none'
                });
            },3000);
        ws_send_content({
            "type": "online_income",
            "money": parseInt($(this).text())
        });

      })
      }

    /**
     * 金币变化
     */
    function goldChange(amount) {
        $(".user-money-two").attr('data-money', parseInt( $(".user-money-two").attr('data-money'))+parseInt(amount));
        $('.user-money-two').html(moneyTransformation(parseInt( $(".user-money-two").attr('data-money'))+parseInt(amount)));
    }
      
     
    

    mui('body').on('tap', '.buyProduct', function() {
        var obj = $(this);
        let id = obj.attr('data-id'),amount = obj.attr('data-amount');

        $.ajax({
            type: 'post',
            url: '<?php echo U("Product/buyProduct"); ?>',
            data: {id: id},
            dataType: 'json',
            success: function (res) {
                if(res.code == 1) {
                    getUserProductData();
                    if(obj.data('is_show_list') != 2) {
                        getStoreProductData();
                    }
                    buyShopAudio.play();
                    goldChange('-'+amount);
                    getUserMaxProductData();
                    getUserBuyMaxProductData();
                } else {
                    if(res.code == -8) {
                        $('.jinbibzuupBox').addClass('jinbibzuupBoxActive');
                    }
                    return mui.toast(res.msg, {duration: '2000', type: 'div'});
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
            }
        });
    });

    mui('body').on('tap', '.composite', function() {
        var obj = $(this);

        $.ajax({
            type: 'post',
            url: '<?php echo U("Product/wufuComposite"); ?>',
            dataType: 'json',
            success: function (res) {
                if(res.code == 1) {
                    wufuCompositeAudio.pause();
                    $('.compoundhcBox').removeClass('compoundhcBoxActive');
                    getUserProductData();
                    getUserMaxProductData();
                } else {
                    return mui.toast(res.msg, {duration: '2000', type: 'div'});
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
            }
        });
    });
    function checkIsWufuComposite(){
        $.ajax({
                type: 'post',
                url: '<?php echo U("Product/checkIsWufuComposite"); ?>',
                dataType: 'json',
                success: function (res) {
                    let arr = [38,39,40,41,42];
                    if(res.code == 1) {
                        $('.tjwdd').hide();
                        $('.tjydd').show();
                    } else {
                        $('.tjwdd').show();
                        $('.tjydd').hide();
                    }
                    for(var i = 0; i<arr.length; i++) {
                        if(res.data.ids[arr[i]] >= 0) {
                            $('.heniubox[data-id="'+arr[i]+'"]').find('.manzu').show()
                            $('.heniubox[data-id="'+arr[i]+'"]').find('.bumanzu').hide()
                        } else {
                            $('.heniubox[data-id="'+arr[i]+'"]').find('.manzu').hide()
                            $('.heniubox[data-id="'+arr[i]+'"]').find('.bumanzu').show()
                        }
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    return mui.toast('网络失败，请刷新页面后重试', {duration: '2000', type: 'div'});
                }
            });
    }
    function showWufuCompositePage() {
        checkIsWufuComposite();
        wufuCompositeAudio.loop = true;
        wufuCompositeAudio.play();
        $('.compoundhcBox').addClass('compoundhcBoxActive');
    }
    // checkIsWufuComposite();
</script>
<script>

    // 连接服务端
    function connect() {
        // 创建websocket
        ws = new WebSocket("ws://"+document.domain+":7373");
        // 当socket连接打开时，输入用户名
        ws.onopen = onopen;
        // 当有消息时根据消息类型显示不同信息
        ws.onmessage = onmessage;
        ws.onclose = function() {
            console.log("连接关闭，定时重连");
            connect();
        };
        ws.onerror = function() {
            console.log("出现错误");
        };
    }
    connect();

    // 连接建立时发送登录信息
    function onopen()
    {
        // 登录
        ws_send_content({
            "type": "login",
            "client_name": "<?php echo htmlentities($user['account']); ?>",
            "uid": "<?php echo htmlentities($user['user_id']); ?>",
        });
    }


    // 服务端发来消息时
    function onmessage(e)
    {
        console.log(e.data);
        var data = eval("("+e.data+")");
        switch(data['type']){
            // 服务端ping客户端
            case 'ping':
                ws_send_content({
                    "type": "pong"
                });
                break;
            case 'login':
                if(data['offline_income'] > 0) {
                    $('#offline_income_money').html(moneyTransformation(data['offline_income']));
                    $('.lixianshyupBox').addClass('lixianshyupBoxActive');
                }
                rewardCountdownmaxtime = data['receive_reward_countdown'];
                rewardCountdowntimer = setInterval("CountDown()", 1000);
                break;
            case 'receive_reward_money_notify':
                if(data['money'] > 0) {
                    rewardCountdownmaxtime = data['receive_reward_countdown'];
                    rewardCountdowntimer = setInterval("CountDown()", 1000);
                    $('#reward_money').html(moneyTransformation(data['money']));
                    $('.drawupBox').addClass('drawupBoxActive');
                }
                break;
            case 'reload':
                window.location.reload();
                break;
            case 'logout':
                window.location.href="<?php echo U('User/outLogin'); ?>";
                break;
        }
    }

    function ws_send_content(data) {
        data.session_id = "<?php echo sid(); ?>";
        data = JSON.stringify(data);
        ws.send(data);
    }


    //    按钮特效 s
    //放大
    mui('body').on('tap', '.btneffectanfd', function () {
        if ($(this).hasClass('.btneffectfd') == false) {
            $(this).addClass('btneffectfd');
        }
        setTimeout(function () {
            $('.btneffectanfd').removeClass('btneffectfd');
        }, 1000);
    });
    //缩小
    mui('body').on('tap', '.btneffectansx', function () {
        if ($(this).hasClass('.btneffectsx') == false) {
            $(this).addClass('btneffectsx');
        }
        setTimeout(function () {
            $('.btneffectansx').removeClass('btneffectsx');
        }, 1000);
    });
    //    按钮特效 e

    //  五福合成弹窗  s
    mui('body').on('tap', '.compoundhcbtn', function () {
        $('.compoundhcBox').addClass('compoundhcBoxActive');
    });
    mui('body').on('tap', '.compoundhcqx', function () {
        wufuCompositeAudio.pause();
        $('.compoundhcBox').removeClass('compoundhcBoxActive');
    });

    //  五福合成弹窗 e


    // 离线收益弹窗 s

    mui('body').on('tap', '.lixianshyupqx', function () {
        $('.lixianshyupBox').removeClass('lixianshyupBoxActive');
    });
    // 离线收益弹窗 e


    // 金币不足弹窗 s

    mui('body').on('tap', '.jinbibzuupqx', function () {
        $('.jinbibzuupBox').removeClass('jinbibzuupBoxActive');
    });
    // 金币不足弹窗 e


    // 恭喜获得金币弹窗 s

    mui('body').on('tap', '.gxhdjbupqx', function () {
        $('.gxhdjbupBox').removeClass('gxhdjbupBoxActive');
    });
    mui('body').on('tap', '.gxhdjbupqxqr', function () {
        $('.gxhdjbupBox').removeClass('gxhdjbupBoxActive');
    });
    // 恭喜获得金币弹窗 e

    // 宝箱弹窗 s

    mui('body').on('tap', '.hdmfbxupqx', function () {
        $('.hdmfbxupBox').removeClass('hdmfbxupBoxActive');
    });
    mui('body').on('tap', '.buoy_ad_btn', function () {
        $('.hdmfbxupBox').addClass('hdmfbxupBoxActive');
    });
    // 宝箱弹窗 e





</script>



<nav class="mui-bar mui-bar-tab navWeiBox">
    <div data-url="<?php echo U('User/index'); ?>" <?php if(($controller == 'User') && ($action == 'index')): ?>class="mui-tab-item likeA navWeiImgActive"<?php else: ?>class="mui-tab-item likeA"<?php endif; ?>>
        <div class="navWeiImg_1">
            <img src="/template/wap/default/Static/centImages/2019a071_2.png" alt="" class="img_1">
            <img src="/template/wap/default/Static/centImages/2019a071_1.png" alt="" class="img_2">
        </div>
        <div class="mui-tab-label span_1">主页</div>
    </div>
    <div data-url="<?php echo U('Extend/index'); ?>" <?php if(($controller == 'Extend') && ($action == 'index')): ?>class="mui-tab-item likeA navWeiImgActive"<?php else: ?>class="mui-tab-item likeA"<?php endif; ?>>
        <div class="navWeiImg_1">
            <img src="/template/wap/default/Static/centImages/2019a071_4.png" alt="" class="img_1">
            <img src="/template/wap/default/Static/centImages/2019a071_3.png" alt="" class="img_2">
        </div>
        <div class="mui-tab-label span_1">游戏</div>
    </div>
	<!--<div data-url="http://shop.xmlianke.top/index.php?r=index/wap" class="mui-tab-item likeA">
        <div class="navWeiImg_1">
            <img src="/template/wap/default/Static/centImages/shop.png" alt="" class="img_1">
            <img src="/template/wap/default/Static/centImages/shop.png" alt="" class="img_2">
        </div>
        <div class="mui-tab-label span_1">商城</div>
    </div>-->
    <div data-url="<?php echo U('Assets/assetsIndex'); ?>" <?php if(($controller == 'Assets') && ($action == 'assetsindex')): ?>class="mui-tab-item likeA navWeiImgActive"<?php else: ?>class="mui-tab-item likeA"<?php endif; ?>>
        <div class="navWeiImg_1">
            <img src="/template/wap/default/Static/centImages/2019a071_6.png" alt="" class="img_1">
            <img src="/template/wap/default/Static/centImages/2019a071_5.png" alt="" class="img_2">
        </div>
        <div class="mui-tab-label span_1">财富</div>
    </div>
    <div data-url="<?php echo U('User/myInfo'); ?>" <?php if(($controller == 'User') && ($action == 'myinfo')): ?>class="mui-tab-item likeA navWeiImgActive"<?php else: ?>class="mui-tab-item likeA"<?php endif; ?>>
        <div class="navWeiImg_1">
            <img src="/template/wap/default/Static/centImages/2019a071_10.png" alt="" class="img_1">
            <img src="/template/wap/default/Static/centImages/2019a071_9.png" alt="" class="img_2">
        </div>
        <div class="mui-tab-label span_1">我的</div>
    </div>
</nav>


</body>
</html>