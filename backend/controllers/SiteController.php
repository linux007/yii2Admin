<?php
namespace backend\controllers;

use Yii;
use app\components\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\httpclient\Client;

/**
 * Site controller
 */
class SiteController extends Controller
{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $backgroudCacheKey = 'bing:everyDay:backgroud';
        $background = Yii::$app->fileCache->get($backgroudCacheKey);

        if ($background === false) {
            $cn_bing = 'http://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1';
            $httpClient = new Client();
            $response = $httpClient->createRequest()
                ->setMethod('get')
                ->setUrl($cn_bing)
                ->send();

            if ( $response->getIsOk() ) {
                $background = $response->data['images'][0]['url'];
            }

            $duration = strtotime(date('Y-m-d')) + 86400 - time();
            Yii::$app->fileCache->set($backgroudCacheKey, $background, $duration);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
                'backgroud' => $background,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
