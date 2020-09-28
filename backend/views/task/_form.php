<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Task */
/* @var $form yii\widgets\ActiveForm */
/* @var $priorities array */

?>

<div class="task-form row">

  <div class="col-md-4"><?php $form = ActiveForm::begin(); ?>

      <?= $form->field($model, 'text')->textInput(['maxlength' => true]) ?>

      <?= $form->field($model, 'priority')->dropDownList($priorities) ?>

      <?= $form->field($model, 'date')->input('date')->label('Execution Date') ?>

      <?= $form->field($model, 'is_done')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

      <?php ActiveForm::end(); ?></div>

</div>
