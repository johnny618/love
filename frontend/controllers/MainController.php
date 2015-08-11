<?php
namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Main controller
 */
class MainController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => [],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(){
        return $this->render('index',['nav_title'=>$this->_nav_title()]);
    }

    const TAB = 'tab';
    const HREF = 'href';
    const TITLE = 'title';
    private function _nav_title(){
//        <a class="tab tab-hook s-bg20 g-fc5 g-fc5h curr curr-hook" data-tab="web" href="http://www.hao123.com" hidefocus="true">网页</a>
//            <a class="tab tab-hook g-fc0" data-tab="music" href="http://music.baidu.com" hidefocus="true">音乐</a>
//            <a class="tab tab-hook g-fc0" data-tab="video" href="http://v.baidu.com" hidefocus="true">视频</a>
//            <a class="tab tab-hook g-fc0" data-tab="image" href="http://image.baidu.com" hidefocus="true">图片</a>
//            <a class="tab tab-hook g-fc0" data-tab="tieba" href="http://tieba.baidu.com" hidefocus="true">贴吧</a>
//            <a class="tab tab-hook g-fc0" data-tab="zhidao" href="http://zhidao.baidu.com" hidefocus="true">知道</a>
//            <a class="tab tab-hook g-fc0" data-tab="news" href="http://news.baidu.com" hidefocus="true">新闻</a>
//            <a class="tab tab-hook g-fc0" data-tab="map" href="http://map.baidu.com" hidefocus="true">地图</a>
//            <a class="tab more g-fc0" style="" href="http://www.baidu.com/more/" hidefocus="true">更多&gt;&gt;</a>
        return [
            [self::TAB=>'web',self::HREF=>"http://www.baidu.com",self::TITLE=>'网页'],
            [self::TAB=>'music',self::HREF=>"http://music.baidu.com",self::TITLE=>'音乐'],
            [self::TAB=>'video',self::HREF=>"http://v.baidu.com",self::TITLE=>'视频'],
            [self::TAB=>'image',self::HREF=>"http://image.baidu.com",self::TITLE=>'图片'],
            [self::TAB=>'tieba',self::HREF=>"http://tieba.baidu.com",self::TITLE=>'贴吧'],
            [self::TAB=>'zhidao',self::HREF=>"http://zhidao.baidu.com",self::TITLE=>'知道'],
            [self::TAB=>'news',self::HREF=>"http://news.baidu.com",self::TITLE=>'新闻'],
            [self::TAB=>'map',self::HREF=>"http://map.baidu.com",self::TITLE=>'地图'],
            [self::TAB=>'',self::HREF=>"http://www.baidu.com/more/",self::TITLE=>'更多&gt;&gt;'],
        ];
    }

}
