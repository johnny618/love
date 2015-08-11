<?php

namespace frontend\helpers;

use yii\base\ErrorException;
use yii\base\Event;
use yii\web\User;

use frontend\models\User as FrontendUser;

class SessionHelper {

    /**
     * 注册登录事件
     * @throws \Exception
     * @return boolean
     */
    public static function userLoginAfter() {
        Event::on(User::className(), User::EVENT_AFTER_LOGIN, function($event) {
            // user identity
            if (isset($event->identity) && ( $event->identity instanceof \common\models\User )) {
                $user = $event->identity;
                $attrs = [
//                    FrontendUser::SESSION_FLAG_STATUS, //当前状态
//                    FrontendUser::SESSION_FLAG_EMAIL_BANDING, //邮箱绑定
//                    FrontendUser::SESSION_FLAG_MOBILE_BANDING, //手机绑定
//                    FrontendUser::SESSION_FLAG_REAL_VERIFY, //实名认证
//                    FrontendUser::SESSION_FLAG_CARD_BINDING, //卡绑定
                ];
                foreach($attrs as $_attr) {
                    \Yii::$app->session->set($_attr, isset($user->$_attr) ? $user->$_attr : false );
                }
            }

            return true;
        });
    }

}