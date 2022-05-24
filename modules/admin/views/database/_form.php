<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Discuss */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="discuss-form animate__animated animate__fadeInUp">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'command')->textarea(['rows' => '10','maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'alt_msg', ['template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Warning information').'</span>{input}</div>'])->textInput()->label(false) ?>

    <div class="form-group">
        <div class="row"><div class="col-md-2 col-md-offset-5"><?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?></div></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
