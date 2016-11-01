<?php
namespace app\modules\admin\models;

use common\models\User;
use Yii;
use yii\data\ArrayDataProvider;
use \yii\db\ActiveRecord;

class Role extends ActiveRecord {

    const TYPE_ROLE = 1;
    const TYPE_PERMISSION = 2;

    public static function tableName() {
        return 'auth_item';
    }

    public function rules() {
        return [
            [['name', 'type'], 'required', 'on' => ['create']],
            [['name'], 'unique', 'on' => ['create']],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
        ];
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['name', 'type'];  //在该场景下的属性进行验证，其他场景和没有on的都不会验证: 如果没有写参数on，则表示适用于任何场景，如果写了参数on，则表示只适用于on参数后面的场景
        return $scenarios;
    }


    /**
     * role searchModel
     * @param null $params
     */
    public function search($params = null) {
        $authManager = Yii::$app->getAuthManager();
        $items = $authManager->getRoles();
        if ( $this->load($params) && $this->validate() ) {
            $search = trim($this->name);
            $desc = trim($this->description);
            $items = array_filter($items, function ($item) use ($search, $desc) {
                return (empty($search) || strpos(strtolower($item->name), $search) !== false) && ( empty($desc) || strpos(strtolower($item->description), $desc) !== false);
            });
        }
        return new ArrayDataProvider([
            'allModels' => $items,
            'sort' => [
                'attributes' => ['id', 'name'],
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
    }

}

