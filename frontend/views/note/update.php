<?php

use common\models\enum\Priority;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Note */
/* @var $priorities array */
/* @var $taskSearch common\models\NoteSearch */
/* @var $taskProvider yii\data\ActiveDataProvider */

$this->title = 'Update Note: ' . $model->name;
?>
<div class="note-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'priorities' => $priorities,
    ]) ?>

    <h2>Tasks of this note</h2>

    <?= GridView::widget([
        'dataProvider' => $taskProvider,
        'filterModel' => $taskSearch,
        'columns' => [
            'text',
            [
                'attribute' => 'priority',
                'format' => 'html',
                'value'=> function ($task) use ($priorities) {
                    return Html::encode($priorities[$task->priority]);
                },
                'contentOptions' => function ($task, $key, $index, $column) use ($priorities) {
                    switch ($task->priority) {
                        case Priority::LOW:
                            return ['style' => 'color: limegreen'];
                            break;
                        case Priority::MIDDLE:
                            return ['style' => 'color: orange'];
                            break;
                        case Priority::HIGH:
                            return ['style' => 'color: red'];
                            break;
                        default: return [];
                    }
                },
                'filter' => $priorities,
            ],
            'date',
            [
                'attribute' => 'is_done',
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 100px; text-align: center'],
                'value'=>function ($task) {
                    $input = '<input type="checkbox" class="js-done-task" data-task-id='.$task->id;
                    return $task->is_done ? $input.' checked>' : $input.'>';
                },
                'filter' => [
                    '0' => 'Not Done',
                    '1' => 'Done',
                ],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                  'update' => function ($url, $task) {
                      $url = Url::to(['/task/update', 'id' => $task->id]);
                      return Html::a('<span class="glyphicon glyphicon-edit"', $url);
                  },
                  'delete' => function ($url, $task) {
                      $url = Url::to(['/task/delete', 'id' => $task->id]);
                      $params = [
                        'data-pjax' => 0,
                        'data-confirm' => 'Are you sure you want to delete this item?',
                        'data-method' => 'post'
                      ];
                      return Html::a('<span class="glyphicon glyphicon-remove"', $url, $params);
                  },
                ],
            ],
        ],
    ]); ?>

    <p>
        <?= Html::a('Add Task', ['task/create', 'noteId' => $model->id], ['class' => 'btn btn-success']) ?>
    </p>
</div>

<?php
$url = json_encode(Url::to('/task/save'));
$script = <<< JS
    $(".js-done-task").on("change", function() {
        const checkbox = $(this);
        const taskId = $(this).data('task-id');
        const isDone = $(this).is(':checked') ? 1 : 0;
        $.ajax({
            url: $url,
            type: 'POST',
            data:{
                'taskId': taskId,
                'isDone': isDone
            },
            success: function (response) {
                if(response['success']) {
                    console.log('Task ID=' + taskId + ' successful saved!');
                    if(response['isNoteUpdate']) {
                        // updates checkbox of note
                        $('.js-done-note').prop('checked', isDone);
                    }
                } else {
                    console.log('%c Task ID=' + taskId + ' did not save!', 'color: red');
                    checkbox.prop('checked', !isDone)
                }
            }
        });
    });
JS;
$this->registerJs($script);

$style = <<<CSS
    .glyphicon {
        margin: 0 5px;
    }
CSS;

$this->registerCss($style);
