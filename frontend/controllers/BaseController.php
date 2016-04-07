<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Html;

/**
 * Base controller
 * 
 * @property \yii\web\Request $request The request component.
 * @property \yii\web\Response $response The response component.
 * @property common\models\Client $client The Client model.
 */
abstract class BaseController extends Controller
{
	// 由于都是api接口方式，所以不启用csrf验证
	public $enableCsrfValidation = false;
	
	public function init()
	{
		parent::init();
	}
	

	/**
	 * 获得请求对象
	 */
	public function getRequest()
	{
		return Yii::$app->getRequest();
	}
	
	/**
	 * 获得返回对象
	 */
	public function getResponse()
	{
		return Yii::$app->getResponse();
	}
	
	/**
	 * 获得请求客户端信息
	 * 从request中获得，便于调试，有默认值
	 */
	public function getClient()
	{
		return Yii::$app->getRequest()->getClient();
	}

    public function params()
    {
        return array_merge($_GET, $_POST);
    }

    public function YiiParams($name='',$val=''){
        if (!empty($name)){
            $value = Yii::$app->request->post($name,$val);
            if (!empty($value)){  return $value; }
            return Yii::$app->request->get($name,$val);
        }
        return array_merge(Yii::$app->request->post(), Yii::$app->request->get());
    }
}