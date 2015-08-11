<?php

namespace common\helpers;

use Yii;
use yii\base\Object;

class MessageHelper extends Object {
    const AUTO_SWITCH = TRUE; //当前通道失败时，切换另外的通道？

    /**
     * 云片已有模版：（模版不能更改，必须跟云片后台的完全一致）
     * 【口袋超盘】您的交易帐号是：#username#，交易密码是：#password#。请妥善保管此密码，勿向任何人提供此短信以免给您造成损失。
     * 【口袋超盘】您的验证码为:#code#(此验证码有效期为#effective#分钟)
     * 【口袋超盘】尊敬的客户#username#，您已成功充值#money#，请至账户查看相关信息。
     * 【口袋超盘】尊敬的客户#username#，您已成功追加保证金#money#元，请至账户查看相关信息。
     * 【口袋超盘】尊敬的客户#username#，您已成功续约项目#plan#，请至账户查看相关信息。
     * 【口袋超盘】尊敬的客户#username#，您已成功提取利润#money#元，请至账户查看相关信息。
     */
    const TPL_HOMS = '您的交易账号是：#username#，交易密码是：#password#。请妥善保管此密码，勿向任何人提供此短信以免给您造成损失。';
    const TPL_PWD = '您的验证码为:#code#(此验证码有效期为#effective#分钟)';
    const TPL_INSURE_APPEND = '尊敬的客户#username#，您的项目#plan#已到警戒线，请及时补仓，否则将会被平仓。'; //追加保证金
    const TPL_PROJ_SETTLEMENT = '尊敬的客户#username#，您的项目#plan#已完结，请至账户查看相关信息。'; //项目完结
    const TPL_RECHARGE = '尊敬的客户#username#，您已成功充值#money#，请至账户查看相关信息。'; //充值
    const TPL_INSURE_ADD = '尊敬的客户#username#，您已成功追加保证金#money#元，请至账户查看相关信息。'; //成功追加保证金
    const TPL_RENEW = '尊敬的客户#username#，您已成功续约项目#plan#，请至账户查看相关信息。'; //成功续约
    const TPL_REJECT_RENEW = '尊敬的客户#username#，您的项目#plan#续约失败，请至账户查看相关信息。'; //续约失败
    const TPL_PROFITS = '尊敬的客户#username#，您已成功提取利润#money#元，请至账户查看相关信息。'; //成功提取利润
    const TPL_EXPIRE_MONTH = '您的按月交易订单#planid#将于#expiretime#到期，请及时续约或者完结。“有超盘，才会赢”'; //按天即将到期
    const TPL_EXPIRE_DAY = '您的按天交易订单#planid#即将到期，如果您需要继续交易，请确保您的账户有足够的余额。“有超盘，才会赢”'; //按月即将到期
    const TPL_SETTLEMENT = '尊敬的客户#username#，您的#type#订单#plan#已完结，资金已存入您的口袋超盘账户，详情请登录至个人中心查看。客服热线：#service#'; //完结
    const TPL_ADMIN_TOLIST = '待办事项:#SMScontent#'; //短信通知后台管理员待办事项
    const TPL_NEED_INSURE = '尊敬的用户：您的#type#(编号:#id#)已经预警，请及时关注您的交易账号。客服热线：#service#
'; //短信通知用户追加保证金

    const SERVICE_YUNPIAN = 'smsService1';
    const SERVICE_WZD = 'smsService';
    const SERVICE_INTERFACE = 'smsServiceInterface';

    const CMD_MAIL_SENDER_SUPPORT = 'support@koudaicp.com';
    const CMD_MAIL_SENDER_ADMIN = 'admin@koudaicp.com';
    const CMD_MAIL_CNT_TYPE_STR = 'string';
    const CMD_MAIL_CNT_TYPE_FILE = 'file'; //TODO 还未实现

    static $services = [
        self::SERVICE_YUNPIAN => true,
        self::SERVICE_WZD => true,
    ];

