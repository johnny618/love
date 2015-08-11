<?php
/**
 * @file 配资相关函数
 */

namespace common\helpers;
use Yii;
use common\models\PlanSetting;
use common\models\PlanRate;
use common\models\PlanRecord;

use common\models\Project;
use common\models\Auth;
use common\models\RateRule;
use common\helpers\ErrorCodeHelper;
use common\models\Homs;
use common\models\Ths;
use common\models\UserHoms;
use common\models\User;
use yii\db\Query;

class PlanHelper {

    public static $warning_val = 1.1;
    public static $force_val = 1.07;

    //默认值
    static $daily_interest_dft = [
        15 => [ //1-15 天
            99999 => 0.035, //1-99999, 10w以下
            999999 => 0.04, //100000-999999, 100w以下
            9999999 => 0.045, //1000000-9999999, 1000w以下
        ],
    ];

    //默认值
    static $monthly_interest_dft = [
        2 => [ //1-2月
            99999 => 0.019, //1-99999, 10w以下
            999999 => 0.018, //100000-999999, 100w以下
            9999999 => 0.017, //1000000-9999999, 1000w以下
            99999999 => 0.016, //1亿以下，再大就报异常了
        ],
        6 => [ //3-6月
            99999 => 0.018, //1-99999, 10w以下
            999999 => 0.017, //100000-999999, 100w以下
            9999999 => 0.016, //1000000-9999999, 1000w以下
            99999999 => 0.015, //1亿以下，再大就报异常了
        ],
    ];

    /**
     * 对比常规利率和活动利率 返回最终利率
     * @param $project_id
     * @param $money_op
     * @param $interval
     * @param $power
     * @return float|int
     */
    public static function getRetRate($project_id, $money_op, $interval, $power){
        $rates = self::getRates($project_id, $money_op, $interval, $power);
        $rateActivity = floatval(self::getRatesActivity($project_id, $money_op, $interval, $power)[RateRule::RATE]);
        if ($rateActivity > 0 && $rateActivity < $rates){
            $rates = $rateActivity;
        }
        return $rates;
    }

