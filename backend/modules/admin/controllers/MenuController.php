<?php
/**
 * Created by PhpStorm.
 * User: yuyc
 * Date: 2016/10/26
 * Time: 17:00
 */

namespace app\modules\admin\controllers;


use app\components\Controller;

/**
 * 菜单管理
 *
 * 菜单相关管理
 * @package app\modules\admin\controllers\
 */

class MenuController extends Controller
{
    /**
     * 菜单管理
     *
     * 菜单管理页面
     * @return string
     */
    public function actionIndex() {
        return $this->render('index');
    }
}