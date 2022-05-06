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

    <?= $form->field($model, 'title', ['template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Title').'</span>{input}</div>'])->textInput()->label(false) ?>

    <?= $form->field($model, 'content')->widget(Yii::$app->setting->get('ojEditor'))->label(false); ?>

    <?= $form->field($model, 'status')->radioList([
        2 => Yii::t('app', '全文显示'),
        1 => Yii::t('app', '列表显示'),
        0 => Yii::t('app', 'Hidden')
    ]) ?>

    <?= $form->field($model, 'entity_id')->radioList([
        1 => Yii::t('app', '固顶'),
        0 => Yii::t('app', '不固顶')
    ]) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
