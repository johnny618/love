<?php
/* @var $this yii\web\View */
$this->title = '主页';
?>
<style type="text/css">
    @font-face {
        font-family: digit;
        src: url('digital-7_mono.ttf') format("truetype");
    }

    .div_tianqi{
        margin-top: -35px;
    }
</style>

<div>
    <div class="bd_div">
        <ul class="nav nav-pills">
            <?php foreach($nav_title as $nav_title_key => $nav_title_val):?>
                <?php if ($nav_title_key == 0):?>
                    <li id="nav_title_li_<?= $nav_title_key?>" class="active" role="presentation"><a href='javascript:bd_type("<?= $nav_title_val['href']?>","<?= $nav_title_key?>")'><?= $nav_title_val['title']?></a></li>
                <?php else:?>
                    <li id="nav_title_li_<?= $nav_title_key?>" role="presentation"><a href='javascript:bd_type("<?= $nav_title_val['href']?>","<?= $nav_title_key?>")'><?= $nav_title_val['title']?></a></li>
                <?php endif;?>
            <?php endforeach;?>
        </ul>
        <form id="form_bd" class="navbar-form navbar-left" action="http://www.baidu.com/s" target="_blank" role="search">
            <img class="img-hook" src="http://s1.hao123img.com/res/images/search_logo/web.png" alt="百度首页" width="97" height="32">

            <div class="form-group">
                <div class="col-lg-6">
                    <div class="input-group" style="width: 500px;">
                        <input type="text" class="form-control" name="word" id="baidu" placeholder=""x autocomplete="off">
                        <div class="input-group-btn">
                            <ul class="dropdown-menu dropdown-menu-right" role="menu" style="padding-top: 0px; max-height: 375px; max-width: 800px; overflow: auto; width: auto; transition: 0.5s; -webkit-transition: 0.5s; min-width: 300px; left: -267px; right: auto;">
                            </ul>
                        </div>
                        <!-- /btn-group -->
                    </div>
                </div>
            </div>
            <button type="button" onclick="javascript:submit();" class="btn btn-default">百度一下</button>
        </form>
    </div>
    <div class="div_tianqi">
        <iframe allowtransparency="true" frameborder="0" width="385" height="96" scrolling="no" src="http://tianqi.2345.com/plugin/widget/index.htm?s=2&z=3&t=0&v=0&d=3&bd=0&k=000000&f=ff80c0&q=1&e=1&a=1&c=54511&w=385&h=96&align=center"></iframe>
    </div>
</div>
<script type="text/javascript">
    function bd_type(type_str,li_id){
        $('#form_bd').attr("action",type_str);
        $(".bd_div li").removeClass("active");
        $('#nav_title_li_'+li_id).attr("class","active");
    }
</script>



<div class="nav_daohang">
    <ul class="nav nav-tabs nav-pills">
        <li id="nav_daohang_li_1" role="presentation" class="active"><a href="javascript:showDiv(1)">网址大全</a></li>
    </ul>
</div>
<div class="panel-body">
    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion"
                       href="#collapseOne">
                        常用网址
                    </a>
                </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse in">
                <div class="panel-body">
                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred
                    nesciunt sapiente ea proident. Ad vegan excepteur butcher vice
                    lomo.
                </div>
            </div>
        </div>
        <div class="panel panel-success">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion"
                       href="#collapseTwo">
                        点击我进行展开，再次点击我进行折叠。第 2 部分--show 方法
                    </a>
                </h4>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse">
                <div class="panel-body">
                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred
                    nesciunt sapiente ea proident. Ad vegan excepteur butcher vice
                    lomo.
                </div>
            </div>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion"
                       href="#collapseThree">
                        点击我进行展开，再次点击我进行折叠。第 3 部分--toggle 方法
                    </a>
                </h4>
            </div>
            <div id="collapseThree" class="panel-collapse collapse">
                <div class="panel-body">
                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred
                    nesciunt sapiente ea proident. Ad vegan excepteur butcher vice
                    lomo.
                </div>
            </div>
        </div>
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion"
                       href="#collapseFour">
                        点击我进行展开，再次点击我进行折叠。第 4 部分--options 方法
                    </a>
                </h4>
            </div>
            <div id="collapseFour" class="panel-collapse collapse">
                <div class="panel-body">
                    Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred
                    nesciunt sapiente ea proident. Ad vegan excepteur butcher vice
                    lomo.
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function showDiv(id){
        $(".nav_daohang li").removeClass("active");
        $("#nav_daohang_li_"+id).attr("class","active");
    }
</script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/bootstrap-suggest.js"></script>
<script type="text/javascript">
    //百度搜索测试
    var baiduBsSuggest = $("#baidu").bsSuggest({
        allowNoKeyword: false, //是否允许无关键字时请求数据
        multiWord: true, //以分隔符号分割的多关键字支持
        separator: ",", //多关键字支持时的分隔符，默认为空格
        getDataMethod: "url", //获取数据的方式，总是从 URL 获取
        url: 'http://unionsug.baidu.com/su?p=3&t='+ (new Date()).getTime() +'&wd=', /*优先从url ajax 请求 json 帮助数据，注意最后一个参数为关键字请求参数*/
        jsonp: 'cb',/*如果从 url 获取数据，并且需要跨域，则该参数必须设置*/
        processData: function (json) {// url 获取数据时，对数据的处理，作为 getData 的回调函数
            var i, len, data = {value: []};
            if (!json || !json.s || json.s.length === 0) {
                return false;
            }
//            console.log(json);
            len = json.s.length;

            jsonStr = "{'value':[";
            for (i = 0; i < len; i++) {
                data.value.push({
                    word: json.s[i]
                });
            }
            data.defaults = 'baidu';

            //字符串转化为 js 对象
            return data;
        }
    });
    //淘宝搜索建议测试
    var taobaoBsSuggest = $("#taobao").bsSuggest({
        indexId: 2, //data.value 的第几个数据，作为input输入框的内容
        indexKey: 1, //data.value 的第几个数据，作为input输入框的内容
        allowNoKeyword: false, //是否允许无关键字时请求数据
        multiWord: true, //以分隔符号分割的多关键字支持
        separator: ",", //多关键字支持时的分隔符，默认为空格
        getDataMethod: "url", //获取数据的方式，总是从 URL 获取
        effectiveFieldsAlias:{Id: "序号", Keyword: "关键字", Count: "数量"},
        showHeader: true,
        url: 'http://suggest.taobao.com/sug?code=utf-8&extras=1&q=', /*优先从url ajax 请求 json 帮助数据，注意最后一个参数为关键字请求参数*/
        jsonp: 'callback',/*如果从 url 获取数据，并且需要跨域，则该参数必须设置*/
        processData: function(json){// url 获取数据时，对数据的处理，作为 getData 的回调函数
            var i, len, data = {value: []};

            if(!json || !json.result || json.result.length == 0) {
                return false;
            }

            console.log(json);
            len = json.result.length;

            for (i = 0; i < len; i++) {
                data.value.push({
                    "Id": (i + 1),
                    "Keyword": json.result[i][0],
                    "Count": json.result[i][1]
                });
            }
            //console.log(data);
            return data;
        }
    });

    $("form").submit(function(e) {
        return false;
    });
</script>
