(function ($) {
    /**
     * @param {Object} options
     * @param {Array}  options.list  存储奖品的的列表，example [{1:{name:'谢谢参与',image:'1.jpg'}}]
     * @param {Object} options.outerCircle {color:'#df1e15'} 外圈颜色，默认红色
     * @param {Object} options.innerCircle {color:'#f4ad26'} 里圈颜色，默认黄色
     * @param {Array}  options.dots ['#fbf0a9', '#fbb936'] 装饰点颜色 ，默认深黄浅黄交替
     * @param {Array}  options.disk ['#ffb933', '#ffe8b5', '#ffb933', '#ffd57c', '#ffb933', '#ffe8b5', '#ffd57c'] 中心奖盘的颜色，默认7彩
     * @param {Object} options.title {color:'#5c1e08',font:'19px Arial'} 奖品标题颜色
     */
    $.fn.WheelSurf = function (options) {
        var _default = {
            outerCircle: {
                color: '#df1e15'
            },
            innerCircle: {
                color: '#f4ad26'
            },
            dots: ['#fbf0a9', '#fbb936'],
            disk: ['#008894', '#ffe8b5', '#008894', '#ffd57c', '#008894', '#ffe8b5', '#008894'],
            title: {
                color: '#5c1e08',
                font: '19px Arial'
            }
        }

        $.extend(_default,options)
        // 画布中心移动到canvas中心
        var _this = this[0],
            width = _this.width,
            height = _this.height,
            ctx = _this.getContext("2d"),
            imgs = [],
            awardTitle = [],
            awardPic = []
        for (var item in _default.list) {
            awardTitle.push(_default.list[item].name)
            imgs.push(_default.list[item].image)
        }
        var num = imgs.length
        // 圆心
        var x = width / 2
        var y = height / 2
        ctx.translate(x, y)

        return {
            init: function (angelTo) {
                angelTo = angelTo || 0;

                ctx.clearRect(-this.width, -this.height, this.width, this.height);

                // 平分角度
                var angel = (2 * Math.PI / 360) * (360 / num);
                var startAngel = 2 * Math.PI / 360 * (-90)
                var endAngel = 2 * Math.PI / 360 * (-90) + angel

                // 旋转画布
                ctx.save()
                ctx.rotate(angelTo * Math.PI / 180);
                // 画外圆
                ctx.beginPath();
                ctx.lineWidth = 10;
                ctx.strokeStyle = _default.outerCircle.color;
                ctx.arc(0, 0, 130, 0, 2 * Math.PI)
                ctx.stroke();
                // 画里圆
                ctx.beginPath();
                ctx.lineWidth = 0;
                ctx.strokeStyle = _default.innerCircle.color;
                ctx.arc(0, 0, 130, 0, 2 * Math.PI)
                ctx.stroke();

                // 装饰点
                var dotColor = _default.dots
                for (var i = 0; i < 20; i++) {
                    // 装饰点 圆心 坐标计算
                    ctx.beginPath();
                    var radius = 130;
                    var xr = radius * Math.cos(startAngel)
                    var yr = radius * Math.sin(startAngel)

                    ctx.fillStyle = dotColor[i % dotColor.length]
                    ctx.arc(xr, yr, 3, 0, 2 * Math.PI)
                    ctx.fill()

                    startAngel += (2 * Math.PI / 360) * (360 / 20);

                }
                // 画里转盘
                var colors = _default.disk
                for (var i = 0; i < num; i++) {
                    ctx.beginPath();
                    ctx.lineWidth = 130;
                    ctx.strokeStyle = colors[i % colors.length]
                    ctx.arc(0, 0, 70, startAngel, endAngel)
                    ctx.stroke();
                    startAngel = endAngel
                    endAngel += angel
                }
                // 添加奖品
                function loadImg() {

                    var dtd = $.Deferred()
                    var countImg = 0
                    if (awardPic.length) {
                        return dtd.resolve(awardPic);
                    }
                    for (var i = 0; i < num; i++) {
                        var img = new Image()
                        awardPic.push(img)

                        img.src = imgs[i]
                        img.onload = function () {
                            countImg++
                            if (countImg == num) {
                                dtd.resolve(awardPic);
                            }
                        }
                    }
                    return dtd.promise()
                }

                $.when(loadImg()).done(function (awardPic) {

                    startAngel = angel / 2
                    for (var i = 0; i < num; i++) {
                        ctx.save();
                        ctx.rotate(startAngel);
                        ctx.drawImage(awardPic[i], -25, -34 -65,50,50);
                        ctx.font = _default.title.font;
                        ctx.font = " 18px 'Helvetica Neue', Helvetica, sans-serif";
                        ctx.fillStyle = _default.title.color;
                        ctx.textAlign = "center";
                        ctx.fillText(awardTitle[i], 0, -110);
                        startAngel += angel;
                        ctx.restore();
                    }
                })
                ctx.restore();
            },
            /**
             * @param angel 旋转角度
             * @param callback 转完后的回调函数
             */
            lottery: function (angel, callback) {
                angel = angel || 0
                angel = 360-angel
                angel += 720
                // 基值（减速）
                var baseStep = 45
                // 起始滚动速度
                var baseSpeed = 0.4
                // 步长
                var count = 1;
                var _this = this
                var timer = setInterval(function () {

                    _this.init(count)
                    if (count == angel) {
                        clearInterval(timer)
                        if (typeof callback == "function") {
                            callback()
                        }
                    }
                    count = count + baseStep * (((angel - count) / angel) > baseSpeed ? baseSpeed : ((angel - count) / angel))
                    if (angel - count < 0.5) {
                        count = angel
                    }

                }, 25)
            }
        }

    }
}(jQuery))