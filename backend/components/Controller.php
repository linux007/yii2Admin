<?php
namespace app\components;

use app\modules\admin\models\Menu;
use Yii;
use yii\filters\VerbFilter;
use yii\web\View;

/**
 * Created by PhpStorm.
 * User: yuyc
 * Date: 2016/10/28
 * Time: 17:13
 */
class Controller extends \yii\web\Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'allowActions' => [
                    'site/login',
                    'site/error',
                    'site/logout',
                ],
                'rules' => [
                    'validate*'
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $actionId = $action->getUniqueId();
            if ($actionId === Yii::$app->getErrorHandler()->errorAction) {
                $this->layout = 'lte_main';
            }
            $this->view->on(View::EVENT_BEGIN_BODY, function() {
                $pathInfo = '/' . Yii::$app->request->getPathInfo();
                $breadcrumbs = Menu::find()->select(['name'])->where(['route' => $pathInfo])->asArray()->one();
                $this->view->params['breadcrumbs'][] = [
                    'label' => $breadcrumbs['name'],
                    'url' => $pathInfo
                ];
            });

            return true;
        }
        return false;
    }

    public function actionError() {
        $exception = \Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
    }

    public function init()
    {
        $user = Yii::$app->getUser();
        $auth = Yii::$app->getAuthManager();
        $assignment = array_keys($auth->getPermissionsByUser($user->id));

        $allNodes = Menu::find()->select(['id','name', 'route', 'lft', 'rgt','lvl', 'display'])->asArray()->all();
        $treeView = $this->toHierarchy($allNodes, $assignment, true);

        Yii::$app->params['treeMenu'] = $treeView;
    }

    /**
     * 树结构生成函数
     *
     * @param      $collection
     * @param null $assignment
     * @param bool $menu  是否是菜单
     * @return array
     */
    protected function toHierarchy($collection, $assignment = null, $isMenu = false)
    {
        // Trees mapped
        $trees = array();
        $l = 0;

        if (count($collection) > 0) {
            // Node Stack. Used to help building the hierarchy
            $stack = array();

            foreach ($collection as $key => $node) {
                $item = $node;

                #非子节点 需要增加 nodes 属性
                $isChild = ($node['rgt'] == $node['lft'] + 1);
                if ( !$isChild ) $item['nodes'] = array();

                $item['text'] = $item['name'];
                if ( $assignment && in_array($item['route'], $assignment) ) {
                    $item['state'] = ['checked' => true];
                }

                # 如果是调取菜单，只获取该用户被分配的权限
                if ($isMenu && !isset($item['state'])) {
                    continue;
                }
                #未激活，不显示菜单
                if ($isMenu && 0 == $item['display']) {
                    continue;
                }
                unset($item['name']);

                // Number of stack items
                $l = count($stack);

                // Check if we're dealing with different levels
                while($l > 0 && $stack[$l - 1]['lvl'] >= $item['lvl']) {
                    array_pop($stack);
                    $l--;
                }

                // Stack is empty (we are inspecting the root)
                if ($l == 0) {
                    // Assigning the root node
                    $i = count($trees);
                    $trees[$i] = $item;
                    $stack[] = & $trees[$i];
                } else {
                    // Add node to parent
                    $i = count($stack[$l - 1]['nodes']);
                    $stack[$l - 1]['nodes'][$i] = $item;
                    $stack[] = & $stack[$l - 1]['nodes'][$i];
                }
            }
        }

        return $trees;
    }
}