<?php
/**
 * Created by PhpStorm.
 * User: yuyc
 * Date: 2016/9/22
 * Time: 11:39
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\EditForm;
use app\modules\admin\models\UserForm;
use common\models\User;
use kartik\form\ActiveForm;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use app\components\Controller;
use yii\web\Response;

/**
 * 成员管理
 * User: yuyc
 * Date: 2016/10/8
 * Time: 15:06
 */
class MemberController extends Controller {

    /**
     * 成员管理
     *
     * 后台管理员列表
     * @return string
     */
    public function actionIndex() {
        $data = null;
        $searchModel = new User();
        $userForm = new UserForm();
        $params = \Yii::$app->request->getQueryParams();
        $query = $searchModel->find()->select(['id','username', 'email', 'status', 'created_at']);

        #filter
        if ( $userForm->load($params) ) {
            $search = trim($userForm->username);
//            $data = array_filter($data, function ($item) use ($search) {
//                return (empty($search) || strpos(strtolower($item['username']), $search) !== false);
//            });
            $query->andFilterWhere(['like', 'username', $userForm->username]);
            $query->andFilterWhere(['like', 'email', $userForm->email]);
            $query->andFilterWhere(['=','status', $userForm->status]);
        }

        $items = $query->asArray()->all();

        array_walk($items, function($val, $key) use (&$data, $items) {
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
        ];

        return $this->render('index', $data);
    }

    /**
     *
     * 创建管理员
     *
     * 创建后台管理员
     * @return string
     */
    public function actionAjaxCreate() {
        $user = new UserForm();
        $user->setScenario('create');
        if ( $user->load(Yii::$app->request->post()) ) {
            if ($user = $user->signup()) {
                Yii::$app->session->setFlash('success', 'Create administrator success.');
                return $this->redirect('/admin/member');
            }
        } else {
            return  $this->renderAjax('form', ['model' => $user, 'action'=>'create', 'showPwdInput' => true]);
        }
    }

    /**
     * 编辑管理员
     *
     * 编辑管理员信息
     * @return string
     */
    public function actionAjaxEdit() {
        $modelForm = new UserForm();
        $modelForm->setScenario('edit');
        if ( $modelForm->load(Yii::$app->request->post()) ) {
            $params = [
                'username' => $modelForm->username,
                'email' => $modelForm->email,
            ];
            $user = $modelForm->edit($modelForm->id, $params);
            if ($user) {
                Yii::$app->session->setFlash('success', 'Update administrator success.');
            }
            $this->redirect('/admin/member');
        }


        if ( $userId = Yii::$app->request->get('id') ) {
            $user = $modelForm->getDetail($userId);
            $modelForm->id = $userId;
            $modelForm->username = $user->username;
            $modelForm->email = $user->email;
            return $this->renderAjax('form', ['model'=> $modelForm, 'action' => 'edit', 'showPwdInput' => false]);
        }
    }

    /**
     * 用户状态修改
     *
     * 进行用户状态修改，开启|禁用
     * todo Ajax、do 前缀的方法默认不会生成菜单
     * @return string
     */
    public function actionAjaxStatusChange() {
        $userForm = new UserForm();
        $request = Yii::$app->request->post();

        $user = User::findOne($request['id']);
        $status = filter_var($request['status'], FILTER_VALIDATE_BOOLEAN);
        if ($status) $status = 10;
        $user->status = $status;
        $id = $user->update();
        return Json::htmlEncode(['code' => 200, 'message' => 'update success.']);
    }

    # 表单验证，直接允许权限控制，在accessContrl增加正则匹配
    # todo
    public function actionAjaxValidateForm() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new UserForm();
        $model->setScenario(Yii::$app->request->get('scenarios'));
        $model->load(Yii::$app->request->post());
        return ActiveForm::validate($model);
    }
}