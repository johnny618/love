/**
 * @author JohnnyLin
 * @datetime 2015-03-05 11:28
 */
//var strnum_reg = /[^a-zA-Z0-9]/g ;
//var number_reg = /[^0-9]/g;
//var string_reg = /[^a-zA-Z]/g;

/**
 * 输入正则验证 {数字}
 * @param {type} id
 * @returns {undefined}
 */
function perg_str(id,str_reg){
    $("#"+id).keyup(function(){
        $(this).val($(this).val().replace(str_reg,''));
    }).bind("paste",function(){  //CTR+V事件处理
        $(this).val($(this).val().replace(str_reg,''));
    });
}


function check_input(id,str_reg){
    if (str_reg == ''){
        if ($.trim($('#'+id).val()) == '' ){
            return false;
        }
    }else{
        return str_reg.test( $.trim($('#'+id).val()) );
    }
    return true;
}

//验证交易密码
function reglx_pwd(str){
    var reg =/^[0-9]\d{5}$/;
    return reg.test(str);
}

//验证15位身份证
function reglx_idcard15(str){
    var reg =/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/;
    return reg.test(str);
}

//验证18位身份证
function reglx_idcard18(str){
    var reg =/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X|x)$/;
    return reg.test(str);
}

//验证手机号
function reglx_phone(str){
    var reg =/^[1][358]\d{9}$/;
    return reg.test(str);
}

//验证银行卡
function reglx_bank(str){
    var reg = /^\d{16}|\d{19}$/;
    return reg.test(str);
}