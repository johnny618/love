<?php
/* @var $this yii\web\View */
$this->title = 'Home';
?>
<style type="text/css">
    @font-face {
        font-family: digit;
        src: url('digital-7_mono.ttf') format("truetype");
    }
</style>

<link href="/css/home/default.css" type="text/css" rel="stylesheet">
<script type="text/javascript" src="/js/css/garden.js"></script>
<script type="text/javascript" src="/js/css/functions.js"></script>

<body>

<div id="mainDiv">
    <div id="content">
        <div id="code">
            <span class="comments">/**</span><br />
            <span class="space"/><span class="comments">*2015—08-08</span><br />
            <span class="space"/><span class="comments">*/</span><br />
            Boy name = <span class="keyword">Mr</span> JohnnyLin<br />
            Girl name = <span class="keyword">Mrs</span> Sara<br />
            <span class="comments"> Fall in love river. </span><br />
            The boy love the girl;<br />
            <span class="comments"> They love each other.</span><br />
            The girl loved the boy;<br />
            <span class="comments"> AS time goes on.</span><br />
            The boy can not be separated the girl;<br />
            <span class="comments"> At the same time.</span><br />
            The girl can not be separated the boy;<br />
            <span class="comments"> Both wind and snow all over the sky.</span><br />
            <span class="comments"> Whether on foot or 5 kilometers.</span><br />
            <span class="keyword">The boy</span> very <span class="keyword">happy</span>;<br />
            <span class="keyword">The girl</span> is also very <span class="keyword">happy</span>;<br />
            <span class="placeholder"/><span class="comments"> Whether it is right now</span><br />
            <span class="placeholder"/><span class="comments"> Still in the distant future.</span><br />
            <span class="placeholder"/>The boy has but one dream;<br />
            <span class="comments"> The boy wants the girl could well have been happy.</span><br />
            <br>
            <br>
            I want to say:<br />
            <span class="placeholder"/><span class="keyword">Baby, I love you forever ...</span><br />
        </div>
        <div id="loveHeart">
            <canvas id="garden"></canvas>
            <div id="words">
                <div id="messages">
                    亲爱的，这是我们相爱在一起的时光。
                    <div id="elapseClock"></div>
                </div>
                <div id="loveu">
                    爱你直到永永远远。<br/>
                    <div class="signature">- 爱你的人</div>
                </div>
            </div>
        </div>
    </div>
    <div id="copyright">
        <button type="button" class="btn btn-danger" onclick="javascript:goMain();"><span class="glyphicon glyphicon-home" aria-hidden="true"></span>&nbsp;进入首页</button>
    </div>
</div>

<script type="text/javascript">
    var mainUrl = '<?php echo \yii\helpers\Url::to("/main/index")?>';
    var offsetX = $("#loveHeart").width() / 2;
    var offsetY = $("#loveHeart").height() / 2 - 55;
    var together = new Date();
    together.setFullYear(2015, 7, 3);
    together.setHours(22);
    together.setMinutes(0);
    together.setSeconds(0);
    together.setMilliseconds(0);

    function goMain(){
        location.href = mainUrl;
    }

    if (!document.createElement('canvas').getContext) {
        var msg = document.createElement("div");
        msg.id = "errorMsg";
        msg.innerHTML = "Your browser doesn't support HTML5!<br/>Recommend use Chrome 14+/IE 9+/Firefox 7+/Safari 4+";
        document.body.appendChild(msg);
        $("#code").css("display", "none");
        $("#copyright").css("position", "absolute");
        $("#copyright").css("bottom", "10px");
        document.execCommand("stop");
    } else {
        setTimeout(function () {
            startHeartAnimation();
        }, 5000);

        timeElapse(together);
        setInterval(function () {
            timeElapse(together);
        }, 500);

        adjustCodePosition();
        $("#code").typewriter();
    }
</script>

</body>