<?php /*a:2:{s:66:"/www/wwwroot/tlsj/application/admin/view/admin_user/edit_info.html";i:1577103176;s:57:"/www/wwwroot/tlsj/application/admin/view/public/base.html";i:1577103178;}*/ ?>
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
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <div class="layui-form" lay-filter="layuiadmin-form-role" id="layuiadmin-form-role" style="padding: 20px 30px 0 0;">
                        <div class="layui-form-item">
                            <label class="layui-form-label">登陆用户名</label>
                            <div class="layui-input-block">
                                <input type="text" disabled value="<?php echo htmlentities($info['user_name']); ?>" class="layui-input" lay-verify="required" autocomplete="off"/>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">手机号</label>
                            <div class="layui-input-block">
                                <input type="text" name="mobile" value="<?php echo htmlentities($info['mobile']); ?>" class="layui-input"  autocomplete="off"/>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">邮箱</label>
                            <div class="layui-input-block">
                                <input type="text" name="email" value="<?php echo htmlentities($info['email']); ?>" class="layui-input" autocomplete="off"/>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">登录密码</label>
                            <div class="layui-input-block">
                                <input type="password" name="password" class="layui-input" autocomplete="off"/>
                                <div class="layui-form-mid layui-word-aux">不输代表不修改</div>
                            </div>
                        </div>
                        <div class="layui-form-item layui-hide">
                            <button class="layui-btn" lay-submit lay-filter="submitBtn" id="submitBtn">提交</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript" src="/static/layuiadmin/layui/layui.js"></script><script type="text/javascript" src="/static/js/jquery.min.js"></script><script type="text/javascript" src="/static/js/admin.js"></script><script type="text/javascript" src="/vendor/pace/js/pace.min.js"></script>

<script>
    layui.use('form', function () {
        var form = layui.form;
    });
</script>

</body>
</html>