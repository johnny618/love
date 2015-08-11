<?php
namespace frontend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Navigation controller
 */
class NavigationController extends BaseController
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
        return [
            [self::TAB=>'web',self::HREF=>"http://www.baidu.com/s",self::TITLE=>'网页'],
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
