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
                    <input type="text" style="width: 500px;" maxlength="100" name="word" class="form-control" placeholder="">
            </div>
            <button type="submit" class="btn btn-default">百度一下</button>
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
<script type="text/javascript">
    function showDiv(id){
        $(".nav_daohang li").removeClass("active");
        $("#nav_daohang_li_"+id).attr("class","active");
    }
</script>