    private static function getSmsParams($config_detail_key) {
        if (!isset(Yii::$app->params[self::SERVICE_INTERFACE]['config_detail'][$config_detail_key])) {
            Yii::error('Get sms params failed : ' . $config_detail_key);
            return false;
        }
        $auth_id = Yii::$app->params[self::SERVICE_INTERFACE]['config_detail'][$config_detail_key]['auth_id'];
        $template_id = Yii::$app->params[self::SERVICE_INTERFACE]['config_detail'][$config_detail_key]['template_id'];
        return ['auth_id' => $auth_id, 'template_id' => $template_id];
    }

    /**
     * 发送验证码短信
     * @param integer $mobile
     * @param integer $code
     * @param integer $effective
     * @param string $service
     * @return Ambigous <boolean, string>
     */
    public static function sendPwd($mobile, $code, $effective=0, $service=self::SERVICE_INTERFACE) {
        $config_detail_key = 'VerifyCode';
        if (empty($effective)) {
            $effective = Yii::$app->getSession()->getTimeout() / 60; #分钟
        }
        $params = [
            'code' => $code,
            'effective' => $effective,
        ];
        $sms_params = MessageHelper::getSmsParams($config_detail_key);
        if ($sms_params == false)
            return false;
        return self::sendSMSCommon($mobile, $sms_params['auth_id'], $sms_params['template_id'], $params, '', 60);
    }

    /**
     * 发送 homs 通知短信
     * @param integer $mobile
     * @param integer $code
     * @param integer $effective
     * @param string $service
     * @return Ambigous <boolean, string>
     */
    public static function sendHoms($mobile, $homs_account, $homs_pwd, $service=self::SERVICE_INTERFACE) {
        $config_detail_key = 'HOMS';
        if (empty($homs_account) || empty($homs_pwd)){
            Yii::error('输入参数不正确 : ' . $config_detail_key);
            return false;
        }
        $params = [
            'username' => $homs_account,
            'password' => $homs_pwd,
        ];
        $sms_params = MessageHelper::getSmsParams($config_detail_key);
        if ($sms_params == false)
            return false;
        return self::sendSMSCommon($mobile, $sms_params['auth_id'], $sms_params['template_id'], $params);
    }
    
    
    
    /**
     * 发送 充值成功 通知短信
     * @param integer $mobile
     * @param integer $code
     * @param integer $effective
     * @param string $service
     * @return Ambigous <boolean, string>
     */
    public static function sendRecharge($mobile, $username, $money, $service=self::SERVICE_INTERFACE) {
        $config_detail_key = 'RECHARGE';
        if (empty($username)) {
            Yii::error('输入参数不正确 : ' . $config_detail_key);
            return false;
        }
        $params = [
            'username' => $username,
            'money' => $money,
        ];
        $sms_params = MessageHelper::getSmsParams($config_detail_key);
        if ($sms_params == false)
            return false;
        return self::sendSMSCommon($mobile, $sms_params['auth_id'], $sms_params['template_id'], $params);
    }    
    
    /**
     * 发送 提现成功 通知短信
     * @param  [type] $mobile   手机号
     * @param  [type] $username 手机号
     * @param  [type] $money    提现金额
     * @param  [type] $code     银行尾号
     * @param  [type] $bank     银行名称
     * @param  [type] $service  [description]
     * @return [type]           [description]
     */
    public static function sendWithdraw($mobile, $username, $money, $code, $bank, $service=self::SERVICE_INTERFACE) {
        $config_detail_key = 'WITHDRAW';
        if (empty($username)) {
            Yii::error('输入参数不正确 : ' . $config_detail_key);
            return false;
        }
        $params = [
            'username' => $username,
            'money' => $money,
            'code' => $code,
            'bank' => $bank,
        ];
        $sms_params = MessageHelper::getSmsParams($config_detail_key);
        if ($sms_params == false)
            return false;
        return self::sendSMSCommon($mobile, $sms_params['auth_id'], $sms_params['template_id'], $params);
    }
    
