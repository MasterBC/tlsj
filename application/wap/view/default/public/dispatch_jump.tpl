{extend name="public:base" /}
{block name="title"}温馨提示{/block}
{block name="other_css"}
    <style>
        .warmtibardBox {
            margin-top:20%;
            text-align: center;
            padding:15px;
        }
        .warmtibardtl {
            color:#888888;
            margin:15px 0px 30px 0px;
        }
        .warmtibardImg img {
            width:30%;
        }
        .warmtibaAn {
            display: inline-block;
            padding:2px 30px;
            background: #5863FC;
            color:#ffffff;
            font-size:14px;
            border-radius:2px;
        }
        .warmtibaAncl {
            color:#ff4d51;
        }
    </style>
{/block}
{block name="body"}
    <header class="mui-bar mui-bar-nav myAssetTop">
        <a class="mui-action-back mui-icon icon-font mui-pull-left likeA">&#xe95b;</a>
        <h1 class="mui-title">
            温馨提示
        </h1>
    </header>
    <div class="mui-content">
        {switch name="code"}
            {case value='1'}
                <!--成功  s-->
                <div class="warmtibardBox">
                    <div class="warmtibardImg"><img src="__STATIC__/centImages/mobile_ok.png" alt=""></div>
                    <div class="warmtibardtl">{$msg}</div>
                    <div class="warmtibaAn">等待时间<span class="warmtibaAncl" id="wait"> {$wait} </span>秒</div>
                </div>
                <!--成功  e-->
            {/case}
            {case value='0'}
                <!--失败  s-->
                <div class="warmtibardBox">
                    <div class="warmtibardImg"><img src="__STATIC__/centImages/mobile_no.png" alt=""></div>
                    <div class="warmtibardtl">{$msg}</div>
                    <div class="warmtibaAn">等待时间<span class="warmtibaAncl" id="wait"> {$wait} </span>秒</div>
                </div>
                <!--失败  e-->
            {/case}
        {/switch}
    </div>
{/block}
{block name="footer"}
    {__block__}
    <script type="text/javascript">
        (function(){
            var wait = document.getElementById('wait'),
                href = "{$url}";
            var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                    location.href = href;
                    clearInterval(interval);
                };
            }, 1000);
        })();
    </script>
{/block}