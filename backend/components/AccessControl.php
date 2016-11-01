<?php
/**
 * Created by PhpStorm.
 * User: yuyc
 * Date: 2016/10/31
 * Time: 16:17
 */

namespace app\components;

use yii\web\ForbiddenHttpException;
use Yii;
use yii\web\User;
use yii\di\Instance;
use yii\base\ActionFilter;

class AccessControl extends ActionFilter
{
    private $_user = 'user';

    public $allowActions = [];

    /**
     * 获取当前用户
     * @return null|object|User
     * @throws \yii\base\InvalidConfigException
     */
    public function getUser() {
        if ( !$this->_user instanceof User ) {
            $this->_user = Instance::ensure($this->_user, User::className());
        }
        return $this->_user;
    }

    public function beforeAction($action)
    {
        $actionId = $action->getUniqueId();
        $user = $this->getUser();
        if ($user->can('/'.$actionId)) {
            return true;
        }

        $this->denyAccess($user);
    }

    protected function denyAccess($user)
    {
        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }

    public function isActive($action)
    {
        $actionId = $action->getUniqueId();
        if ($actionId === Yii::$app->getErrorHandler()->errorAction) {
            return false;
        }

        if ($this->allowActions) {
            if (in_array($actionId, $this->allowActions)) {
                return false;
            }
        }
        return true;
    }
}