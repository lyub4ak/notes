<?php

use common\models\enum\Priority;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\NoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $priorities array */

$this->title = 'Notes';
?>
<div class="note-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Note', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'user_id',
            'userName',
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value'=>function ($note) {
                    return Html::a($note->name, ['update', 'id' => $note->id]);
                },
            ],
            'text:ntext',
            [
                'attribute' => 'priority',
                'format' => 'html',
                'value'=> function ($note) use ($priorities) {
                    return Html::encode($priorities[$note->priority]);
                },
                'contentOptions' => function ($note, $key, $index, $column) use ($priorities) {
                    switch ($note->priority) {
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
            [
                'attribute' => 'is_done',
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 100px; text-align: center'],
                'value'=>function ($note) {
                    $input = '<input type="checkbox" class="js-done" data-note-id='.$note->id;
                    return $note->is_done ? $input.' checked>' : $input.'>';
                },
                'filter' => [
                  '0' => 'Not Done',
                  '1' => 'Done',
                ],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
            ],
        ],
    ]); ?>

</div>

<?php
$url = json_encode(Url::to('/admin/note/save'));
$script = <<< JS
    $(".js-done").on("change", function() {
        const checkbox = $(this);
        const noteId = $(this).data('note-id');
        const isDone = $(this).is(':checked') ? 1 : 0;
        $.ajax({
            url: $url,
            type: 'POST',
            data:{
                'noteId': noteId,
                'isDone': isDone
            },
            success: function (response) {
                if (response['success']) {
                    console.log('Note ID=' + noteId + ' successful saved!');
                } else {
                    console.log('%c Note ID=' + noteId + ' did not save!', 'color: red');
                    checkbox.prop('checked', !isDone)
                }
            }
        });
    });
JS;
$this->registerJs($script);

$style = <<<CSS
    th {
        text-align: center; 
    }
CSS;

$this->registerCss($style);
?>
