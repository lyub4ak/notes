<?php

namespace frontend\controllers;

use common\models\enum\Priority;
use Yii;
use common\models\Task;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller
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
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the page of note with this task.
     *
     * @param $noteId
     * @return mixed
     */
    public function actionCreate($noteId)
    {
        $task = new Task();
        $task->note_id = $noteId;

        if ($task->load(Yii::$app->request->post()) && $task->save()) {
            self::checkNoteDone($task, $task->is_done);
            return $this->redirect(['note/update', 'id' => $noteId]);
        }

        return $this->render('create', [
            'model' => $task,
            'priorities' => Priority::$list,
        ]);
    }

    /**
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws Yii\web\HttpException if access forbidden.
     */
    public function actionUpdate($id)
    {
        $task = $this->findModel($id);
        if(Yii::$app->user->can('manageNote', ['note' => $task->note])) {
            if ($task->load(Yii::$app->request->post()) && $task->save()) {
                self::checkNoteDone($task, $task->is_done);

                return $this->redirect(['/note/update', 'id' => $task->note_id]);
            }
            return $this->render('update', [
                'model' => $task,
                'priorities' => Priority::$list,
            ]);
        } else {
            throw new HttpException(403, 'Access forbidden.');
        }
    }

    /**
     * Deletes an existing Task model.
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
        $task = $this->findModel($id);
        if(Yii::$app->user->can('manageNote', ['note' => $task->note])) {
            $task->delete();
            self::checkNoteDone($task, true);

            return $this->redirect(['/note/update', 'id' => $task->note_id]);
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
        $task = Task::findOne($post['taskId']);
        $success = false;
        if(Yii::$app->user->can('manageNote', ['note' => $task->note])) {
            // updates note
            $task->is_done = $post['isDone'] ? 1 : 0;
            $success = $task->save();
        }

        return [
            'success' => $success,
            'isNoteUpdate' => self::checkNoteDone($task, $post['isDone'])
        ];
    }

    /**
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Task the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Whether all tasks are done - checks note, otherwise - uncheck note.
     *
     * @param Task $task
     * @param bool $isDone Whether note is done.
     * @return bool Whether note is updated.
     */
    public static function checkNoteDone (Task $task, bool $isDone): bool
    {
        $note = $task->note;

        $isNoteUpdate = false;
        if ($isDone) {
            // whether current task is done checks other tasks
            // tasks of this note where is_done=0
            $tasks = $note->getTasks()
                ->andWhere(['is_done' => 0])
                ->all();

            // if all tasks are done - checks note
            if (!$tasks) {
                $note->is_done = 1;
                $isNoteUpdate = $note->save();
            }
        } elseif($note->is_done != 0) {
            // whether current task has not been done - uncheck note
            $note->is_done = 0;
            $isNoteUpdate = $note->save();
        }

        return $isNoteUpdate;
    }
}
