<?php

use app\models\Group;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Group */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name', ['template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Training Name').'</span>{input}</div>'])->textInput()->label(false) ?>

    <?= $form->field($model, 'logo_url', ['template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Logo Url').'</span>{input}</div>'])->textInput()->label(false) ?>
    <p class="hint-block">可以填写训练LOGO的URL地址，如果留空就显示默认图标。您可以使用下方的编辑器上传图标LOGO后，将获取的URL填写到这儿。</p>

    <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->radioList([
        Group::STATUS_VISIBLE => Yii::t('app', 'Visible'),
        Group::STATUS_HIDDEN => Yii::t('app', 'Hidden')
    ])->hint('可见：用户可在前台页面查看该训练。') ?>


    <div class="alert alert-light"><i class="fa fa-info-circle"></i> 训练公告在进入训练后展示。</div>

    <?= $form->field($model, 'kanban', [
        'template' => "{input}",
    ])->widget(Yii::$app->setting->get('ojEditor')); ?>

    <div class="form-group">
    <div class="row"><div class="col-md-2 col-md-offset-5"><?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?></div></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
