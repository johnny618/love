
function Plan(rate_cfg, project_info,rate_activity_cfg) {
    this.rate_cfg = rate_cfg;
    this.rate_activity_cfg = rate_activity_cfg;
    this.rate_type = project_info['id'] || undefined;
    this.default_rate = project_info['rate'];
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
    // if (! this.check()) {
    //     alert('数据错误');
    //     return false;
    // }

    var max = 0;
    for (var i in this.rate_cfg[ this.rate_type ]) {
        if (i >= max) {
            max = i;
        }
    }
    return max;
}

//获取生效的利率
Plan.prototype.get_rate = function(money_op, interval, power) {
    power=power*10;
    money_op=money_op*100;
    var money_loan = money_op*power/(power+10);//借款金额
    var ret = this.default_rate;
    var rates = this.rate_cfg[ this.rate_type ];

    for (var _power in rates) {
        if (power <= _power) {
            for (var _minper in rates[_power]) {
                if (interval >= _minper) {
                    for (var _minmoney in rates[_power][_minper]) {
                        if (money_loan >= _minmoney) {
                            for (var _rate in rates[_power][_minper][_minmoney]) {
                                if(!ret || parseInt(_rate) < parseInt(ret)){
                                    ret = _rate;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return ret/10000;
}

//获取生效的利率
Plan.prototype.get_rate_activity = function(money_op, interval, power) {
    power=power*10;
    money_op=money_op*100;
    var money_loan = money_op*power/(power+10);//借款金额
    var ret = '';
    var rates = this.rate_activity_cfg[ this.rate_type ];

    for (var _power in rates) {
        if (power <= _power) {
            for (var _minper in rates[_power]) {
                if (interval >= _minper) {
                    for (var _minmoney in rates[_power][_minper]) {
                        if (money_loan >= _minmoney) {
                            for (var _rate in rates[_power][_minper][_minmoney]) {
                                var curent_date = CurentDate();
                                var curent_time = CurentTime();
                                if ((curent_date >= rates[_power][_minper][_minmoney][_rate]['activity_st'] &&
                                    curent_date <= rates[_power][_minper][_minmoney][_rate]['activity_ed']) &&
                                    (curent_time >= rates[_power][_minper][_minmoney][_rate]['time_st'] &&
                                    curent_time <= rates[_power][_minper][_minmoney][_rate]['time_ed'])
                                ){
                                    if(!ret || parseInt(_rate) < parseInt(ret)){
                                        ret = _rate;
                                    }
                                }

                            }
                        }
                    }
                }
            }
        }
    }
    return ret/10000;
};


function CurentDate()
{
    var now = new Date();

    var year = now.getFullYear();       //年
    var month = now.getMonth() + 1;     //月
    var day = now.getDate();            //日

    var hh = now.getHours();            //时
    var mm = now.getMinutes();          //分
    var ss = now.getSeconds();           //秒

    var clock = year + "-";

    if(month < 10)
        clock += "0";

    clock += month + "-";

    if(day < 10)
        clock += "0";

    clock += day + " ";

    if(hh < 10)
        clock += "0";

        clock += hh + ":";
    if (mm < 10) clock += '0';
        clock += mm + ":";

    if (ss < 10) clock += '0';
        clock += ss;
    return clock;
}

function CurentTime()
{
    var now = new Date();
    var hh = now.getHours();            //时
    var mm = now.getMinutes();          //分
    var ss = now.getSeconds();           //秒

    var clock = "";
    if(hh < 10)
        clock += "0";

    clock += hh + ":";
    if (mm < 10) clock += '0';
    clock += mm + ":";

    if (ss < 10) clock += '0';
    clock += ss;
    return clock;
}

//获取生效的利率的备注
Plan.prototype.get_rate_activity_des = function(money_op, interval, power) {
    power=power*10;
    money_op=money_op*100;
    var money_loan = money_op*power/(power+10);//借款金额
    var ret = '';
    var rates = this.rate_activity_cfg[ this.rate_type ];
    var des = "";

    for (var _power in rates) {
        if (power <= _power) {
            for (var _minper in rates[_power]) {
                if (interval >= _minper) {
                    for (var _minmoney in rates[_power][_minper]) {
                        if (money_loan >= _minmoney) {
                            for (var _rate in rates[_power][_minper][_minmoney]) {
                                var curent_date = CurentDate();
                                var curent_time = CurentTime();
                                if ((curent_date >= rates[_power][_minper][_minmoney][_rate]['activity_st'] &&
                                    curent_date <= rates[_power][_minper][_minmoney][_rate]['activity_ed']) &&
                                    (curent_time >= rates[_power][_minper][_minmoney][_rate]['time_st'] &&
                                    curent_time <= rates[_power][_minper][_minmoney][_rate]['time_ed'])
                                ){
                                    if(!ret || parseInt(_rate) < parseInt(ret)){
                                        ret = _rate;
                                        des = rates[_power][_minper][_minmoney][_rate]['des'];
                                    }
                                }

                            }
                        }
                    }
                }
            }
        }
    }

    return des;
}

//获取生效的利率的备注
Plan.prototype.get_rate_des = function(money_op, interval, power) {
    power=power*10;
    money_op=money_op*100;
    var money_loan = money_op*power/(power+10);//借款金额
    var ret = this.default_rate;
    var rates = this.rate_cfg[ this.rate_type ];
    var des = "";
    for (var _power in rates) {
        if (power <= _power) {
            for (var _minper in rates[_power]) {
                if (interval >= _minper) {
                    for (var _minmoney in rates[_power][_minper]) {
                        if (money_loan >= _minmoney) {
                            for (var _rate in rates[_power][_minper][_minmoney]) {
                                if(!ret || _rate<ret){
                                    ret = _rate;
                                    des = rates[_power][_minper][_minmoney][_rate];
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return des;
}



Plan.prototype.get_fee = function(money_op, power,rate){

    var TYPE_DAY = 1;
    var TYPE_MONTH = 2;

    // if (! this.check()) {
    //     alert('数据错误');
    //     return false;
    // }
    switch(parseInt(this.rate_type)) {
        case TYPE_DAY:
            var fee = (money_op/(power+1))*power*(rate/100)/30;//先算出一月的利息费再算每一天的利息费
            fee = fee.toFixed(2);
            return fee;
        case TYPE_MONTH:
            var fee = (money_op/(power+1))*power*(rate/100);
            fee = fee.toFixed(2);
            return fee;
        default:
            return false;
    }
}

var warning_val = 1.1;
var force_val = 1.07;

//当前配资类型下, 预警线
Plan.prototype.cal_warning_line = function(insure, power) {
    power = parseInt(power);
    var ret = false;
    // if (! this.check()) {
    //     alert('数据错误');
    //     return ret;
    // }

    ret = insure*((power+1)*warning_val - 1);

    return Math.round(ret*100)/100;
}

//当前配资类型下, 强制线
Plan.prototype.cal_force_line = function(insure, power) {
    var ret = false;

    // if (! this.check()) {
    //     alert('数据错误');
    //     return false;
    // }
    ret = ret = insure*((parseInt(power)+1)*force_val - 1);;
    return Math.round(ret*100)/100;
}
