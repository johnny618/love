<?php
/**
 * Created by PhpStorm.
 * User: JohnnyLin
 * Date: 2014/11/8
 * Time: 18:25
 */

namespace common\helpers;


class TimeHelper
{
    const DAY = 86400;

    // DateA 是否比 DateB 大于 30天
    public static function getLimitDay(){
        return 1 * self::DAY;
    }
    public static function isLT30Days($timeA, $timeB)
    {
        $timeA = self::zeroClockTimeOfDay($timeA);
        $timeB = self::zeroClockTimeOfDay($timeB);
        if ( $timeA - $timeB >= self::getLimitDay())
        {
            return true;
        }
        return false;
    }

    // 转换成当天0点的Linux时间戳
    public static function zeroClockTimeOfDay($time)
    {
        // 如果是linux时间戳
        if (is_numeric($time))
        {
            $date = date("Y-m-d",$time);
            return strtotime($date);
        }
        // 如果是日期函数
        else if (is_string($time)){
            $time = strtotime($time);
            return self::zeroClockTimeOfDay($time);
        }
        return false;
    }

    // 转换成当天24点的Linux时间戳
    public static function twentyFourTimeOfDay($time)
    {
        // 如果是linux时间戳
        if (is_numeric($time))
        {
            $date = date("Y-m-d",$time + self::DAY);
            return strtotime($date);
        }
        // 如果是日期函数
        else if (is_string($time)){
            $time = strtotime($time);
            return self::twentyFourTimeOfDay($time);
        }
        return false;
    }

    /*
    public static function Now($delay = ""){
        return strtotime($delay, time());
    }
    */

    public static function Now(){
        return time();
    }

    public static function Today(){
        return date("Y-m-d",self::Now());
    }

    public static function DataNow(){
        return date("Y-m-d H:i:s",self::Now());
    }

    // 获得时间的前一天
    public static function Yesterday($time = '')
    {
        if(empty($time))
        {
            $time = strtotime(self::Today());
        }
        else
        {
            if(is_string($time))
            {
                $time = strtotime($time);
            }
        }
        return date("Y-m-d", strtotime("-1 day", $time));
    }

    // 获得时间的后一天
    public static function Tomorrow($time = '')
    {
        if(empty($time))
        {
            $time = strtotime(self::Today());
        }
        else
        {
            if(is_string($time))
            {
                $time = strtotime($time);
            }
        }
        return date("Y-m-d", strtotime("+1 day", $time));
    }

    // 获得两个日期相差的天数
    public static function DiffDays($dateA, $dateB){
        return intval( ( strtotime($dateA) - strtotime($dateB) ) / self::DAY );
    }

    /**
     * 获取周末日期
     * @param $start_date
     * @param $days
     * @return int
     */
    static function get_weekend_day($start_date,$days){
        $weeks = 0 ;
        for($i = 0;$i <= $days ; $i++){
            $is_week = date('N',$start_date + 86400 * $i);
            if (in_array($is_week,array(6,7))){
                $weeks++;
                $days++;
            }
        }
        return $weeks;
    }

    /**
     *根据开始时间 结束时间 算出里面除去周末剩余的时间
     *@param start_time 开始日期
     *@param end_time 结束日期
     *@return 实际工作日天数
     */
    static function get_real_day($start_time,$end_time){
        $days = 0;
        for($time=$start_time;$time<$end_time;$time+=86400){
            if(!in_array(date('N',$time), array(6,7))){
                $days++;//如果当前时间不是周末 工作日就加一天
            }
        }

        return $days;
    }

    /**
     * 获取当天最早的时间戳
     * @return int
     */
    static function get_current_time(){
        return strtotime(date('Y-m-d',time()));
    }

    /**
     * 根据两个日期获取天数
     * @param $startdate
     * @param string $enddate
     */
    static function get_between_days($startdate,$enddate = ''){
        if (empty($startdate)){
            return false;
        }
        if (empty($enddate)){
            $enddate = time();
        }
        $startdate = self::zeroClockTimeOfDay($startdate);
        return ceil(abs($enddate - $startdate)/86400);
    }

    /**
     * 获取当月 第一天 0点0分的时间
     * @return [int] 时间戳
     */
    public static function get_month_first_day(){

        $time = time();
        $date = date('Y-m',$time);
        $time2 = strtotime($date);

        return $time2;
    }

    /**
     * 获取增配开始时间
     * @return [$start_time, $is_nextday]
     */
    public static function get_start_time($now=null) {
        if (empty($now)) {
            $now = time();
        }
        $week_n = date('N',$now);
        $is_afternoon = $now > strtotime(date('Y-m-d', $now) . ' 13:00:00');
        if (in_array($week_n, [6,7]) || ($week_n == 5 && $is_afternoon))
        {
            $start_time = TimeHelper::zeroClockTimeOfDay(strtotime("+" . (8-$week_n) . " day", $now));
            $is_nextday = true;
        } else if ($week_n < 5 && $is_afternoon) {
            $start_time = TimeHelper::zeroClockTimeOfDay(strtotime("+1 day", $now));
            $is_nextday = true;
        } else {
            $start_time = TimeHelper::zeroClockTimeOfDay($now);
            $is_nextday = false;
        }
        return ["start_time" => $start_time, "is_nextday" => $is_nextday];
    }
}