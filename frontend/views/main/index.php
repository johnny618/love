<?php
/* @var $this yii\web\View */
$this->title = '主页';
?>
<style>
    .index_banner_img {
        width:100%;
        height: 500px;;
    }
</style>
<link rel="stylesheet" type="text/css" href="/css/index/default.css">
<div id="myCarousel" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
        <li data-target="#myCarousel" data-slide-to="3"></li>
    </ol>
    <div class="carousel-inner">
        <div class="item active">
            <div class="index_banner_img">
                <img src="/images/index/11.jpg"  class="index_banner_img"  data-src=" " alt="First slide">
            </div>
            <div class="container">
                <div class="carousel-caption">
                    <h1>
                        Example headline.</h1>
                    <p>
                        Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi
                        porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                    <p>
                        <a class="btn btn-lg btn-primary" href="#" role="button">Sign up today</a></p>
                </div>
            </div>
        </div>
        <div class="item">
            <div class="index_banner_img">
                <img src="/images/index/22.jpg"  class="index_banner_img"  data-src="" alt="Second slide">
            </div>
            <div class="container">
                <div class="carousel-caption">
                    <h1>
                        Another example headline.</h1>
                    <p>
                        Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi
                        porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                    <p>
                        <a class="btn btn-lg btn-primary" href="#" role="button">Learn more</a></p>
                </div>
            </div>
        </div>
        <div class="item">
            <div class="index_banner_img">
                <img src="/images/index/33.jpg" class="index_banner_img"  data-src="" alt="Third slide">
            </div>
            <div class="container">
                <div class="carousel-caption">
                    <h1>
                        One more for good measure.</h1>
                    <p>
                        Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi
                        porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                    <p>
                        <a class="btn btn-lg btn-primary" href="#" role="button">Browse gallery</a></p>
                </div>
            </div>
        </div>
        <div class="item">
            <div class="index_banner_img">
                <img src="/images/index/44.jpg"  class="index_banner_img"  data-src="" alt="Third slide">
            </div>
            <div class="container">
                <div class="carousel-caption">
                    <h1>
                        One more for good measure.</h1>
                    <p>
                        Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi
                        porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                    <p>
                        <a class="btn btn-lg btn-primary" href="#" role="button">Browse gallery</a></p>
                </div>
            </div>
        </div>
    </div>
    <a class="left carousel-control" href="#myCarousel" data-slide="prev"><span class="glyphicon glyphicon-chevron-left">
	</span></a><a class="right carousel-control" href="#myCarousel" data-slide="next"><span
            class="glyphicon glyphicon-chevron-right"></span></a>
</div>
<!-- /.carousel -->
<script src="/js/bootstrap.min.js"></script>

<div style="text-align:center;margin:50px 0; font:normal 14px/24px 'MicroSoft YaHei';">
</div>