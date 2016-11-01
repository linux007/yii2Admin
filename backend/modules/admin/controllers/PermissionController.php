<?php
/**
 * Created by PhpStorm.
 * User: yuyc
 * Date: 2016/10/8
 * Time: 15:06
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\Menu;
use app\modules\admin\models\Product;
use kartik\tree\models\Tree;
use Yii;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use app\components\Controller;
use yii\web\Response;

/**
 * Created by PhpStorm.
 * User: yuyc
 * Date: 2016/10/8
 * Time: 15:06
 */
class PermissionController extends Controller
{

    /**
     * 权限列表
     *
     * 显示所有权限权限列表
     */
    public function actionIndex() {
        // todo

        $parentKey = null;
        $permissions = $this->actionFlush();

//        print_r($permissions);
//        exit;

        $productModel = new Product();

        foreach ($permissions as $m => $mIterm) {
            $node = Product::findOne(['route' => $m]);
            if ( empty($node) ) {
                $node = clone $productModel;
                $node->activeOrig = $node->active;
                $node->collapsed = true;
                $isNewRecord = $node->isNewRecord;
                $node->load(['Product' => ['name' => $m, 'route' => $m]]);
                $node->makeRoot();
                $node->save();
            }

            foreach ($mIterm as $c => $cItem) {
                if ($m == 'app') {
                    $route = $c;
                } else {
                    $route = $m . '/' . $c;
                }

                $node = Product::findOne(['route' => $route]);
                if ( empty($node) ) {
                    $node = new Product();
                    $node->activeOrig = $node->active;
                    $node->load(['Product' => ['name' => $cItem['doc'], 'route' => $route]]);
                    $parentMod = Product::findOne(['route' => $m]);
                    $node->appendTo($parentMod);
                    # 如果不符合rules规则，会保存失败，导致action 循环会报异常
//                    if ( !$node->save() ) {
//                        continue;
//                    }
                }
                if (isset($cItem['actions']) && $cItem['actions']) {
                    foreach ($cItem['actions'] as $action) {
                        $node = Product::findOne(['route' => $action['route']]);
                        if ( empty($node) ) {
                            $node = new Product();
                            $node->activeOrig = $node->active;
                            $node->load(['Product' => ['name' => $action['doc'], 'route' => $action['route']]]);
                            $parentCtl = Product::findOne(['route' => $route]);
                            $node->appendTo($parentCtl);
//                            $node->save();
                        }
                    }
                }
            }
        }



        /*
        foreach ($menu as $m => $mItem) {
            $menuModel = new Menu();
            $menuModel->name = $m;
            $menuModel->route = $m;
            $menuModel->save();
            foreach ($mItem as $c => $cItem) {
                $menuModel = new Menu();
                $menuModel->name = $cItem['doc'];
                $menuModel->route = $m . '/' . $c;
                $menuModel->parent = $m;
                $menuModel->save();

                foreach ($cItem['actions'] as $action) {
                    $menuModel = new Menu();
                    $menuModel->name = $action['doc'];
                    $menuModel->route = $action['route'];
                    $menuModel->parent = $m . '/' . $c;
                    $menuModel->save();
                }
            }
        }
        */

        $menuModel = new Menu();
        $menus = $menuModel->find()->select(['name', 'route', 'parent', 'display'])->asArray()->all();
        $result = $this->buildTree($menus);
        return Json::encode($result);

    }

    /**
     * 权限管理
     *
     * 显示所有权限内容
     */
    public function actionManager() {
        $treeView = [];

        # 当前分配的角色
        $roleName = Yii::$app->request->get('role');
        $assignment = array_keys(Yii::$app->getAuthManager()->getPermissionsByRole($roleName));

        # nested sets
        $allNodes = Product::find()->select(['id','name', 'route', 'lft', 'rgt','lvl'])->asArray()->all();
        $treeView = $this->toHierarchy($allNodes, $assignment);

        /**
        $menuModel = new Menu();

        $roleName = Yii::$app->request->get('role');
        $assignment = array_keys(Yii::$app->getAuthManager()->getPermissionsByRole($roleName));

        $menus = $menuModel->find()->select(['name', 'route', 'parent'])->asArray()->all();
        $result = $this->buildTree($menus, null, $assignment);
//        return Json::htmlEncode($result);
         **/
        return $this->renderAjax('manager', [
           'treeData' => Json::htmlEncode($treeView),
           'role' => $roleName,
        ]);
    }

