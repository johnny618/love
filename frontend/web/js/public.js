/**
 * Created by johnny on 3/4/15.
 */
function getAmountDesc(amount){
    var one_thousand = 1000;
    var ten_thousand = 10 * one_thousand;
    // 大于1万
    if (amount >= ten_thousand) {
        amount_desc = ( amount / ten_thousand).toFixed(1).replace('.0',"") + "万元";
    }
    //else if (amount >= one_thousand) {
    //    amount_desc = parseInt( amount / one_thousand ) + "千";
    //}
    else {
        amount_desc = parseInt( amount ) + "元";
    }
    return amount_desc;
}

function getFormat(s, type){
//    增加千位符
        if (/[^0-9\.]/.test(s))
            return "0";
        if (s == null || s == "")
            return "0";
        s = s.toString().replace(/^(\d*)$/, "$1.");
        s = (s + "00").replace(/(\d*\.\d\d)\d*/, "$1");
        s = s.replace(".", ",");
        var re = /(\d)(\d{3},)/;
        while (re.test(s))
            s = s.replace(re, "$1,$2");
        s = s.replace(/,(\d\d)$/, ".$1");
        if (type == 0) {// 不带小数位(默认是有小数位)
            var a = s.split(".");
            if (a[1] == "00") {
                s = a[0];
            }
        }
        return s;

}

