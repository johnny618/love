<?php
/**
 * Created by JohnnyLin.
 * User: JohnnyLin
 * Date: 2015/06/21
 */

namespace common\helpers;

use Yii;

class MailHelper
{
    static public function sendMail($from='johnnylin@koudailc.com',$To='johnnylin@koudailc.com',$Cc='',$Subject='title',$context=''){
        $mail= Yii::$app->mailer->compose(); //加载模板这样写：$mail= Yii::$app->mailer->compose('moban',['key'=>'value']);
        $mail->setFrom($from);
        $mail->setTo($To);
        if (!empty($Cc)){
            $mail->setCc($Cc);
        }
        $mail->setSubject($Subject);
        $mail->setHtmlBody($context);
        if($mail->send())
            return true;
        else
            return false;
    }
}