<?php
/**
 * Created by PhpStorm.
 * User: yuyc
 * Date: 2016/10/26
 * Time: 16:55
 */

namespace app\modules\admin\models;


use kartik\tree\models\Tree;
use kartik\tree\models\TreeTrait;
use kartik\tree\TreeView;
use yii\db\ActiveRecord;
use Yii;

class Menu extends Tree
{
//    use TreeTrait {
//        isDisabled as parentIsDisabled;
//
//    }

    /**
     * @var string the classname for the TreeQuery that implements the NestedSetQueryBehavior.
     * If not set this will default to `kartik	ree\models\TreeQuery`.
     */
    public static $treeQueryClass; // change if you need to set your own TreeQuery

    /**
     * @var bool whether to HTML encode the tree node names. Defaults to `true`.
     */
    public $encodeNodeNames = true;

    /**
     * @var bool whether to HTML purify the tree node icon content before saving.
     * Defaults to `true`.
     */
    public $purifyNodeIcons = true;

    /**
     * @var array activation errors for the node
     */
    public $nodeActivationErrors = [];

    /**
     * @var array node removal errors
     */
    public $nodeRemovalErrors = [];

    /**
     * @var bool attribute to cache the `active` state before a model update. Defaults to `true`.
     */
    public $activeOrig = true;

    public $allRoute = [];


    public function init()
    {
        $auth = Yii::$app->getAuthManager();
        $permissions = $auth->getPermissions();

        $allRoute = [];
        if ($permissions) {
            foreach ($permissions as $route => $item) {
                $allRoute[$route] = $item->name;
            }
        }

        $this->allRoute = $allRoute;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['display'] = '显示菜单';
        return $labels;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_menu';
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['route', 'display'], 'safe'];
        return $rules;
    }

    /**
     * Note overriding isDisabled method is slightly different when
     * using the trait. It uses the alias.
     */
    public function isDisabled()
    {
//        if (Yii::$app->user->username !== 'admin') {
//            return true;
//        }
//        return $this->parentIsDisabled();
        return parent::isDisabled();
    }

}