     /**
     * 发送 追加保证金成功 通知短信
     * @param integer $mobile
     * @param integer $code
     * @param integer $effective
     * @param string $service
     * @return Ambigous <boolean, string>
     */
    public static function sendInsureAdd($mobile, $username, $money, $service=self::SERVICE_INTERFACE) {
        $config_detail_key = 'INSURE_ADD';
        if (empty($username)) { 
            Yii::error('输入参数不正确 : ' . $config_detail_key);
            return false;
        }
        $params = [
            'username' => $username,
            'money' => $money,
        ];
        $sms_params = MessageHelper::getSmsParams($config_detail_key);
        if ($sms_params == false)
            return false;
        return self::sendSMSCommon($mobile, $sms_params['auth_id'], $sms_params['template_id'], $params);
    }

    /**
     * 发送 增配/减配成功 通知短信
     * @return Ambigous <boolean, string>
     */
    public static function sendOpChange($mobile, $username, $plan_id, $to_increase=true, $service=self::SERVICE_INTERFACE) {
        if ($to_increase == true) {
            $config_detail_key = 'OPINCREASE';
        } else {
            $config_detail_key = 'OPDECREASE';
        }
        if (empty($username)) { 
            Yii::error('输入参数不正确 : ' . $config_detail_key);
            return false;
        }
        $params = [
            'username' => $username,
            'plan' => $plan_id,
        ];
        $sms_params = MessageHelper::getSmsParams($config_detail_key);
        if ($sms_params == false)
            return false;
        return self::sendSMSCommon($mobile, $sms_params['auth_id'], $sms_params['template_id'], $params);
    }
    
    /**
     * 发送 续约成功 通知短信
     * @param integer $mobile
     * @param integer $code
     * @param integer $effective
     * @param string $service
     * @return Ambigous <boolean, string>
     */
    public static function sendRenew($mobile, $username, $plan, $service=self::SERVICE_INTERFACE) {
        $config_detail_key = 'RENEW';
        if (empty($username) || empty($plan)) {
            Yii::error('输入参数不正确 : ' . $config_detail_key);
            return false;
        }
        $params = [
            'username' => $username,
            'plan' => $plan,
        ];
        $sms_params = MessageHelper::getSmsParams($config_detail_key);
        if ($sms_params == false)
            return false;
        return self::sendSMSCommon($mobile, $sms_params['auth_id'], $sms_params['template_id'], $params);
    }

    /**
     * 发送 续约失败 通知短信
     * @param integer $mobile
     * @param integer $code
     * @param integer $effective
     * @param string $service
     * @return Ambigous <boolean, string>
     */
    public static function sendRejectRenew($mobile, $username, $plan, $service=self::SERVICE_INTERFACE) {
        $config_detail_key = 'REJECT_RENEW';
        if (empty($username) || empty($plan)) {
            Yii::error('输入参数不正确 : ' . $config_detail_key);
            return false;
        }
        $params = [
            'username' => $username,
            'plan' => $plan,
        ];
        $sms_params = MessageHelper::getSmsParams($config_detail_key);
        if ($sms_params == false)
            return false;
        return self::sendSMSCommon($mobile, $sms_params['auth_id'], $sms_params['template_id'], $params);
    }

    /**
     * 发送 待办事项 通知短信
     * @param integer $mobile
     * @param string $SMScontent
     * @param string $service
     * @return Ambigous <boolean, string>
     */
    public static function sendTodoList($mobile, $SMScontent) {
        $config_detail_key = 'TodoList';
        $params = [
            'SMScontent' => $SMScontent,
        ];
        $auth_id = Yii::$app->params[self::SERVICE_INTERFACE]['config_detail'][$config_detail_key]['auth_id'];
        $template_id = Yii::$app->params[self::SERVICE_INTERFACE]['config_detail'][$config_detail_key]['template_id'];
        return self::sendSMSCommon($mobile, $auth_id, $template_id, $params);
    }

