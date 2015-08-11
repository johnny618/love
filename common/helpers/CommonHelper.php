<?php

namespace common\helpers;

use Yii;
use yii\helpers\Json;
use common\helpers\ErrorCodeHelper;
use frontend\models\User;
use common\log\TLog;

class CommonHelper {

    /**
     * print a json resp
     * @param array $data
     * @param number $code
     * @param string $msg
     * @return string
     */
    public static function resp_json($data=[], $code=0, $msg='') {
        if (empty($msg)) {
            if (array_key_exists($code, ErrorCodeHelper::$err_map)) {
                $msg = ErrorCodeHelper::$err_map[ $code ];
            }
        }

        echo Json::encode([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ]);
    }

    /**
     * print a json resp
     * @param array $data
     * @param number $code
     * @param string $msg
     * @return string
     */
    public static function json_encode($code=0, $msg='',$data=[]) {
        if (empty($msg)) {
            if (array_key_exists($code, ErrorCodeHelper::$err_map)) {
                $msg = ErrorCodeHelper::$err_map[ $code ];
            }else{
                $msg = 'system error';
            }
        }

        echo Json::encode([
            'errno' => $code,
            'errmsg' => $msg,
            'data' => $data,
        ]);
    }

    /**
     * 获取通用错误信息
     * @param string $file
     * @param integer $line
     * @param string $func
     * @param string $msg
     * @return string
     */
    public static function getErrorMsg($file, $line, $func, $msg='') {
        return sprintf('[%s][%d][%s]%s', $file, $line, $func, $msg);
    }

    /**
     * 18位身份证号码组成：ddddddyyyymmddxxsp共18位，其中：其他部分都和15位的相同。年份代码由原来的2位升级到4位。最后一位为校验位。
     * 校验规则是：
     * （1）十七位数字本体码加权求和公式
     *     S = Sum(Ai * Wi), i = 0, ... , 16 ，先对前17位数字的权求和
     *     Ai:表示第i位置上的身份证号码数字值
     *     Wi:表示第i位置上的加权因子
     *     Wi: 7 9 10 5 8 4 2 1 6 3 7 9 10 5 8 4 2
     * （2）计算模
     *     Y = mod(S, 11)
     * （3）通过模得到对应的校验码
     *     Y: 0 1 2 3 4 5 6 7 8 9 10
     *     校验码: 1 0 X 9 8 7 6 5 4 3 2
     * 也就是说，如果得到余数为1则最后的校验位p应该为对应的0.如果校验位不是，则该身份证号码不正确。
     * @param string $id_card 身份证号
     * @return mixed 正确则返回生(birth)和性别(sex)；错误则返回false
     */
    public static function verify_id_card($id_card) {
        if (strlen($id_card) != 18) {
            return false;
        }

        $sex = '';
        $birth = substr($id_card, 6, 8);
        $birth_year = intval( substr($birth, 0, 4) );
        $birth_month = intval( substr($birth, 4, 2) );
        $birth_day = intval( substr($birth, 6, 2) );
        if ( $birth_year < 1949 || $birth_year > intval(date('Y', time()))
            || $birth_month < 0 || $birth_month > 12
            || $birth_day < 0 || $birth_day > 31) {

                return false; //生日非法
        }

        $powers = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 ];
        $parity_bits = [ '1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2' ];

        $num = substr($id_card, 0, 17);
        $parity_bit = substr($id_card, 17, 1); //校验位

        $power = 0;
        for ($i = 0; $i < 17; $i++) {
            $_num = intval( $num[$i] );
            if (intval( $_num ) < 0 || intval( $_num ) > 9) { // 校验每一位的合法性
                return false;
            }
            else {
                $power += $_num * $powers[$i]; // 加权

                // 设置性别
                if ($i == 16) {
                    $sex = $_num % 2 == 0 ? User::SEX_FEMALE : User::SEX_MALE;
                }
            }
        }

        $mod = $power % 11; // 取模
        if ($parity_bits[$mod] == $parity_bit) {
            return [
                'birth' => $birth,
                'sex' => $sex,
            ];
        }

        return false;
    }

    /**
     * 校验图像验证码
     * @param  [string]  $input用户输入的验证码
     * @param  [string] $sessionKey 验证码所在的action 比如(site/captcha)
     * @param  boolean $caseSensitive 是否忽略大小写
     * @return [type] 成功 返回true 失败返回false
     */
    public static function validateCode($input, $sessionKey, $caseSensitive=false)
    {
        $sessionKey = "__captcha/".$sessionKey;
        // TLog::info("sessionKey = ".$sessionKey);
        $code = Yii::$app->session[$sessionKey];
        unset(Yii::$app->session[$sessionKey]);
        $valid = $caseSensitive ? ($input === $code) : strcasecmp($input, $code) === 0;
        return $valid;
    }

}