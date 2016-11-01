<?php
/**
 * Created by PhpStorm.
 * User: yuyc
 * Date: 2016/10/25
 * Time: 11:22
 */

namespace app\modules\admin\models;


use common\models\User;
use yii\db\ActiveRecord;

class Assignment extends ActiveRecord
{
    public static function tableName() {
        return 'auth_assignment';
    }

    public function getUsers() {
        return $this->hasMany(User::className(), ['id' => 'user_id']);
    }
}