    /**
     * 权限分配
     *
     * 给角色分配权限
     */
    public function actionAssign() {

        # get params
        $request = Yii::$app->request;
        $nodeIds = $request->post('nodeIds');
        $roleName = $request->post('role');

        $auth = Yii::$app->getAuthManager();
        $role = $auth->getRole($roleName);
        $assignments = array_keys($auth->getPermissionsByRole($roleName));

        $optionsDeleted = array_diff($assignments, $nodeIds);
        $optionsAdded = array_diff($nodeIds, $assignments);

        #删除的权限
        if ( $optionsDeleted ) {
            foreach ($optionsDeleted as $name) {
                $permission = $auth->getPermission($name);
                $auth->removeChild($role, $permission);
            }
        }

        # 添加的权限
        if ( $optionsAdded ) {
            foreach ($optionsAdded as $name) {
                $permission = $auth->getPermission($name);
                if($auth->canAddChild($role, $permission)) {
                    $auth->addChild($role, $permission);
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['code'=>true,'message'=>'assigned success!'];
    }

    /**
     * 刷新权限
     *
     * 刷新所有权限
     */
    public function actionFlush() {
        $result = $menu = [];
        $auth = Yii::$app->authManager;
        $this->getRouteRecrusive(Yii::$app, $result);

        foreach ($result as $moduleId => $module) {
            if (is_array($module)) {
                if ($moduleId) {
                    $moduleObj = Yii::$app->getModule($moduleId);
                    $nameSpace = trim($moduleObj->controllerNamespace, '\\') . '\\';
                } else {
                    $moduleId = 'app';
                    $nameSpace = trim(Yii::$app->controllerNamespace, '\\') . '\\';
                }

                // module 一级 Item
                $oldRermission = $auth->getPermission($moduleId);
                if ( empty($oldRermission) ) {
                    $modulePermission = $auth->createPermission($moduleId);
                    $auth->add($modulePermission);
                }

                foreach ($module as $controllerId => $controller) {
                    if (is_array($controller)) {
                        $controllerName = array_pop(explode('/', $controllerId));
                        $className = $nameSpace . Inflector::id2camel($controllerName) . 'Controller';
                        $class = new \ReflectionClass($className);
                        $doc = $class->getDocComment();
                        $matchDesc = $this->getDoc($doc);
                        if ($matchDesc) {
                            $matchDesc = trim($matchDesc[0][1]);
                        } else {
                            # 没有匹配到注释 不添加， 可手动添加或添加注释
                            continue;
                        }

                        // Controller 二级 Item
                        $oldRermission = $auth->getPermission($controllerId);
                        if ( empty($oldRermission) ) {
                            $controllerPermission = $auth->createPermission($controllerId);
                            $auth->add($controllerPermission);
//                            $auth->addChild($modulePermission, $controllerPermission);
                        }

                        $menu[$moduleId][$controllerName]['doc'] = $matchDesc;
                        $module = Yii::$app->getModule($moduleId);
                        $controllerObj = Yii::createObject($className, [$controllerName, $module]);
                        $actions = array_keys($controllerObj->actions());
                        foreach ($controller as $Id => $route) {
                            $routeArr = explode('/', ltrim($route, '/'));
                            $action = end($routeArr);
                            // 过滤actions里的方法，不算做menu
                            if ($action !== '*' && !in_array($action, $actions)) {
                                $method = $class->getMethod('action' . Inflector::id2camel($action));
                                $inheritdoc = $method->getDocComment();
                                if ($inheritdoc) {
                                    //解析注释，获取注释标题,有注释的才加入菜单
                                    $match = $this->getDoc($inheritdoc);
                                    if ($match) {
                                        $menuName = trim($match[0][1]);
                                        $menu[$moduleId][$controllerName]['actions'][$Id]['route'] = $route;
                                        $menu[$moduleId][$controllerName]['actions'][$Id]['doc'] = $menuName;
                                        // 创建permission
                                        $oldRermission = $auth->getPermission($route);
                                        if ( empty($oldRermission) ) {
                                            $routePermission = $auth->createPermission($route);
                                            $routePermission->description = $menuName;
                                            $auth->add($routePermission);
//                                            $auth->addChild($controllerPermission, $routePermission);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $menu;
    }

    /**
     * 获取文档
     *
     * 获取方法的注释内容
     * @param $doc
     */
    private function getDoc($doc) {
        $matches = [];
        $pattern = "#^\s+\*([\S\s].+)$#m";
        preg_match_all($pattern, $doc, $matches, PREG_SET_ORDER);
        return $matches;
    }

    private function buildTree($rawData, $parent = null, $assignment = []) {
        $result = $item = [];
        foreach ($rawData as $v) {
            if ($v['parent'] === $parent) {
                $item = $this->buildTree($rawData, $v['route'], $assignment);
                $v['text'] = $v['name'];  //改名
                if ( in_array($v['route'], $assignment) ) {
                    $v['state'] = ['checked' => true];
                }
                unset($v['name'], $v['parent']);
                //判断是否存在子数组
                $item && $v['nodes'] = $item;
                $result[] = $v;
            }
        }
        return $result;
    }

    private function getRouteRecrusive($module, &$result) {
        $token = "Get Route of '" . get_class($module) . "' with id '" . $module->uniqueId . "'";
        Yii::beginProfile($token, __METHOD__);
        try {
            foreach ($module->getModules() as $id => $child) {
                if ( in_array($id, ['gridview', 'debug', 'gii']) ) continue;  //todo
                if (($child = $module->getModule($id)) !== null) {
                    $this->getRouteRecrusive($child, $result);
                }
            }

            foreach ($module->controllerMap as $id => $type) {
                $this->getControllerActions($type, $id, $module, $result);
            }

            $namespace = trim($module->controllerNamespace, '\\') . '\\';
            $this->getControllerFiles($module, $namespace, '', $result);
            $result[$module->uniqueId][] = ($module->uniqueId === '' ? '' : '/' . $module->uniqueId) . '/*';
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    private function getControllerFiles($module, $namespace, $prefix, &$result) {
        $path = @Yii::getAlias('@' . str_replace('\\', '/', $namespace));
        $token = "Get controllers from '$path'";
        Yii::beginProfile($token, __METHOD__);
        try {
            if (!is_dir($path)) {
                return;
            }
            foreach (scandir($path) as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                if (is_dir($path . '/' . $file)) {
                    $this->getControllerFiles($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
                } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                    $id = Inflector::camel2id(substr(basename($file), 0, -14));
                    $className = $namespace . Inflector::id2camel($id) . 'Controller';
                    if (strpos($className, '-') === false && class_exists($className) && is_subclass_of($className, 'yii\base\Controller')) {
                        $this->getControllerActions($className, $prefix . $id, $module, $result);
                    }
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    private function getControllerActions($type, $id, $module, &$result)
    {
        $token = "Create controller with cofig=" . VarDumper::dumpAsString($type) . " and id='$id'";
        Yii::beginProfile($token, __METHOD__);
        try {
//            echo $type .'=>'. $id . '=>' . '<br>';
            $controller = Yii::createObject($type, [$id, $module]);
            $this->getActionRoutes($module, $controller, $result);
            $result[$module->uniqueId][$controller->uniqueId][] = '/' . $controller->uniqueId . '/*';
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }

    private function getActionRoutes($module, $controller, &$result) {
        $token = "Get actions of controller '" . $controller->uniqueId . "'";
        Yii::beginProfile($token, __METHOD__);
        try {
            $prefix = '/' . $controller->uniqueId . '/';
            foreach ($controller->actions() as $id => $value) {
                $result[$module->uniqueId][$controller->uniqueId][] = $prefix . $id;
            }
            $class = new \ReflectionClass($controller);
            foreach ($class->getMethods() as $method) {
                $name = $method->getName();
                if ($method->isPublic() && !$method->isStatic() && strpos($name, 'action') === 0 && $name !== 'actions') {
                    $result[$module->uniqueId][$controller->uniqueId][] = $prefix . Inflector::camel2id(substr($name, 6));
                }
            }
        } catch (\Exception $exc) {
            Yii::error($exc->getMessage(), __METHOD__);
        }
        Yii::endProfile($token, __METHOD__);
    }


}