//显示灰色 jQuery 遮罩层

var _div_id ;
function showBg(id) {
    _div_id = id;
    var bh = $("body").height();
    var bw = $("body").width();
    $("#fullbg").css({
        height:bh,
        width:bw,
        display:"block"
    });

    $("#"+_div_id).show();

    if(id != 'detail_invest_confirm4' && id != 'free-pass-expire' && id != 'free-pass-no-money' && id != 'free-pass-success' && id != 'free-pass-day-count' )
    $("#"+_div_id).css({display:"block",position:"fixed"});

    $("#"+_div_id).css("left", ($(window).width()-$("#"+_div_id).width())/2+$(window).scrollLeft()); 
}
function showB(id) {
    _div_id = id;
    var bh = $("body").height();
    var bw = $("body").width();
    $("#fullbg").css({
        height:bh,
        width:bw,
        display:"block"
    });

    $("#"+_div_id).show();

    if(id != 'detail_invest_confirm4' && id != 'free-pass-expire' && id != 'free-pass-no-money' && id != 'free-pass-success' && id != 'free-pass-day-count' )
    $("#"+_div_id).css({display:"block",position:"absolute"});

    $("#"+_div_id).css("left", "35%"); 
}
function showBgg(id) {
    _div_id = id;
    var bh = $("body").height();
    var bw = $("body").width();
    $("#fullbg").css({
        height:bh,
        width:bw,
        display:"block"
    });

    $("#"+_div_id).show();

    if(id != 'detail_invest_confirm4' && id != 'free-pass-expire' && id != 'free-pass-no-money' && id != 'free-pass-success' && id != 'free-pass-day-count' )
    $("#"+_div_id).css({display:"block",position:"absolute"});

    $("#"+_div_id).css("left", "25%"); 
}
function closeBg() {
    $("#fullbg").hide();
    $("#"+_div_id).hide();
}
