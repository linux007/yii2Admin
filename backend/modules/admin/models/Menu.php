<?php
namespace app\modules\admin\models;

use Yii;
use yii\data\ArrayDataProvider;
use \yii\db\ActiveRecord;

class Menu extends ActiveRecord {


    public static function tableName() {
        return 'menu';
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

