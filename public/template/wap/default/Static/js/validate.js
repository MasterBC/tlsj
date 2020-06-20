/**
 * 手机号码格式判断
 * @param tel
 * @returns {boolean}
 */
function check_mobile(tel) {
    var reg = /(^1[123456789]\d{9}$)/;
    console.log(tel);
    if (reg.test(tel)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 邮箱格式判断
 * @param str
 */
function check_mail(str) {
    var reg = /^[a-z0-9]([a-z0-9\\.]*[-_]{0,4}?[a-z0-9-_\\.]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+([\.][\w_-]+){1,5}$/i;
    if (reg.test(str)) {
        return true;
    } else {
        return false;
    }
}