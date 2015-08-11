<?php

namespace common\helpers;

use yii\helpers\Json;

class ErrorCodeHelper {
    const CODE_SUCCESS = 0;

    const CODE_SYS_ERROR = 1000;
    const CODE_INPUT_INVALID = 1001;

    const CODE_NOT_FOUND = 2001;
    const CODE_CAPTCHA_ERROR = 2002;

    const CODE_USR_EMAIL_BINDING_FQ = 3001;
    const CODE_USR_MOBILE_BINDING_FQ = 3002;

    const CODE_PLAN_CANNOT_RENEW = 4001;

    const CODE_NO_LOGIN = 1002;
    const CODE_NO_NEW_USER = 1003;
    const CODE_NO_MONEY = 1004;
    const CODE_ALL_COUNT = 1005;
    const CODE_DAY_COUNT = 1006;
    const CODE_POWER_ERROR = 1007;
    const CODE_NO_PROJECT = 1008;
    const CODE_TIME_ERROR = 1009;
    const CODE_POWER_COUNT = 1010;
    const CODE_ACTIVITY_COUNT = 1011;
    const CODE_MONEY_NOT_ENOUGH = 1012;


    static $err_map = [
        self::CODE_SUCCESS => '成功',

        self::CODE_SYS_ERROR => '系统错误',
        self::CODE_INPUT_INVALID => '输入错误',

        self::CODE_NOT_FOUND => '未找到',
        self::CODE_CAPTCHA_ERROR => '验证码错误！',

        self::CODE_USR_EMAIL_BINDING_FQ => 'sending email error, frequency limit',
        self::CODE_USR_MOBILE_BINDING_FQ => 'sending sms error, frequency limit',

        self::CODE_PLAN_CANNOT_RENEW => '当前订单不能续约',
        self::CODE_NO_LOGIN => '请先登陆账户',
        self::CODE_NO_NEW_USER => '不是新用户',
        self::CODE_NO_MONEY => '余额不足',
        self::CODE_ALL_COUNT => '用户次数已满',
        self::CODE_DAY_COUNT => '每日限额已满',
        self::CODE_POWER_ERROR => '杠杆不合法',
        self::CODE_NO_PROJECT => '项目不存在',
        self::CODE_MONEY_NOT_ENOUGH => '账户余额不足',

        self::CODE_POWER_COUNT => '该杠杆人数已满',
        self::CODE_ACTIVITY_COUNT => '当前配资利率已过期，请重新确认配资方案',

        self::CODE_TIME_ERROR => '当前时间段不允许配资',

    ];


}
