<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ContestPrint */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="print-source-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'source')->textarea(['rows' => 20])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-success btn-block']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
