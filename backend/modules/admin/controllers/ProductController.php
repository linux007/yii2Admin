<?php
/**
 * Created by PhpStorm.
 * User: yuyc
 * Date: 2016/10/26
 * Time: 17:00
 */

namespace app\modules\admin\controllers;


use app\components\Controller;

class ProductController extends Controller
{
    public function actionIndex() {
        return $this->render('index');
    }
}