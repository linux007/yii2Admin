<?php
/**
 * Created by PhpStorm.
 * User: yuyc
 * Date: 2016/9/22
 * Time: 11:39
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\Assignment;
use app\modules\admin\models\UserForm;
use kartik\form\ActiveForm;
use Yii;
use app\modules\admin\models\Role;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use yii\helpers\Url;
use app\components\Controller;
use yii\web\Response;
use common\models\User;

/**
 * Created by PhpStorm.
 * User: yuyc
 * Date: 2016/10/8
 * Time: 15:06
 */
class RoleController extends Controller {

    /**
     * 角色管理
     *
     * 用户角色管理列表
     * @return string
     */
    public function actionIndex() {
        $searchModel = new Role();
        $dataProvider = $searchModel->search(\Yii::$app->request->getQueryParams());
//        $searchModel->setScenario('default');
        Url::remember(['/admin/role'], 'role-index');
        $data = [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ];
        return $this->render('index', $data);
    }

    /**
     *
     * 创建角色
     *
     * 创建后台管理用户角色
     * @return string
     */
    public function actionCreate() {

        $time = time();
        $role = new Role();
        $role->created_at = $time;
        $role->updated_at = $time;
        $role->type = Role::TYPE_ROLE;
        $role->setScenario('create');
        if ( $role->load(Yii::$app->request->post()) && $role->save() ) {
//            Yii::$app->session->setFlash('success', '保存成功');
            return $this->redirect('/admin/role');
        } else {
            return  $this->renderAjax('create', ['model' => $role]);
        }

    }

    /**
     * 成员列表
     *
     * 显示角色成员分配列表
     * @return string
     */
    public function actionAssignMember() {
        $roleName = Yii::$app->request->get('roleName');

        $data = $checkedUsers = [];
        $searchModel = new User();
        $userForm = new UserForm();
        $params = \Yii::$app->request->getQueryParams();
        $query = $searchModel->find()->select(['id','username', 'email'])->where(['status' => 10]);

        #关联查询
        $assignmentsQuery = Assignment::find()->joinWith(['users'], false)->where(['auth_assignment.item_name' => $roleName]);
        # 显示执行的sql语句
        #$commanQuery = clone $authAssignments;
        #echo $commanQuery->createCommand()->getRawSql();
        $assignments = $assignmentsQuery->asArray()->all();
        if ( $assignments ) {
            foreach ($assignments as $val) {
                $checkedUsers[] = $val['user_id'];
            }
        }

        # 按条件获取系统所有用户
        if ( $userForm->load($params) ) {
            $query->andFilterWhere(['like', 'username', $userForm->username]);
        }
        $items = $query->asArray()->all();

        array_walk($items, function($val, $key) use (&$data, $items, $checkedUsers) {
            $val['checked'] = false;
            if ( in_array($val['id'], $checkedUsers) ) {
                $val['checked'] = true;
            }
            $data[$val['id']] = $val;
            unset($items[$key]);
        });

        $dataProvider  = new ArrayDataProvider([
            'allModels' => $data,
            'sort' => [
                'attributes' => ['username'],
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $data = [
            'dataProvider' => $dataProvider,
            'searchModel' => $userForm,
            'assignRole' => $roleName,
        ];
        return $this->render('member', $data);
    }


    public function actionDoAssignMember() {
        $checkedUsers = [];
        $roleName = Yii::$app->request->post('roleName');
        $memberIds = Yii::$app->request->post('memberIds', []);

        $referer = Url::previous('role-index');

        if ( empty($roleName) ) {
            Yii::$app->session->setFlash('warning', '无效的参数：roleName 不能为空');
            $this->redirect($referer);
        }

//        if ( empty($memberIds) ) {
//            return Json::htmlEncode(['code' => 201, 'message' => '请选择要分配的管理员', 'type' => 'warning']);
//        }

        #已经分配的用户
        $assignmentsQuery = Assignment::find()->joinWith(['users'], false)->where(['auth_assignment.item_name' => $roleName]);
        $assignments = $assignmentsQuery->asArray()->all();
        if ( $assignments ) {
            foreach ($assignments as $val) {
                $checkedUsers[] = $val['user_id'];
            }
        }

        $optionsDeleted = array_diff($checkedUsers, $memberIds);
        $optionsAdded = array_diff($memberIds, $checkedUsers);

        $auth = Yii::$app->getAuthManager();
        $role = $auth->getRole($roleName);

        #revoke
        if ( $optionsDeleted ) {
            foreach ($optionsDeleted as $userId) {
                $auth->revoke($role, $userId);
            }
        }
        # grant
        if ( $optionsAdded ) {
            foreach ($optionsAdded as $userId) {
                $auth->assign($role, $userId);
            }
        }

        Yii::$app->session->setFlash('success', '分配成员成功');
        return Json::htmlEncode(['code' => 200, 'message' => '分配成员成功']);
    }

    public function actionValidateForm() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new Role();
        $model->type = Role::TYPE_ROLE;
        $model->load(Yii::$app->request->post());
        return ActiveForm::validate($model);
    }
}