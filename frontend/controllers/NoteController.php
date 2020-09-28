<?php

namespace frontend\controllers;

use common\models\enum\Priority;
use common\models\TaskSearch;
use Yii;
use common\models\Note;
use common\models\NoteSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * NoteController implements the CRUD actions for Note model.
 */
class NoteController extends Controller
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
                        'roles' => ['@'],
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
        $userId = Yii::$app->user->id;

        $queryParams = Yii::$app->request->queryParams;
        $queryParams['NoteSearch']['user_id'] = $userId;
        $queryParams['NoteSearch']['is_deleted'] = 0;
        $searchModel = new NoteSearch();
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'priorities' => Priority::$list,
        ]);
    }

    /**
     * Creates a new Note model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $note = new Note();
        $note->user_id = Yii::$app->user->id;

        if ($note->load(Yii::$app->request->post()) && $note->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $note,
            'priorities' => Priority::$list,
        ]);
    }

    /**
     * Updates an existing Note model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id Note ID.
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws HttpException
     */
    public function actionUpdate($id)
    {
        $note = $this->findModel($id);

        if (Yii::$app->user->can('manageNote', ['note' => $note])) {
            $note->user_id = Yii::$app->user->id;

            $queryParams = Yii::$app->request->queryParams;
            $queryParams['TaskSearch']['note_id'] = $id;
            $queryParams['TaskSearch']['is_deleted'] = 0;
            $taskSearch = new TaskSearch();
            $taskProvider = $taskSearch->search($queryParams);

            $success = $note->load(Yii::$app->request->post());
            if ($success && $note->getOldAttribute('is_done') != $note->is_done) {
                $success = self::allTasksDone($note, $note->is_done);
            }
            if ($success && $note->save()) {
                return $this->redirect(['update', 'id' => $note->id]);
            }
            return $this->render('update', [
                'model' => $note,
                'priorities' => Priority::$list,
                'taskSearch' => $taskSearch,
                'taskProvider' => $taskProvider,
            ]);
        } else {
            throw new HttpException(403, 'Access forbidden.');
        }
    }

    /**
     * Deletes an existing Note model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $note = $this->findModel($id);

        if (Yii::$app->user->can('manageNote', ['note' => $note])) {
            $note->delete();

            return $this->redirect(['index']);
        } else {
            throw new HttpException(403, 'Access forbidden.');
        }
    }

    /**
     * Checks or unchecks note and all tasks of this note.
     *
     * @return bool[]
     */
    public function actionSave() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $post = Yii::$app->request->post();
        $note = Note::findOne($post['noteId']);
        $success = false;
        if(Yii::$app->user->can('manageNote', ['note' => $note])) {
            // updates note
            $note->is_done = $post['isDone'];
            if($note->save() && self::allTasksDone($note, $post['isDone'])) {
                $success = true;
            } else {
                $success = false;
            }
        }

        return [
            'success' => $success,
        ];
    }

    /**
     * Finds the Note model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Note the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Note::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Checks or unchecks all tasks of note.
     *
     * @param Note $note
     * @param bool $isDone Whether note is done.
     * @return bool Whether all tasks saved without errors.
     */
    public static function allTasksDone (Note $note, bool $isDone): bool
    {
        // tasks which need update
        $tasks = $note->getTasks()
            ->andWhere(['<>', 'is_done', $isDone])
            ->all();

        // updates tasks
        foreach ($tasks as $task) {
            $task->is_done = $isDone ? 1 : 0;
            if(!$task->save()) {
                return false;
            }
        }

        return true;
    }
}