    /**
     * 发送 需要追加保证金
     * @param $mobile
     * @param $type
     * @param $id
     * @param string $service
     * @return bool|string
     */
    public static function sendNeedInsure($mobile, $type, $id,$tel='400-799-1885',$service=self::SERVICE_INTERFACE) {
        $config_detail_key = 'NEED_INSURE';
        if (empty($type) || empty($id)) {
            Yii::error('输入参数不正确 : ' . $config_detail_key);
            return false;
        }
        $params = [
            'type' => $type,
            'id' => $id,
            'service' => $tel,
        ];
        $sms_params = MessageHelper::getSmsParams($config_detail_key);
        if ($sms_params == false)
            return false;
        return self::sendSMSCommon($mobile, $sms_params['auth_id'], $sms_params['template_id'], $params);
    }
    
      /**
     * 发送 完结 通知短信
     * @param integer $mobile
     * @param integer $code
     * @param integer $effective
     * @param string $service
     * @return Ambigous <boolean, string>
     */
    public static function sendSettlement($mobile, $username,$type, $plan,$tel, $service=self::SERVICE_INTERFACE) {
        $config_detail_key = 'SETTLEMENT';
        if (empty($username) || empty($plan)) {
            Yii::error('输入参数不正确 : ' . $config_detail_key);
            return false;
        }
        $params = [
            'username' => $username,
            'type' => $type,
            'plan' => $plan,
            'service' => $tel,
        ];
        $sms_params = MessageHelper::getSmsParams($config_detail_key);
        if ($sms_params == false)
            return false;
        return self::sendSMSCommon($mobile, $sms_params['auth_id'], $sms_params['template_id'], $params);
    }

    /**
     * 发送 提取利润成功 通知短信
     * @param integer $mobile
     * @param integer $code
     * @param integer $effective
     * @param string $service
     * @return Ambigous <boolean, string>
     */
    public static function sendProfits($mobile, $username, $money, $service=self::SERVICE_INTERFACE) {
        $config_detail_key = 'PROFITS';
        if (empty($username)) {
            Yii::error('输入参数不正确 : ' . $config_detail_key);
            return false;
        }
        $params = [
            'username' => $username,
            'money' => $money,
        ];
        $sms_params = MessageHelper::getSmsParams($config_detail_key);
        if ($sms_params == false)
            return false;
        return self::sendSMSCommon($mobile, $sms_params['auth_id'], $sms_params['template_id'], $params);
    }

    /**
     * 发送 按月即将到期 通知短信
     * @param integer $mobile
     * @return Ambigous <boolean, string>
     */
    public static function sendExpireMonth($mobile, $planid, $expiretime, $service=self::SERVICE_INTERFACE) {
        $config_detail_key = 'EXPIRE_MONTH';
        if (empty($planid) || empty($expiretime)) {
            Yii::error('输入参数不正确 : ' . $config_detail_key);
            return false;
        }
        $params = [
            'planid' => $planid,
            'expiretime' => $expiretime,
        ];
        $sms_params = MessageHelper::getSmsParams($config_detail_key);
        if ($sms_params == false)
            return false;
        return self::sendSMSCommon($mobile, $sms_params['auth_id'], $sms_params['template_id'], $params);
    }

    /**
     * 发送 按天即将到期 通知短信
     * @param integer $mobile
     * @return Ambigous <boolean, string>
     */
    public static function sendExpireDay($mobile, $planid, $service=self::SERVICE_INTERFACE) {
        $config_detail_key = 'EXPIRE_DAY';
        if (empty($planid)) {
            Yii::error('输入参数不正确 : ' . $config_detail_key);
            return false;
        }
        $params = [
            'planid' => $planid,
        ];
        $sms_params = MessageHelper::getSmsParams($config_detail_key);
        if ($sms_params == false)
            return false;
        return self::sendSMSCommon($mobile, $sms_params['auth_id'], $sms_params['template_id'], $params);
    }
    
