// document.write("<script src='mask.js'></script>");
$(document).ready(function(){
    var text_map = {};
    text_map['增']='申请增配';
    text_map['追']='追加保证金';
    text_map['盈']='提取盈利';
    text_map['续']='申请续约';
    text_map['终']='终止操盘';
    var text_map_reverse = {};
    text_map_reverse['申请增配'] = '增';
    text_map_reverse['增配审核中'] = '增';
    text_map_reverse['减配审核中'] = '减';
    text_map_reverse['追加保证金'] = '追';
    text_map_reverse['提取盈利'] = '盈';
    text_map_reverse['申请续约'] = '续';
    text_map_reverse['终止操盘'] = '终';
    $("div.operate_div").hover(function() {
        var raw = $(this).children('span').text();
        var isInCheck = $(this).attr('isInCheck');
        if (isInCheck == 'true'){
            var checkType = $(this).attr('checkType');
            if (checkType == 'increase')
                $(this).children('span').text('增配审核中');
            else
                $(this).children('span').text('减配审核中');
        }else{
            $(this).children('span').text(text_map[raw]);
        }
        $(this).stop().css({width: "40px"}).animate({width: "+=70px"}, 10);
    }, function() {
        var raw = $(this).children('span').text();
        $(this).children('span').text(text_map_reverse[raw]);
        $(this).stop().css({width: "40px"});
    });
});

//追加保证金
function invest1(id,min){
    $('#plan_detail_invest1').attr('data',id);
    $('#plan_detail_invest1').attr('data-2',min);
    $('#plan_detail_invest1_min_insure').html(min);
    showBg("plan_detail_invest1");
}
function add_insure(usable) {
    var plan_id=$('#plan_detail_invest1').attr('data');
    var money_insure = parseFloat( $.trim( $('#InputMoneyInsure').val() ));
    var min_insure = parseFloat( $('#plan_detail_invest1').attr('data-2'));

    // var usable = <?= StringHelper::safeConvertIntToCent( $user_account->money_usable ) ?>;
    if (isNaN(money_insure) || money_insure <= 0) {
        alert('请填入正确的追加保证金金额。');
        $('#InputMoneyInsure').focus();
        return false;
    }
    if ( money_insure < min_insure) {
        alert('输入金额必须大于最低追加保证金额。');
        $('#InputMoneyInsure').focus();
        return false;
    }
    if ( money_insure > usable) {
        alert('余额不足。');
        $('#InputMoneyInsure').focus();
        return false;
    }

    $.post('/user/json-add-insure', {
        plan_id: plan_id,
        money_insure: money_insure
    }, function(resp) {
        var timeout = 3000;
        if (resp && ('code' in resp) && resp.code == 0) {
            alert('追加保证金申请提交成功，谢谢');
            timeout = 1000;
        }
        else {
            alert('该项目不允许追加保证金！');
        }

        setTimeout(function() {
            closeBg("plan_detail_invest1");
        }, timeout);
    }, 'json');
}
//提取利润
function invest2(id,counts,type){
    var plan_type = type;
    if(plan_type==1){
        alert("按天配资不可以提取收益！");
        return;
    }
    var profits_count =  counts;
    if(profits_count > 0){
         alert('已提交提取利润申请。');
        return false;
    }
    $('#plan_detail_invest2').attr('data',id);
    showBg("plan_detail_invest2");
}
function profits() {
    var plan_id=$('#plan_detail_invest2').attr('data');
    var money_profits = $.trim( $('#InputProfits').val() );
   if (money_profits % 1000 != 0){
       alert('请输入1000的倍数');
       return false;
   };
    if (isNaN(money_profits) || money_profits < 1000) {
        alert('请填入正确的提取利润金额。');
        $('#InputProfits').focus();
        return false;
    }
    $.post('/user/json-profits', {
        plan_id: plan_id,
        money_profits: money_profits
    }, function(resp) {
        var timeout = 3000;
        if (resp && ('code' in resp) && resp.code == 0) {
            alert('提取利润申请提交成功，谢谢');
            timeout = 1000;
        }
        else {
            alert('该项目不允许提交利润');
        }

        setTimeout(function() {
            closeBg("plan_detail_invest2");
        }, timeout);
    }, 'json');
}
//终止操盘
function stop_plan(id) {
    var ret = window.confirm('申请终止项目' + id + '？');
    if (ret) {
        $.getJSON('/user/json-stop-plan', {id: id}, function(resp) {
            if (resp && ('code' in resp) && resp.code == 0) {
                alert('申请终止成功');
                setTimeout(function() {
                    location.reload();
                }, 500);
            }
            else {
                
                alert(resp.msg);
            }
        });
    }
}

//续约
var check_renew = true;
function continue_contract(id, endDay){
    // var status = <?= $status = $plan->canRenew();?>; /*项目续约状态*/
    var myDate = new Date();
    var timestamp = myDate.getTime() / 1000; /*当前时间*/
    // var endDay = <?= $plan['end_time'] ?>;  /*到期时间戳*/
    check_renew = true;
    $('#plan_detail_invest4').attr('data',id);
    showBg("plan_detail_invest4");

    return check_renew;
}

function confirm_renew_charge(){
    var plan_id=$('#plan_detail_invest4').attr('data');
    if (check_renew) {
        $.post('/user/json-record-renew', {
            plan_id: plan_id,
            remark: $('#renew_remark').val()
        }, function(resp) {
            var timeout = 3000;
            if (resp && ('code' in resp) && resp.code == 0) {
                alert('续约申请提交成功，谢谢');
                timeout = 1000;
                location.reload();
            }
            else {
                var msg = ('data' in resp) && (typeof resp.data == 'string') ? resp.data : '续约申请提交失败，请稍后重试.';
                alert( msg );
            }

            setTimeout(function() {
                closeBg("plan_detail_invest4");
            }, timeout);
        }, 'json');
    }
}

function alert_disable(msg) {
    if (msg != '')
        $.showMessage(msg,[{ text : '我知道了', style : 'cancel btn', click : onclick}]);
}