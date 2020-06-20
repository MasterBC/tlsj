//处理点击this事件，需要打开原生浏览器

// 页面跳转
mui("body").on('tap','.likeA',function(){
    //获取id
    var url = this.getAttribute("data-url");
	if(url != null) {
        mui.openWindow({
            id:url,
            url:url,
            createNew:true,
        });
	}
});
mui("body").on('tap','.reminderByNo',function(){
	return mui.alert($(this).data('tishi'),'温馨提示');
});
/*mui.plusReady(function(){
 	plus.geolocation.getCurrentPosition(function(p){
		mui('Geolocation\nLatitude:' + p.coords.latitude + '\nLongitude:' + p.coords.longitude + '\nAltitude:' + p.coords.altitude);
	}, function(e){
		mui('Geolocation error: ' + e.message);
	} );
});*/




