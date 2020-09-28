<?php
namespace console\controllers;

use common\models\User;
use console\rbac\OwnerRule;
use Exception;
use Yii;
use yii\console\Controller;

/**
 * Manages RBAC.
 * @package console\controllers
 */
class RbacController extends Controller
{
    /**
     * Creates RBAC.
     * Command: yii rbac/init
     *
     * @throws \yii\base\Exception
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        // creates roles
        $admin = $auth->createRole('admin');
        $auth->add($admin);

        $user = $auth->createRole('user');
        $auth->add($user);

        // creates permission
        $manageNote = $auth->createPermission('manageNote');
        $manageNote->description = 'Allows manage note.';
        $auth->add($manageNote);

        // add the rule
        $rule = new OwnerRule;
        $auth->add($rule);

        // creates permission with rule
        $manageOwnNote = $auth->createPermission('manageOwnNote');
        $manageOwnNote->description = 'Allows manage own note.';
        $manageOwnNote->ruleName = $rule->name;
        $auth->add($manageOwnNote);

        // relates permissions
        $auth->addChild($manageOwnNote, $manageNote);

        // relates permission to role
        // admin can manage all notes.
        $auth->addChild($admin, $manageNote);

        // relates permission to role
        // user can manage only own notes.
        $auth->addChild($user, $manageOwnNote);

        echo 'Success!';
        return 0;
    }

    /**
     * Assigns role "admin" for user.
     * Command: yii rbac/assign-admin 1
     *
     * @param string $id Primary key of user which should be assigned as "admin".
     * @return int
     * @throws Exception
     */
    public function actionAssignAdmin($id)
    {
        $user = User::findOne($id);
        if(!$user) {
            echo 'User with this ID not found.';
            return 1;
        }

        $auth = Yii::$app->authManager;
        $role = $auth->getRole('admin');
        $auth->assign($role, $id);

        echo 'Success!';
        return 0;
    }
}