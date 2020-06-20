/**
 * 获取省份
 */
function get_province() {
    var url = '/zfadmin.php?m=Zfuwl&c=Api&a=getRegion&level=1&parent_id=0';
    $.ajax({
        type: "GET",
        url: url,
        error: function (request) {
            alert("服务器繁忙, 请联系管理员!");
            return;
        },
        success: function (v) {
            v = '<option value="0">选择省份</option>' + v;
            $('#province').empty().html(v);
        }
    });
}

/**
 * 获取城市
 * @param t  省份select对象
 */
function get_city(t) {
    console.log(t);
    var parent_id = $(t).val();
    if (!parent_id > 0) {
        return;
    }
    $('#twon').empty().css('display', 'none');
    var url = '/zfadmin.php/Index/getRegion?level=2&parent_id=' + parent_id;
    $.ajax({
        type: "GET",
        url: url,
        error: function (request) {
            alert("服务器繁忙, 请联系管理员!");
            return;
        },
        success: function (v) {
            v = '<option value="0">选择城市</option>' + v;
            $('#city').empty().html(v);
        }
    });
}

/**
 * 获取地区
 * @param t  城市select对象
 */
function get_area(t) {
    var parent_id = $(t).val();
    if (!parent_id > 0) {
        return;
    }
    var url = '/zfadmin.php/Index/getRegion?level=3&parent_id=' + parent_id;
    $.ajax({
        type: "GET",
        url: url,
        error: function (request) {
            alert("服务器繁忙, 请联系管理员!");
            return;
        },
        success: function (v) {
            v = '<option>选择区域</option>' + v;
            $('#district').empty().html(v);
        }
    });
}

/**
 * 获取乡镇
 * @param obj
 */
function get_twon(obj) {
    var parent_id = $(obj).val();
    var url = '/zfadmin.php/Index/getTwon?parent_id=' + parent_id;
    $.ajax({
        type: "GET",
        url: url,
        success: function (res) {
            if (parseInt(res) == 0) {
                $('#twon').empty().css('display', 'none');
            } else {
                $('#twon').css('display', 'block');
                $('#twon').empty().html(res);
            }
        }
    });
}

