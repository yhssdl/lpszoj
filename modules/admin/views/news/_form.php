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
        0 => Yii::t('app', 'Hidden'),
        1 => Yii::t('app', '列表显示'),
        2 => Yii::t('app', '全文显示'),
    ]) ?>

    <?= $form->field($model, 'entity_id')->radioList([
        0 => Yii::t('app', '不固顶'),
        1 => Yii::t('app', '固顶'),
    ]) ?>


    <div class="form-group">
        <div class="row"><div class="col-md-2 col-md-offset-5"><?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?></div></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
