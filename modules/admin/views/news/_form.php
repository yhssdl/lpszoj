<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Discuss */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="discuss-form animate__animated animate__fadeInUp">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title', ['template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Title').'</span>{input}</div>'])->textInput()->label(false) ?>

    <?= $form->field($model, 'content')->widget(Yii::$app->setting->get('ojEditor'))->label(false); ?>

    <?= $form->field($model, 'status')->radioList([
        1 => Yii::t('app', 'Visible'),
        0 => Yii::t('app', 'Hidden')
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
