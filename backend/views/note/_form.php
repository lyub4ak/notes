<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Note */
/* @var $form yii\widgets\ActiveForm */
/* @var $priorities array */

?>

<div class="note-form row">

    <div class="col-md-4">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'priority')->dropDownList($priorities) ?>

        <?= $form->field($model, 'is_done')->checkbox(['class' => 'js-done-note']) ?>

      <div class="form-group">
          <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
      </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
