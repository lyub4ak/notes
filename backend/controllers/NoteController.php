<?php

namespace backend\controllers;

use common\models\enum\Priority;
use Yii;
use common\models\NoteSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * NoteController implements the CRUD actions for Note model.
 */
class NoteController extends \frontend\controllers\NoteController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Note models.
     * @return mixed
     */
    public function actionIndex()
    {
        $queryParams = Yii::$app->request->queryParams;
        $queryParams['NoteSearch']['is_deleted'] = 0;
        $searchModel = new NoteSearch();
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'priorities' => Priority::$list,
        ]);
    }
}