    /**
     * 根据配资类型，时间长度，配资金额返回相应的利率
     * @param  [type] $project_id  项目id
     * @param unknown $interval 时间跨度
     * @param unknown $amount 配资金额，单位[元]
     * @return float 利率
     */
    public static function getRates($project_id, $money_op, $interval, $power) {
        $rates = RateRule::getRates($project_id);
        $project = Project::getInfo($project_id);

        $power=$power*10;
        $money_op=$money_op*100;
        $money_loan = $money_op*$power/($power+10);//借款金额
        $ret = $project['rate'];

        if(empty($rates[$project['id']])){
            //没有设置项目利率规则时 特殊处理
             return $ret/10000;
        }

        $rates = $rates[$project['id']];

        foreach ($rates as $_power => $powerValue) {
            if ($power <= $_power) {
                foreach ($powerValue as $_minper => $minperValue) {
                    if($interval >= $_minper){
                        foreach ($minperValue as $_minmoney => $minmoneyValue) {
                            if ($money_loan >= $_minmoney) {
                                foreach ($minmoneyValue as $_rate => $des) {
                                    if(!$ret || $_rate<$ret){
                                        $ret = $_rate;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $ret/10000;
    }

    /**
     * 根据配资类型，时间长度，配资金额返回相应的利率
     * @param  [type] $project_id  项目id
     * @param unknown $interval 时间跨度
     * @param unknown $amount 配资金额，单位[元]
     * @return float 利率
     */
    public static function getRatesActivity($project_id, $money_op, $interval, $power) {
        $rates = RateRule::getRatesActivity($project_id);
        $project = Project::getInfo($project_id);

        $power=$power*10;
        $money_op=$money_op*100;
        $money_loan = $money_op*$power/($power+10);//借款金额
        $ret = $project['rate'];

        if(empty($rates[$project['id']])){
            //没有设置项目利率规则时 特殊处理
            return $ret/10000;
        }
        $result = [];
        $result[RateRule::RATE] = $ret/10000;
        $result[RateRule::NUMBER] = 0;
        $result[RateRule::ID] = 0;
        $rates = $rates[$project['id']];
        foreach ($rates as $_power => $powerValue) {
            if ($power <= $_power) {
                foreach ($powerValue as $_minper => $minperValue) {
                    if($interval >= $_minper){
                        foreach ($minperValue as $_minmoney => $minmoneyValue) {
                            if ($money_loan >= $_minmoney) {
                                foreach ($minmoneyValue as $_rate => $des) {
                                    $datetime = date('Y-m-d H:i:s',time());
                                    $time = date('H:i:s',time());
                                    if (($datetime >= $des['activity_st'] &&  $datetime <= $des['activity_ed']) &&
                                        ($time >= $des['time_st'] &&  $time <= $des['time_ed']) ){
                                        if(!$ret || $_rate<$ret){
                                            $ret = $_rate;
                                            $result[RateRule::RATE] = $ret/10000;
                                            $result[RateRule::NUMBER] = $des['number'];
                                            $result[RateRule::ID] = $des['id'];
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 警戒线 
     * @param int $money_op
     * @param int $power
     */
    public static function getWarningLine($money_op, $power) {

        $ret = $money_op*(self::$warning_val - 1/($power+1));

        return round($ret,2);
    }

    /**
     * 平仓线 
     * @param int $money_op
     * @param int $power
     */
    public static function getForceLine($money_op, $power) {

        $ret = $money_op*(self::$force_val - 1/($power+1));

        return round($ret,2);
    }

    /**
     * 获取总共的费用
     * @param  [type] $project_id  项目id
     * @param  [type] $interval     [description]
     * @param  [type] $money_insure [description]
     * @param  [type] $power        [description]
     * @return [type]               [description]
     */
    public static function getTotalFee($project_id, $interval, $money_op, $power, $rate=null) {
        if(empty($rate)){
            $rate = self::getRates($project_id, $money_op, $interval, $power);
        }
        $project_type = Project::getVal($project_id,'type');
        switch(intval($project_type)) {
            case Project::TYPE_DAY:
                $fee = ($money_op/($power+1))*$power*($rate/100)/30;//先算出一月的利息费再算每一天的利息费
                $fee = round($fee,2);
                return $fee*$interval;

            case Project::TYPE_MONTH:
                $fee = ($money_op/($power+1))*$power*($rate/100);//先算出一月的利息费再算每一天的利息费
                $fee = round($fee,2);
                return $fee*$interval;
            default:
                return false;
        }
    }

    // /**
    // *通过操盘资金获取利息
    // */
    // public static function getFeeByMoneyOp($type, $interval, $money_op, $power,$rate=null){
    //     if ($type == PlanRecord::TYPE_DAY) {
    //         $amount = $money_op*(1-$power);
    //     }
    //     elseif ($type == PlanRecord::TYPE_MONTH){
    //         $amount =($money_op / $power)*($power-1) ;
    //     }
    //     else {
    //         throw new ErrorException('type not support');
    //     }
    //     if(empty($rate)){
    //         $rate = self::getRate( $type, $interval, $amount ); 
    //     }
    //     if ($type == PlanRecord::TYPE_DAY) {
    //         $fee = $amount * $rate * $interval  / PlanRecord::DAYS_PER_MONTH;
    //     }
    //     elseif ($type == PlanRecord::TYPE_MONTH) {
    //         $fee = $amount * $rate * $interval;
    //     }
    //     return $fee;
    // }

    /**
     * 计算操盘金
     * @param $money_insure
     * @param $power
     * @param int $type
     * @return float
     */
    public static function getMoneyOP( $money_insure, $power) {

        return $money_insure * ($power+1);
    }


    public static function getPlanSwitch(){
        return \Yii::$app->redis->get( 'plan_switch') ? false : true;    //true为关闭状态
    }

    public static function setPlanSwitch($isSwitch = true){
        return \Yii::$app->redis->setex( 'plan_switch', 86400,  $isSwitch); //一天
    }

    public static function getRedisVal($key=''){
        $value = \Yii::$app->redis->get($key);
        return $value ? $value : 0;    //true为关闭状态
    }

    public static function setRedisVal($key='',$val=0,$time='cur'){
        if ($time === 'cur'){
            $time = strtotime(date('Y-m-d',strtotime("+1 day"))) - time();
            $time = TimeHelper::DAY * 3;
        }
        return \Yii::$app->redis->setex( $key, $time,  $val);
    }

    /**
     * REDIS加锁
     * @param string $key
     * @param int $val
     * @return mixed
     */
    public static function setRedisNXVal($key='r_lock',$val=0){
        $status = \Yii::$app->redis->setnx( $key, $val);
        \Yii::$app->redis->expire( $key, 3);
        return $status;
    }

    public static function delRedisNXVal($key='r_lock'){
        return \Yii::$app->redis->del( $key);
    }

    /**
     * 根据旧的杠杆获取新的杠杆
     * @param power 从数据库中读的旧的杠杆
     */
    public static function getNewPower($power){
        $t_power = $power;
        if($power>=10){
            $t_power = $power/10;//新比率
        }else if($power>1){//老的按月配资
            //原来减一
            $t_power = $power-1;
        }else if($power<1){//老的按天配资
            $t_power = 1/$power -1;
        }
        return $t_power;
    }
    /**
     * 根据旧的利率获取新的利率
     * @param rate 旧的利率 单位是百分比 分的1/100
     *  @return rate利率单位是分       利率
     */
    public static function getNewRate($rate){

        return ($rate>1)?($rate/10000):($rate*100);
    }

    /**
     * 获取起始保证金
     * @param $op_money 操盘金单位为分
     * @param $power 杠杆已经乘 10
     *  返回 起始保证金 单位是分
     */
    public static function getMoneyInsure($op_money,$power){
        if (empty($op_money) || empty($power)){
            return 0;
        }
        $power = self::getNewPower($power);

        return ($op_money / ($power+1));
    }

    /**
     * 用户身份验证
     * @param  [type] $type 校验类型
     * @return [type]       成功返回 true 失败返回 原因
     */
    public static function auth($projectId,$data=''){
        
        $auth = Auth::find()->where(['project_id'=>$projectId,'state'=>Auth::STATE_USE])->asArray()->all();
        if(!$auth){
            Yii::info("no need check !");
            return true;
        }
        foreach ($auth as $key => $value) {

            switch (intval($value['type'])) {

                    //判断是否有新用户校验
                case Auth::TYPE_NEW_USER:
                    if(!self::isNewUser()){
                        return ErrorCodeHelper::CODE_NO_NEW_USER;
                    }
                    break;
                
                default:
                    # code...
                    break;
            }
            return true;

        }

       
    }

    /**
     * 判断是否有新用户校验
     * @return boolean [description]
     */
    public static function isNewUser(){
        $user_id = Yii::$app->user->id;
        //判断之前是否有投资过这个项目
        // $plans = PlanRecord::find()->where(['user_id'=>$user_id])->asArray()->all();
        $plans = PlanRecord::find()->where('user_id = '.$user_id.' and status >=0 ')->asArray()->all();
        Yii::info("user_id = ".$user_id." PlanRecord count = ".count($plans));
        if(empty($plans)){
            return true;
        }
        return false;
    }


    /**
     * 获取Account
     * 创建人 creator
     * @return \yii\db\ActiveQuery
     */
    public static function getAccount($account_type='',$account_id='',$homs_account='') {
        $account = '';
        if ($account_id && in_array($account_type,array_keys(PlanRecord::$_type_account))){
            if ($account_type == PlanRecord::ACCOUNT_HOMS){
                $account = Homs::getDataById($account_id);
            }else if ($account_type == PlanRecord::ACCOUNT_DZH){
                $account = UserHoms::getDataById($account_id);
            }else if ($account_type == PlanRecord::ACCOUNT_YJHOMS){
                $account = Homs::getDataById($account_id,Homs::TYPE_YJ);
            }else if ($account_type == PlanRecord::ACCOUNT_THS){
                $account = Ths::getDataById($account_id);
            }
        }else if($account_type == 0 && $homs_account){
            $account = $homs_account;
        }
        return $account ? $account : NULL;
    }


    // /**
    //  * 根据parent_id获取到续约前的原始订单信息
    //  * @param $id
    //  */
    // public static function getPlanInfoOfParentId($id){
    //     if (empty($id)){
    //         return false;
    //     }
    //     $record_arr = (new Query())->from(PlanRecord::tableName() . ' as pr')->select([
    //         'pr.id', 'u.username', 'u.realname', 'pr.money_insure', 'pr.money_op', 'pr.money_warning','pr.money_force','pr.money_settlement','pr.status','pr.parent_id'
    //     ])->leftJoin(
    //         User::tableName() . ' as u',
    //         'pr.user_id = u.id'
    //     )->where([
    //         'pr.id' => $id,
    //     ])->one();

    //     if (empty($record_arr)){
    //         return false;
    //     }

    //     if ($record_arr['parent_id'] != 0){
    //         return self::getPlanInfoOfParentId($record_arr['parent_id']);
    //     }
    //     return $record_arr;
    // }

    /**
     * 根据原始ID获取最新的续约订单
     * @param $id
     */
    public static function getREQPlanOfId($id){
        if (empty($id)){
            return false;
        }
        $record_arr = (new Query())->from(PlanRecord::tableName() . ' as pr')->select([
            'pr.id', 'u.username', 'u.realname', 'pr.money_insure', 'pr.money_op', 'pr.money_warning','pr.money_force','pr.money_settlement','pr.status','pr.parent_id'
        ])->leftJoin(
            User::tableName() . ' as u',
            'pr.user_id = u.id'
        )->where([
            'pr.parent_id' => $id,
            'pr.parent_type' => PlanRecord::PARENT_TYPE_RENEW,
        ])->one();

        if (empty($record_arr)){
            return false;
        }

        if ($record_arr['status'] == PlanRecord::STATUS_EXPIRED){
            return self::getREQPlanOfId($record_arr['id']);
        }
        return $record_arr;
    }
    
    /**
     * 为增配获取剩余周期
     * @params $end_time
     */
    public static function getRestInterval($plan_type, $end_time, $start_time=null) {
        if (empty($start_time)) {
            $start_time = TimeHelper::zeroClockTimeOfDay(time());
        }
        $projectInfo = Project::getInfo($plan_type);
        if (!$projectInfo) {
            return false;
        }
        if ($projectInfo['type'] == Project::TYPE_DAY) {
            $rest_days = TimeHelper::get_real_day($start_time, $end_time);
            $rest_interval = $rest_days;
        } else if ($projectInfo['type'] == Project::TYPE_MONTH) {
            $rest_days = TimeHelper::get_between_days($start_time, $end_time);
            $rest_interval = ($rest_days % 30) == 0 ? $rest_days/30 : intval($rest_days/30)+1;
        } else {
            return false;
        }
        return $rest_interval;
    }

    /**
     * 获取配资已过周期
     * 
     */
    public static function getPastInterval($plan_type, $start_time, $end_time=null) {
        if(empty($end_time)) {
            $end_time = TimeHelper::zeroClockTimeOfDay(time());
        }
        $projectInfo = Project::getInfo($plan_type);
        if (!$projectInfo) {
            return false;
        }
        if ($projectInfo['type'] == Project::TYPE_DAY) {
            $rest_days = TimeHelper::get_real_day($start_time, $end_time);
            $rest_interval = $rest_days;
        } else if ($projectInfo['type'] == Project::TYPE_MONTH) {
            $rest_days = TimeHelper::get_between_days($start_time, $end_time);
            $rest_interval = ($rest_days % 30) == 0 ? $rest_days/30 : intval($rest_days/30)+1;
        } else {
            return false;
        }
        return $rest_interval;
    }

    /**
     * 计算增配/减配操盘金范围
     * @return ［'max_op' => $max_op, 'min_op' => $min_op］
     */
    public static function getIncreaseOpInterval($money_cur_insure, $power, $plan_type, $to_increase=true){
        $project_info = Project::getInfo($plan_type);
        if ($to_increase == true){
            $max_insure = $project_info['insure_max'] - $money_cur_insure;
        }else{
            $max_insure = $money_cur_insure - $project_info['insure_min'];
        }
        $min_insure = $project_info['insure_min'];
        if ($max_insure < $min_insure || $max_insure == 0)
            return false;
        return ["max_insure" => $max_insure, "min_insure" => $min_insure];
    }

    /**
     * 为增配/减配检查操盘金是否合法
     * @params $money_op_increase 
     * @return boolean
     */
    public static function checkValidIncrease($money_cur_insure, $money_insure_increase, $power, $plan_type, $to_increase=true) {
        if ($money_insure_increase % 1000 != 0) {
            return ['errno' => ErrorCodeHelper::CODE_INPUT_INVALID, 'errmsg' => (($to_increase == true) ? '新增' : '减少') . '的保证金必须是1000的整数倍！'];
        }
        $increase_interval = PlanHelper::getIncreaseOpInterval($money_cur_insure, $power, $plan_type, $to_increase);
        if ($money_insure_increase > $increase_interval['max_insure'] or $money_insure_increase < $increase_interval['min_insure']) {
            return ['errno' => ErrorCodeHelper::CODE_INPUT_INVALID, 'errmsg' => (($to_increase == true) ? '新增' : '减少') . '的保证金超出了选择范围！'];
        }
        return true;
    }
}
