<?php
namespace frontend\models;

use common\models\User;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $password_repeat;
    public $password;
    public $realname;
    public $phone;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => '这个用户名已存在'],
            ['username', 'string', 'min' => 2, 'max' => 20],

            [[ 'password', 'password_repeat'], 'filter', 'filter' => 'trim'],
            [[ 'password', 'password_repeat'], 'required'],

            ['phone', 'default', 'value' => ''],
            ['phone', 'string', 'length' => 11],
            ['phone', 'unique', 'targetClass' => 'frontend\models\User', 'message' => '这个手机号已经被占用了'],

            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"两次密码输入不同"],

        ];
    }

    public function attributeLabels() {
        return [
            'username' => '用户名',
            'password' => '密码',
            'password_repeat' => '确认密码',
            'phone'=>'手机号',
            'realname'=>'真实姓名',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
//    public function signup()
//    {
//        if ($this->validate()) {
//            $user = new User();
//            $user->username = $this->username;
//            $user->setPassword($this->password);
//            $user->generateAuthKey();
//            if ($user->save()) {
//                return $user;
//            }
//        }
//
//        return null;
//    }
    public function signup() {
        if ($this->validate()) {
            $user = new User();
            $user->phone = $this->phone;
            $user->setPassword($this->password);

            if (empty($this->username)) {
                $user->username = $this->phone;
            }
            else {
                $user->username = $this->username;
            }
            if (!empty($this->email)) {
                $user->email = $this->email;
            }
            #$user->generateAuthKey();
            $ret = $user->save();
            if (! $ret) {
                Yii::$app->getSession()->setFlash('error', '系统错误，注册失败。');
                return false;
            }
            return $user;
        }
        $error = array_pop( $this->getErrors() );
        Yii::$app->getSession()->setFlash('error', $error[0]);

        return false;
    }

}