    public static function sendSMSCommon($phone, $auth_id, $template_id, $params=array(), $ext='', $sleep_time=0) {
        if (Yii::$app->getRequest() instanceof \yii\web\Request) {
            Yii::info('sms common interface: ' . $auth_id . ' ' . $template_id . ' ' . Yii::$app->getRequest()->getUrl(), 'sms');
        } else {
            Yii::info('sms common interface: ' . $auth_id . ' ' . $template_id, 'sms');
        }
        $apikeys = Yii::$app->params[self::SERVICE_INTERFACE]['apikeys'];
        $url = Yii::$app->params[self::SERVICE_INTERFACE]['url'];
        $auth_id = strval($auth_id);
        if ( !isset($apikeys[$auth_id])) {
            Yii::error("短信发送错误: " . $auth_id . " not exists! (" . $phone . ")", 'sms');
            return false;
        }
        $post_data = [
            'apikey' => $apikeys[$auth_id],
            'auth_id' => $auth_id,
            'template_id' => strval($template_id),
            'mobile' => $phone,
            'params' => json_encode($params),
            'ext' => $ext,
        ];
        if (intval($sleep_time) > 0){
            $post_data['sleep_time'] = intval($sleep_time);
        }
        $query = http_build_query($post_data);
        $response = NetHelper::cURLHTTPPost($url, $query);
        if ($response == false){
            Yii::error("短信发送连接失败: mobile:{$phone} auth_id:{$auth_id} template_id:{$template_id}", 'sms');
            return false;
        }else{
            $resp = json_decode(trim($response,chr(239).chr(187).chr(191)), true);
            if ($resp['errno'] != 0){
                Yii::error("短信发送失败: mobile:{$phone} auth_id:{$auth_id} template_id:{$template_id} error_msg:{$resp['errmsg']}", 'sms');
                return false;
            }else{
                Yii::info("短信发送成功: mobile:{$phone} auth_id:{$auth_id} template_id:{$template_id}", 'sms');
                return true;
            }
        }
    }

    /**
     * 短信发送。［注意］云片发送需要预先配置短信模版
     * @param integer $phone 手机号
     * @param string $message 短信内容
     * @param string $smsServiceUse "smsService"为温州贷接口，"smsService1"为云片接口
     * @return boolean|string
     */
    public static function sendSMS($phone, $message, $smsServiceUse = self::SERVICE_YUNPIAN) {
        if (Yii::$app->getRequest() instanceof \yii\web\Request) {
            Yii::info($message . ' ' . Yii::$app->getRequest()->getUrl(), 'sms');
        } else {
            Yii::info($message, 'sms');
        }

        if ($smsServiceUse == 'smsService') {
            $msg = urlencode($message);
            $url = Yii::$app->params['smsService']['url'];
            $uid = Yii::$app->params['smsService']['uid'];
            $auth = md5(Yii::$app->params['smsService']['code'] . Yii::$app->params['smsService']['password']);
            $result = file_get_contents("{$url}?uid={$uid}&auth={$auth}&mobile={$phone}&msg={$msg}&expid=0&encode=utf-8");
            // 返回值要是0这种格式才成功，后面是短信id
            if ($result && strpos($result, ',') !== false) {
                list($resCode, $resMsg) = explode(",", $result);
                if ($resCode == '0') {
                    return true;
                }
            }else{
                Yii::error("发送短信失败，result:{$result} mobile:{$phone} msg:{$msg}");
                return false;
            }
        }
        else {
            /*
             * 普通接口发短信
             * apikey 为云片分配的apikey
             * text 为短信内容
             * mobile 为接受短信的手机号
             */
            $url1 = Yii::$app->params[ self::SERVICE_YUNPIAN ]['url'];
            $apikey = Yii::$app->params[ self::SERVICE_YUNPIAN ]['apikey'];
            $msg = urlencode('【口袋超盘】' .$message);
            $post_string="apikey=$apikey&text=$msg&mobile=$phone";
            $data = "";
            $info=parse_url($url1);
            $fp=fsockopen($info["host"],80,$errno,$errstr,30);
            if(!$fp){
                Yii::warning(sprintf("%s open 80 failed[%s, %s].", __FUNCTION__, $errno, $errstr));
                return $data;
            }

            $head="POST ".$info['path']." HTTP/1.0\r\n";
            $head.="Host: ".$info['host']."\r\n";
            $head.="Referer: http://".$info['host'].$info['path']."\r\n";
            $head.="Content-type: application/x-www-form-urlencoded\r\n";
            $head.="Content-Length: ".strlen(trim($post_string))."\r\n";
            $head.="\r\n";
            $head.=trim($post_string);
            $write=fputs($fp,$head);
            $header = "";
            while ($str = trim(fgets($fp,4096))) {
                $header.=$str;
            }
            while (!feof($fp)) {
                $data .= fgets($fp,4096);
            }
            // 返回值要是0这种格式才成功
            $ret = json_decode($data, true);
            if ($ret && $ret['code'] == '0') {
                return true;
            }
            else {
                Yii::error("发送短信失败,".$data);
                return false;
            }
        }
    }

