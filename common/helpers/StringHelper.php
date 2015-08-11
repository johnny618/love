<?php

namespace common\helpers;
use yii;
use common\models\BankConfig;
use yii\base\ErrorException;

class StringHelper extends \yii\helpers\StringHelper {

    const ONE_MONTH = 30;

    const YEAR_DAYS = 365;

    const KEY_ENCRYPT = 'nowamagic';

    /**
     * 模糊手机号
     * 比如：13917883434 变成 139****3434
     */
    public static function blurPhone($phone) {
        return substr( $phone, 0, 3 ) . '****' . substr( $phone, 7 );
    }

    /**
     * 模糊真名，比如：林佳神 变成 *佳神
     * @param string $name
     * @return string
     */
    public static function blurName($name) {
        return '*' . mb_substr( $name, 1, 8, 'UTF-8' );
    }

    /**
     * 模糊银行卡
     * 比如：6224 8851 1234 4568 变成 6224 **** 4568
     */
    public static function blurCardNo($card_no) {
        $start_pos = strlen( $card_no ) - 4;
        return substr( $card_no, 0, 4 ) . ' **** ' . substr( $card_no, $start_pos );
    }

    /**
     * 安全的将“元”转化成“分”
     * 比如：10.01 变成 1001
     */
    public static function safeConvertCentToInt($num) {
        return intval( bcmul( floatval( $num ), 100 ) );
    }

    /**
     * 转化利率，为存入数据库做准备
     * 比如：0.01 变成 100
     */
    public static function ToDbRate($rate) {
        return intval( bcmul( floatval( $rate ), 10000 ) );
    }

    /**
     * 转化滞纳金利率，为存入数据库做准备
     * 比如：0.2 变成 0.2*10000*30
     */
    public static function ToDbLateRate($rate) {
        return intval( bcmul( floatval( $rate ), 10000*30 ) );
    }

    /**
     * 转化杠杆，为存入数据库做准备
     * 比如：0.1 变成 1
     */
    public static function ToDbPower($power) {
        return intval( bcmul( floatval( $power ), 10 ) );
    } 

    /**
     * 安全利率
     * 比如：0.01 变成 1.00
     */
    public static function safeConvertCentToDouble($num) {
        return sprintf( '%.2f', (floatval( $num )*100)  ) ;
    }

    /**
     * 安全的将“分”转化成“元”
     * 比如：1001 变成 10.01
     */
    public static function safeConvertIntToCent($num) {
        return sprintf( '%.2f', $num / 100 );
    }

    /**
     * 输入的月份数变成天数
     *
     * @param $numberM 月份数
     */
    public static function monthToDays($numberM) {
        if ($numberM < 12) {
            return intval( $numberM * self::ONE_MONTH );
        }
        $years = intval( $numberM / 12 );
        $months = intval( $numberM % 12 );
        return intval( self::YEAR_DAYS * $years + self::ONE_MONTH * $months );
    }

    /**
     * 生成唯一ID
     * @return string
     */
    public static function generateUniqid() {
        $prefix = rand( 10000, 99999 );
        return uniqid( $prefix );
    }

    /**
     * 生成唯一ID
     * @return string
     */
    public static function generateMd5() {
        $prefix = rand( 10000, 99999 );
        return substr(md5( $prefix ),4, 12);
    }

    /**
     * 加密字符串
     * @param string $string
     * @param string $key
     * @return string
     */
    static public function encrypt($string, $key = '') {
        $key = md5( empty($key) ? self::KEY_ENCRYPT : $key );
        $key_length = strlen( $key );

        $string = substr( md5( $string . $key ), 0, 8 ) . $string;
        $string_length = strlen( $string );

        $rndkey = $box = array ();
        $result = '';
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord( $key[$i % $key_length] );
            $box[$i] = $i;
        }
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr( ord( $string[$i] ) ^ ($box[($box[$a] + $box[$j]) % 256]) );
        }

        return str_replace( '=', '', base64_encode( $result ) );
    }

    /**
     * 解密字符串
     * @param string $string
     * @param string $key
     * @return string
     */
    static public function decrypt($string, $key = '') {
        $key = md5( empty($key) ? self::KEY_ENCRYPT : $key );
        $key_length = strlen( $key );

        $string = base64_decode( $string );
        $string_length = strlen( $string );

        $rndkey = $box = array ();
        $result = '';
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord( $key[$i % $key_length] );
            $box[$i] = $i;
        }
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr( ord( $string[$i] ) ^ ($box[($box[$a] + $box[$j]) % 256]) );
        }

        if (substr( $result, 0, 8 ) == substr( md5( substr( $result, 8 ) . $key ), 0, 8 )) {
            return substr( $result, 8 );
        }

        throw new ErrorException('decrypt failed');
    }

    /**
     * 删除银行卡中的空格
     * @param string $bank_card
     * @return mixed|boolean
     */
    public static function trimBankCard($bank_card) {
        $bank_card = str_replace( " ", '', $bank_card );
        if (preg_match( '/^[0-9]{10,24}$/', $bank_card )) {
            return $bank_card;
        }
        return false;
    }

    /**
     * 转换金额加上“千”，“万”字样
     * @param intger $amount 基数元
     * @return string
     */
    public static function getAmountDesc($amount) {
        $one_thousand = 1000;
        $ten_thousand = 10 * $one_thousand;
        // 大于1万
        if ($amount >= $ten_thousand) {
            $amount_desc = intval( $amount / $ten_thousand ) . "万";
        }
//         else if ($amount >= $one_thousand) {
//             $amount_desc = intval( $amount / $one_thousand ) . "千";
//         }
        else {
            $amount_desc = intval( $amount ) . "元";
        }
        return $amount_desc;
    }

    /**
     * 获取下一个工作日
     * @param int $time 时间戳
     * @param boolean $next 明儿？
     * @return int 下一个工作日的时间戳
     */
    public static function workDay($time=NULL, $next=false) {
        if (empty($time)) {
            $time = time();
        }

        $day = date("w", $time);
        if ($day == 5) {
            $append = $next ? 3 : 0;
            return strtotime( date('Y-m-d H:i:s', $time + 86400 * $append) );
        }
        else if ($day == 6) {
            $append = 2;
            return strtotime( date('Y-m-d H:i:s', $time + 86400 * $append) );
        }
        else { // 0-4
            $append = $next ? 1 : 0;
            return strtotime( date('Y-m-d H:i:s', $time + 86400* $append ) );
        }
    }

    /**
     * 获取当前时间是否符合当日下单
     */
    public static function work_current_day() {
        $time = time();
        $day = date("w", $time);
        if (in_array($day,[0,6,7])){
            return false;
        }
        $hour = date("H", $time);
        if ($hour >= 13){
            return false;
        }
        return true;
    }

    /**
     * 根据入参，生成随机数字
     * @param int $length
     * @return int
     */
    public static function randomNum($length) {
        $result = '';

        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand( 0, 9 );
        }

        return $result;
    }
    
     /**
     * 根据入参，切割字符串，获取简单的order_id
     * @param int $num
     * @return int
     */
     public static function orderId($num) {
        $result = explode("_",$num);

        if(empty($result)||empty($result[1])){
            $order_id = "未知";
        }else{
            $order_id = $result[1]; 
        }
        return $order_id;
    }
    
}

