<?php

namespace common\helpers;

use yii\base\ErrorException;

class RedisHelper {
    const CHANNEL_FREQUENCY = 'frequency';

    public static $key_map = [
        //TODO
    ];

    public static $key_prefix_map = [
        'user_email_binding' => 'user:email:binding:',
        'user_mobile_binding' => 'user:mobile:binding:',
    ];

    public static $key_tpl_map = [];


    /**
     * 根据传参生成相应的redis key
     * 不符合格式，或者未定义的，一律抛异常
     * @param string $name key 名字
     * @param array $params 生成key的参数
     * @return string
     */
    public static function getKey($name, $params=[]) {
        if (array_key_exists($name, self::$key_map)) {
            return self::$key_map[ $name ];
        }
        else if (array_key_exists($name, self::$key_prefix_map)) {
            if (empty($params)) {
                throw new \ErrorException('empty params to get redis key');
            }
            $pre = self::$key_prefix_map[ $name ];
            return $pre . implode( $params );
        }
        else if ( array_key_exists($name, self::$key_tpl_map) ) {
            throw new ErrorException('none implementation.');
        }
        else {
            throw new ErrorException('get redis key failed');
        }
    }

}