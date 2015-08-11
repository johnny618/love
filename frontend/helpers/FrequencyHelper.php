<?php

namespace frontend\helpers;

use yii\base\ErrorException;
use yii\base\Event;
use yii\web\User;
use common\helpers\ErrorCodeHelper;

use frontend\models\User as FrontendUser;
use common\helpers\RedisHelper;
use common\helpers\TimeHelper;
use common\log\TLog;

class FrequencyHelper {
    const INTERVAL_EMAIL_BINDING = 180; //seconds
    const INTERVAL_MOBILE_BINDING = 60;


    /**
     * 根据参数，进行频率检查。
     * @param array $params
     * @param int $interval
     * @param string $class
     * @return boolean TRUE表示成功，其他都是失败
     */
    public static function checkLastTime(array $params, $interval, $class='_none_class_') {
        $lasttime = self::getLastTime($params, $class);
        return empty($lasttime) || FrequencyHelper::INTERVAL_EMAIL_BINDING < (time() - intval($lasttime));
    }

    /**
     * 获取上一次执行时间
     * @param array $params
     * @param string $class
     * @return
     */
    public static function getLastTime(array $params, $class='_none_class_') {
        if (empty($params)) {
            throw new \ErrorException('empty params.');
        }

        array_unshift($params, $class);
        array_unshift($params, RedisHelper::CHANNEL_FREQUENCY);
        $key = implode(':', $params);
        return \Yii::$app->redis->get( $key );
    }

    /**
     * 设置上一次执行时间
     * @param array $params 用来拼装redis key
     * @param string $val
     * @param string $class
     * @return
     */
    public static function setLastTime(array $params, $val, $class='_none_class_') {
        if (empty($params)) {
            throw new \ErrorException('empty params.');
        }

        array_unshift($params, $class);
        array_unshift($params, RedisHelper::CHANNEL_FREQUENCY);
        $key = implode(':', $params);
        return \Yii::$app->redis->setex( $key, 300, intval($val) ); //5 mins
    }

    /**
     * 锁一分钟
     * @param  [type]
     * @param  [type]
     * @return [type]
     */
    public static function lock($key,$time){

        $status = \Yii::$app->redis->setnx( $key, 1);
        if(!$status){
            return false;
        }

        \Yii::$app->redis->expire( $key, $time);
        return true;
    }

    /**
     * 获取当日计数次数 并且值加一
     * @param  [type]
     * @return [type]
     */
    public static function canSendMsm($key){
        $count = 100;
        $time = TimeHelper::DAY * 3;
        $value = \Yii::$app->redis->get($key);
        TLog::info("canSendMsm key = ".var_export($key,true)." value = ".var_export($value,true));
        if(empty($value)){
            $value = 0;
        }

        $status =  \Yii::$app->redis->setex( $key, $time,  ++$value);
        if(!$status){
            return false;
        }
        TLog::info("canSendMsm after count = ".var_export(\Yii::$app->redis->get($key),true));
        return ($value<$count)?true:false;
    }

}