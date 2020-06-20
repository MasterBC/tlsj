
/**
 @ Name：layui.orgCharts 中国省市区选择器
         注意：本文件只是一个扩展组件的示例，暂时没有写相关功能
 @ Author：贤心
 @ License：MIT 
 */
 
layui.define('form', function(exports){ //假如该组件依赖 layui.form
  var $ = layui.$
  ,form = layui.form
  
  //字符常量
  ,MOD_NAME = 'orgCharts', ELEM = '.layui-orgCharts'
  
  //外部接口
  ,orgCharts = {
	onClick: {}, //元素点击事件
	el_style: 'normal', //元素风格
	el_root: {}, //操作根元素
	data: {}, //数据
	//根据地址绘制
	drawByUrl: function(data) {
		try {
			//请求服务器数据 start
			var ajax = new XMLHttpRequest();
			ajax.open('get', data.url);
			ajax.send();
			ajax.onreadystatechange = function() {
				if(ajax.readyState == 4 && ajax.status == 200) {
					orgCharts.data = JSON.parse(ajax.responseText).data;
					orgCharts.draw();

					//加载完成  判断是否定义完成回调函数  有则执行回调
					var isFunction = false;
					try {  
						isFunction = typeof(eval(data.success)) == "function";
					} catch(e) {}
					if(isFunction) {  
						data.success();
					}
				}
			}
			//请求服务器数据 end

		} catch(e) {
			//加载失败 执行回调
			var isFunction = false;

			try {  
				isFunction = typeof(eval(data.error)) == "function";
			} catch(e) {}

			if(isFunction) {  
				data.error(e);
			}
			//加载失败 执行回调 end
		}

	},
	//初始化 id元素   style样式
	init: function(data) {
		try {   //尝试初始化
			this.el_root = document.getElementById(data.id);

			this.el_root.setAttribute("class", "root_div"); //根容器样式

			if(data.theme != undefined && data.theme != '') {
				this.el_style = data.theme;
			}
			//鼠标移动事件 start
			var el = document.getElementById(data.id);
			el.style.left = '0px';
			el.style.top = '0px';
			var x = 0; //
			var y = 0; //
			var l = 0; //记录上次移动位置
			var t = 0; //记录上次移动位置
			var isDown = false;
			//鼠标按下事件
			/*el.onmousedown = function(e) {
				//获取x坐标和y坐标
				x = e.clientX;
				y = e.clientY;
				//开关打开
				isDown = true;
				//设置样式  
				el.style.cursor = 'move';
			}*/

			//鼠标移动
			/*window.onmousemove = function(e) {
				if(isDown == false) {
					return;
				}
				//获取x和y
				var nx = e.clientX;
				var ny = e.clientY;
				el.style.left = l + (nx - x) + 'px';
				el.style.top = t + (ny - y) + 'px';
			}
			//鼠标抬起事件
			onmouseup = function() {
				//开关关闭
				isDown = false;
				el.style.cursor = 'default';
				l = parseInt(el.style.left.split("px")[0]);
				t = parseInt(el.style.top.split("px")[0]);
			}*/

			//鼠标移动事件 end
			var isFunction = false;

			try {  
				isFunction = typeof(eval(data.onClick)) == "function";
			} catch(e) {}
			if(isFunction) {  
				orgCharts.onClick = data.onClick;
			}
			//初始化成功  判断是否定义完成回调函数  有则执行回调

			try {  
				isFunction = typeof(eval(data.success)) == "function";
			} catch(e) {}
			if(isFunction) {  
				data.success();
			}
		} catch(e) {
			//初始化异常  判断是否定义 异常回调函数 有则执行
			if(this.el_root == undefined) {
				e = '初始化错误: 找不到元素id';
			}
			var isFunction = false;

			try {  
				isFunction = typeof(eval(data.error)) == "function";
			} catch(e) {}

			if(isFunction) {  
				data.error(e);
			}

		}
	},
	//根据数据绘制
	drawByData: function(data) { //初始化 data数据  
		try {  
			this.data = data.data;
			this.draw();
			//成功回调 start
			var isFunction = false;

			try {  
				isFunction = typeof(eval(data.success)) == "function";
			} catch(e) {}

			if(isFunction) {  
				data.success();
			}
			//成功回调 end
		} catch(e) {
			//异常回调 start
			var isFunction = false;

			try {  
				isFunction = typeof(eval(data.error)) == "function";
			} catch(e) {}

			if(isFunction) {  
				data.error(e);
			}
			//异常回调 end
		}

	},
	//设置主题
	setTheme: function(theme) {
		try {  
			if(theme != undefined && theme != '') {
				orgCharts.el_root.innerHTML = '';
				orgCharts.el_style = theme;
				orgCharts.draw();
			}
		} catch(e) {

		}
	},
	draw: function() {
		//nodes节点数组  parent容器
		function drawNodes(nodes, parent) {
			var level_count = 0; //跳过已经计算的层级

			for(var x in nodes) {
				var openShow = true; //是否展开子项
				var node = document.createElement("div"); //节点 容器
				var contentSpan = document.createElement("span"); //节点标题
				var contentSpan2 = document.createElement("span"); //节点标题
				var content = document.createElement("div"); //节点标题
				var open = document.createElement("div"); //节点 容器
				// console.log(nodes[x]);

				if(nodes[x].html == '' || nodes[x].html == undefined) {
					contentSpan2.innerText = '点击查看详情';
					contentSpan.innerText = nodes[x].name; //节点标题内容
					content.setAttribute("class", "node node-" + orgCharts.el_style); //节点容器样式
					content.setAttribute("style", "background:"+nodes[x].bg_color+";"); //节点容器样式
					if(nodes[x].child.length > 0) {
						if(nodes[x].open == 'true') {
							open.setAttribute("class", "org-open-down"); //节点容器样式
						} else {
							open.setAttribute("class", "org-open-up"); //节点容器样式
							openShow = false;
						}
					}
				} else {
					content.setAttribute("class", "user-html"); //节点容器样式
					contentSpan.innerHTML = nodes[x].html;
					if(nodes[x].child.length > 0) {
						if(nodes[x].open == 'true') {
							open.setAttribute("class", "org-open-down-html"); //节点容器样式
						} else {
							open.setAttribute("class", "org-open-up-html"); //节点容器样式
							openShow = false;
						}
					}
				}

				content.appendChild(contentSpan);
				nodes[x].child.length > 0 && content.appendChild(contentSpan2);
				content.appendChild(open);

				//点击回调 start
				var isFunction = false;
				try {  
					isFunction = typeof(eval(orgCharts.onClick)) == "function";
				} catch(e) {}

				if(isFunction) {  
					var data = {};
					data.id = nodes[x].id;
					data.name = nodes[x].name;
					data.prev_name = nodes[x].prev_name;
					data.prev_id = nodes[x].prev_id;
					contentSpan.setAttribute("onclick", "layui.orgCharts.onClick(this, " + JSON.stringify(data) + ", 1)");
					contentSpan2.setAttribute("onclick", "layui.orgCharts.onClick(this, " + JSON.stringify(data) + ", 2)");

				}
				//点击回调 end

				//收起与隐藏 start
				open.onclick = function() {
					if(this.className == "org-open-up-html") {
						this.setAttribute("class", "org-open-down-html");
						show(this);
					} else if(this.className == "org-open-down-html") {
						this.setAttribute("class", "org-open-up-html");
						hide(this);
					} else if(this.className == "org-open-up") {
						this.setAttribute("class", "org-open-down");
						show(this);
					} else if(this.className == "org-open-down") {
						this.setAttribute("class", "org-open-up");
						hide(this);
					}
				}
				//收起与隐藏 end
				function hide(el) {
					var brother1 = el.parentNode.parentNode.lastChild;
					var brother2 = brother1.previousSibling;
					brother1.style.display = 'none';
					brother2.style.display = 'none';
				}

				function show(el) {
					var brother1 = el.parentNode.parentNode.lastChild;
					var brother2 = brother1.previousSibling;
					brother2.style.display = '';
					brother1.style.display = '';
				}

				node.setAttribute("class", "node"); //节点容器样式

				if(parent.parentNode.id != orgCharts.el_root.id && nodes.length > 1) {

					var line_h = document.createElement("div"); //头部线条

					/*
					 * 选择线条类型
					 */
					if(x == 0) {
						line_h.setAttribute("class", "line transverse-line-s transverse-line-" + orgCharts.el_style); //节点容器样式
					} else if(x == nodes.length - 1) {
						line_h.setAttribute("class", "line transverse-line-e transverse-line-" + orgCharts.el_style); //节点容器样式
					} else {
						line_h.setAttribute("class", "line transverse-line-c transverse-line-" + orgCharts.el_style); //节点容器样式
					}

					node.appendChild(line_h);

					var line_div = document.createElement("div"); //头部线条
					var line_s = document.createElement("div"); //头部线条

					line_s.setAttribute("class", "line vertical-line-" + orgCharts.el_style); //节点容器样式

					line_div.appendChild(line_s);

					node.appendChild(line_div); //添加线条
				}

				node.appendChild(content); //添加标题

				if(nodes[x].child.length > 0) {

					var span_div = document.createElement("div"); //竖的线条
					var span = document.createElement("span"); //竖的线条

					span.setAttribute("class", "line vertical-line-" + orgCharts.el_style); //节点容器样式

					span_div.appendChild(span);
					var parent_div = document.createElement("div");
					parent_div.setAttribute("class", "parent_div");
					
					if(!openShow) {
						span_div.style.display = 'none';
						parent_div.style.display = 'none';
					}
					node.appendChild(span_div);

					node.appendChild(parent_div);

					drawNodes(nodes[x].child, parent_div);

				}

				parent.appendChild(node);

			}

		}

		/**
		 * 创建第一个父容器
		 */
		var parent_div = document.createElement("div");
		parent_div.setAttribute("class", "parent_div");
		parent_div.id = 'parent_main';
		orgCharts.el_root.appendChild(parent_div);

		drawNodes(orgCharts.data, parent_div);
	}
}
  //操作当前实例
  ,thisIns = function(){
    var that = this
    ,options = that.config
    ,id = options.id || options.index;
    
    return {
      reload: function(options){
        that.reload.call(that, options);
      }
      ,config: options
    }
  }
  
  //构造器
  ,Class = function(options){
    var that = this;
    that.index = ++orgCharts.index;
    that.index = ++orgCharts.index;
    that.config = $.extend({}, that.config, orgCharts.config, options);
    that.render();
  };
  
  //默认配置
  Class.prototype.config = {
    layout: ['province', 'city', 'county', 'street'] //联动层级
    //其他参数
    //……
  };
  
  //渲染视图
  Class.prototype.render = function(){
  }
  
  //核心入口
  orgCharts.render = function(options){
    var ins = new Class(options);
    return thisIns.call(ins);
  };
  
  //加载组件所需样式
  layui.link(layui.cache.base + 'orgCharts/orgCharts.css?v=1', function(){
    //样式加载完毕的回调
    
  }, 'orgCharts'); //此处的“orgCharts”要对应 orgCharts.css 中的样式： html #layuicss-orgCharts{}
  
  exports('orgCharts', orgCharts);
});