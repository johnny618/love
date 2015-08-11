
function Plan(rate_cfg, rate_type) {
    this.rate_cfg = rate_cfg;
    this.rate_type = rate_type || undefined;
}

//rate_type 变更
Plan.prototype.set_rate_type = function(rate_type) {
    if (parseInt(rate_type) > 0) {
        this.rate_type = rate_type;
    }
    else {
        this.rate_type = undefined;
        alert('配资类型错误');
    }
}

//检查有效性
Plan.prototype.check = function() {
    if (undefined === this.rate_type) {
        return false;
    }

    var rates = (this.rate_type in this.rate_cfg) ? this.rate_cfg[ this.rate_type ] : {};
    if ($.isEmptyObject(rates)) {
        return false;
    }

    return true;
}

//当前配资类型下, 最大时间跨度
Plan.prototype.get_max_interval = function() {
    if (! this.check()) {
        alert('数据错误');
        return false;
    }

    var max = 0;
    for (var i in this.rate_cfg[ this.rate_type ]) {
        if (i >= max) {
            max = i;
        }
    }
    return max;
}

//获取生效的利率
Plan.prototype.get_rate = function(amount, interval) {
    var ret = false;
    var rates = this.rate_cfg[ this.rate_type ];
    outer:
    for (var _interval in rates) {
        if (interval <= _interval) {
            for (var _amount in rates[_interval]) {
                if (amount <= _amount) {
                    ret = rates[_interval][_amount];
                    break outer;
                }
            }
        }
    }
    return ret;
}


Plan.prototype.get_fee = function(type, amount, interval) {
    type = parseInt( type );
    throw new Error('费用获取还未实现'); //TODO 费用获取还未实现

    switch(type) {
        case 1:
            return '';
        case 2:
            return '';
        default:
            return false;
    }
}

var warning_val = 1.1;
var force_val = 1.07;
//当前配资类型下, 预警线
Plan.prototype.cal_warning_line = function(insure, power) {
    var ret = false;
    if (! this.check()) {
        alert('数据错误');
        return ret;
    }

    ret = insure * power * warning_val - insure;
    return Math.round(ret*100)/100;
}

//当前配资类型下, 强制线
Plan.prototype.cal_force_line = function(insure, power) {
    var ret = false;

    if (! this.check()) {
        alert('数据错误');
        return false;
    }

    ret = insure * power * force_val - insure;
    return Math.round(ret*100)/100;
}

//当前配资类型下, 预警线
Plan.prototype.cal_warning_line_day = function(insure, power) {
    var ret = false;
    if (! this.check()) {
        alert('数据错误');
        return ret;
    }

    ret = insure / power * (warning_val - power);
    return Math.round(ret*100)/100;
}

//当前配资类型下, 强制线
Plan.prototype.cal_force_line_day = function(insure, power) {
    var ret = false;

    if (! this.check()) {
        alert('数据错误');
        return false;
    }
    ret = insure / power * (force_val - power);
    return Math.round(ret*100)/100;
}
