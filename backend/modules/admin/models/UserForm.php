<?php
/**
 * Created by PhpStorm.
 * User: yuyc
 * Date: 2016/10/18
 * Time: 18:01
 */

namespace app\modules\admin\models;


use common\models\User;
use yii\base\Model;

/**
 * 后台用户表单
 * @package app\modules\admin\models
 */
class UserForm extends Model
{
    public $id;
    public $username;
    public $email;
    public $status;
    public $password;
    public $password_repeat;

    public function rules() {
        return [
            ['id', 'trim'],
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.', 'on' => ['create']],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.', 'on' => ['create']],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['status', 'integer'],

            ['password_repeat', 'required'],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Passwords don't match" ],
        ];
    }

    public function scenarios()
    {
        $scenarios =  parent::scenarios();
        $scenarios['create'] = ['username', 'email', 'password', 'password_repeat', 'id'];
        $scenarios['edit'] = ['username', 'email', 'id'];
        $scenarios['statusSwitch'] = ['status'];
        return $scenarios;
    }

    /**
     * 添加后台管理员
     * @return User|null
     */
    public function signup() {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }

    /**
     * 编辑后台用户
     * @param $userId  用户Id
     * @return User|null
     * @throws \Exception
     */
    public function edit($userId, $params) {
        if ( !$this->validate() ) {
            return null;
        }
        $user = User::findOne($userId);
        foreach ($params as $attribute => $value) {
            $user->{$attribute} = $value;
        }
        $result =  $user->update();  # false 表示update失败，0表示没有变化
        return $result ? $user : $result;
    }

    /**
     * 获取用户的信息
     * @param $userId
     * @return null|static
     */
    public function getDetail($userId) {
        $user = new User();
        $row = $user->findOne($userId);
        return $row;
    }

    public function delete($userId) {
        $user = new User();
        $user->delete();
    }
}