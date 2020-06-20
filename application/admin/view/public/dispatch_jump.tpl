{extend name="public:base" /}
{block name="title"}出错了{/block}
{block name="body"}
    <div class="layui-fluid">
        <div class="layadmin-tips">
            <i class="layui-icon" face style="font-size:195px;">&#xe664;</i>

            <div class="layui-text" style="font-size: 20px;">
                <?php echo(strip_tags($msg));?>
            </div>
            <p class="jump">
                页面自动 <a id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b>
            </p>
        </div>
    </div>
{/block}
{block name="footer"}
    {__block__}
    <script type="text/javascript">
        (function () {
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
            var interval = setInterval(function () {
                var time = --wait.innerHTML;
                if (time <= 0) {
                    if(typeof(parent.callback)=="function") {
                        parent.callback();
                    } else {
                        location.href = href;
                    }
                    clearInterval(interval);
                }
                ;
            }, 1000);
        })();
    </script>
{/block}