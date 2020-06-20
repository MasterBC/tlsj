## 极验Geetest
ThinkPHP5.1可用的极验扩展

## 样例
[极验demo](https://www.geetest.com/demo/)

## 安装
> composer require huelse/geetest

## 使用
### 参数配置
在配置文件config里配置geetest配置，需要到官网申请

~~~
//key配置
//路径 config/config.php
'geetest'=> [
       'captcha_id' =>'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
       'private_key'=>'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
    ],
~~~

### 模板里的调用

~~~

CSS样式参照demo.html的style样式48-144行

<!-- 设定DIV -->
<form method="post">
  <input type="text" id="inputUserid" class="form-control" placeholder="账号" required>
  <input type="password" id="inputPassword" class="form-control" placeholder="密码" required>
  <div id="captcha" style="height: 42px;">
    <div id="text">
      行为验证™ 安全组件加载中
    </div>
    <div id="wait" class="show">
      <div class="loading">
        <div class="loading-dot"></div>
        <div class="loading-dot"></div>
        <div class="loading-dot"></div>
        <div class="loading-dot"></div>
      </div>
    </div>
  </div>
  <input class="btn btn-lg btn-primary btn-block" id="submit" type="button" value="登陆"/>
</form>

<!-- 引入js库 -->
<!-- 注意，验证码本身是不需要 jquery 库，此处使用 jquery 仅为了在 demo 中使用，减少代码量 -->
<script src="https://apps.bdimg.com/libs/jquery/1.9.1/jquery.js"></script>
<script src="https://www.geetest.com/demo/libs/gt.js"></script>

<script>
$(document).ready(function () {
    var handler = function (captchaObj) {
        captchaObj.appendTo('#captcha');
        captchaObj.onReady(function () {
            $("#wait").hide();
        });
        $("#submit").click(function () {
            var result = captchaObj.getValidate();
            if (!result) {
                alert('请完成验证');
            } else {
                $.ajax({
                    type: 'POST',
                    url: '/login/index/login', // 自定义
                    dataType: 'json',
                    data: {
                        userId: $('#inputUserid').val(),
                        userPwd: $('#inputPassword').val(),
                        geetest_challenge: result.geetest_challenge,
                        geetest_validate: result.geetest_validate,
                        geetest_seccode: result.geetest_seccode
                    },
                    success: function (data) {
                        if (data) {  // 自定义
                            alert('登陆成功');
                        }
                    },
                    error: function (data) {
                        console.log(JSON.stringify(data));
                        alert('登陆失败');
                    },
                });
            }
        });
        window.gt = captchaObj;
    };
    $.ajax({
        url: "{:geetest_url()}?t=" + (new Date()).getTime(), // 加随机数防止缓存 // "geetest.html?t=" // 按需更换
        type: "get",
        dataType: "json",
        success: function (data) {
            // console.log(data);
            $('#text').hide();
            $('#wait').show();
            initGeetest({
                gt: data.gt,
                challenge: data.challenge,
                new_captcha: data.new_captcha,
                product: "float", // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
                offline: !data.success, // 表示用户后台检测极验服务器是否宕机，一般不需要关注
            }, handler);
        }
    });
});
</script>
~~~

### 控制器里验证

~~~
$data = Request::param(false); //传入请求数据,使用false参数以获取原始数据
if(!is_null($data) && !geetest_check($data)){
    //验证失败
    return json()->data(false)->code(403); // 自定义
}
~~~

### 更多

如有问题,请及时[issue](https://github.com/Huelse/geetest/issues)或者发送邮件huelse@oini.top
