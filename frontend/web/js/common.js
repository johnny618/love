$(function() {
    $('.J_todo').click(function() {
        alert('敬请期待。');
        return false;
    });
});


/**
 * string.format
 *
 * @tutorial "{0} xxx {1} xxx {0} {2}".format("arg1", "arg2")
 */
if (!String.prototype.format) {
    String.prototype.format = function() {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function(match, number) {
            return typeof args[number] != 'undefined' ? args[number] : match;
        });
    };
}
if (!String.format) {
    String.format = function(format) {
        var args = Array.prototype.slice.call(arguments, 1);
        return format.replace(/{(\d+)}/g, function(match, number) {
            return typeof args[number] != 'undefined' ? args[number] : match;
        });
    };
}

if (! Object.keys) {
    Object.keys = function(obj) {
        var ret = [];
        for (var k in obj) {
            ret.push(k);
        }
        return ret;
    }
}

/**
 * 验证身份证
 *
 * @return mixed 正确则返回生(birth)和性别(sex)；错误则返回false
 */
function verify_id_card(id) {
    var powers = [ '7', '9', '10', '5', '8', '4', '2', '1', '6', '3', '7', '9', '10', '5', '8', '4', '2' ];
    var parityBit = [ '1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2' ];

    var birth, sex;
    var _id = id + '';
    var _num = _id.substr(0, 17);
    var _parityBit = _id.substr(17); //校验位
    birth = _id.substr(6, 8);

    var _power = 0;
    for (var i = 0; i < 17; i++) {
        // 校验每一位的合法性
        if (_num.charAt(i) < '0' || _num.charAt(i) > '9') {
            return false;
            break;
        }
        else {
            _power += parseInt(_num.charAt(i)) * parseInt(powers[i]); // 加权

            // 设置性别
            if (i == 16) {
                sex = parseInt(_num.charAt(i)) % 2 == 0 ? 'female' : 'male';
            }
        }
    }

    var mod = parseInt(_power) % 11; // 取模
    if (parityBit[mod] == _parityBit) {
        return {
            birth: birth,
            sex: sex,
        };
    }

    return false;
}

/**
 * 精度计算
 * @param number
 * @param precision
 * @returns
 */
function strip(number, precision) {
    precision = precision | 2;
    return (parseFloat(number.toPrecision( precision )));
}

function in_array(needle, haystack, argStrict) {
    var key = '',
        strict = !! argStrict;

    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;
            }
        }
    }
    else {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return true;
            }
        }
    }

    return false;
}