    /**
     * [注意] 该方法存在命令注入的危险！！！
     * 通过命令行发送邮件，未完整实现，暂时仅测试了发送简单文本内容。
     * @param mixed $content string/array 发送内容
     * @param string $title 发件标题
     * @param string $to 送件地址
     * @param string $from 发件地址
     * @param array $cc CC地址
     * @param array $bcc BCC地址
     * @throws ErrorException
     * @return boolean
     */
    public static function sendCmdMail($content, $title, $to, $from=self::CMD_MAIL_SENDER_SUPPORT, $cc=[], $bcc=[]) {
        $ret = false;

        $emails = array_merge([$to], $cc, $bcc);
        foreach($emails as $_email) {
            if (! filter_var($_email, FILTER_VALIDATE_EMAIL)) {
                Yii::error( sprintf('邮件地址有误:%s', $_email) );
                throw new ErrorException('发送失败，邮件地址有误');
            }
        }

        $now = time();
        if (is_array($content)) {
            Yii::error(sprintf('[%s]调用失败，%s.', __FUNCTION__, '暂未实现')); //TODO
            throw new ErrorException('发送失败');
        }
        else if (is_string($content)) {
            $msg = sprintf('[%s] %s', date('Y-m-d H:i', $now), addslashes($content)); //addslashes($content)
            $title = addslashes($title);
            $cmd_tpl = "echo '{$msg}' | mail -s '{$title}' {cc_holder} {bcc_holder} {$to}";
            if (empty($cc)) {
                $cmd = str_replace('{cc_holder}', '', $cmd_tpl);
            }
            else {
                $cc_str = sprintf('-c %s', implode(',', $cc));
                $cmd = str_replace('{cc_holder}', $cc_str, $cmd_tpl);
            }

            if (empty($bcc)) {
                $cmd = str_replace('{bcc_holder}', '', $cmd);
            }
            else {
                $bcc_str = sprintf('-b "%s"', implode(',', $bcc));
                $cmd = str_replace('{bcc_holder}', $bcc_str, $cmd);
            }

            exec($cmd, $output, $retval);
            $output = implode(' ', $output);
            Yii::info("cmd邮件发送结果[retval:$retval][output:$output]");

            return ($retval === 0);
        }
        else {
            Yii::error(sprintf('[%s]调用失败，%s.', __FUNCTION__, '不支持的类型'));
            throw new ErrorException('发送失败');
        }

        return $ret;
    }
}