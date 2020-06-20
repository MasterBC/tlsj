1,后台使用php thinkphp框架配合php长链接框架workman，前端使用接近于原生页面的mui实现类陀螺世界农场养成类游戏；

2,环境: ubuntu18.04+apache2.4 + mysql5.6(5.6以上需要去掉字段严格检查限制，否则会有错误) + php7.3版本；

3,config/database.php （修改数据库配置 改成自己的即可 );

4,启动workman：
	1>进入项目根目录
	2>执行命令：php think worker:server

5,浏览器打开测试
	前台：htttp://你的ip/
	后台:http://你的ip/zfadmin.php
	    后台登录帐号：admin
        后台登录密码：add8897