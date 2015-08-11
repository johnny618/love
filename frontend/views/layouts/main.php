<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <meta name="keywords" content="专业娱乐">
    <meta name="description" content="专业娱乐">
    <?= Html::csrfMetaTags() ?>
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <?php $this->head() ?>
</head>
<body>


    <?php $this->beginBody() ?>

    <div class="wrap">
        <nav class="navbar navbar-default">
            <div class="container">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <a class="navbar-brand" href="<?php echo Url::to(['main/index'], true); ?>">
                        <span class="glyphicon glyphicon-home" aria-hidden="true"></span>&nbsp;首页
                    </a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <a class="navbar-brand" href="<?php echo Url::to(['site/index'], true); ?>">
                            <span class="glyphicon glyphicon-star-empty" aria-hidden="true"></span>&nbsp;空间
                        </a>
                    </ul>
                    <ul class="nav navbar-nav">
                        <a class="navbar-brand" href="<?php echo Url::to(['navigation/index'], true); ?>">
                            <span class=" glyphicon glyphicon-fire" aria-hidden="true"></span>&nbsp;导航
                        </a>
                    </ul>
                    <ul class="nav navbar-nav">
                        <a class="navbar-brand" href="<?php echo Url::to(['site/index'], true); ?>">
                            <span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>&nbsp;联系我们
                        </a>
                    </ul>
                    <ul class="nav navbar-nav">
                        <a class="navbar-brand" href="<?php echo Url::to(['site/index'], true); ?>">
                            <span class="glyphicon glyphicon-heart-empty" aria-hidden="true"></span>&nbsp;关于我们
                        </a>
                    </ul>
                    <form class="navbar-form navbar-left" role="search">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="按名字查找">
                        </div>
                        <button type="submit" class="btn btn-default">搜 索</button>
                    </form>
                    <ul class="nav navbar-nav navbar-right">
                        <?php if (Yii::$app->user->isGuest) { ?>
                            <li><a href="<?php echo Url::to(['site/login']); ?>">登 录</a></li>
                            <li><a href="<?php echo Url::to(['site/signup']); ?>">注 册</a></li>
                        <?php } else { ?>
                            <li><a href="<?php echo Url::to(['site/logout']); ?>"><?=  Yii::$app->user->identity->username ?> 退 出</a></li>
                        <?php } ?>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div>
            </div><!-- /.container-fluid -->
        </nav>


        <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>
        <p class="pull-right">一个充满爱的地方</p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
