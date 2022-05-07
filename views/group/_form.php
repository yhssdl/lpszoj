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

    <?= $form->field($model, 'name', ['template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Group Name').'</span>{input}</div>'])->textInput()->label(false) ?>

    <?= $form->field($model, 'logo_url', ['template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Logo Url').'</span>{input}</div>'])->textInput()->label(false) ?>
    <p class="hint-block">可以填写小组LOGO的URL地址，如果留空就显示默认图标。您可以使用下方的编辑器上传图标LOGO后，将获取的URL填写到这儿。</p>

    <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'join_policy')->radioList([
        Group::JOIN_POLICY_INVITE => Yii::t('app', 'Invite Only'),
        Group::JOIN_POLICY_APPLICATION => Yii::t('app', 'Application & Approve'),
        Group::JOIN_POLICY_FREE => Yii::t('app', 'Free')
    ])?>

    <?= $form->field($model, 'status')->radioList([
        Group::STATUS_VISIBLE => Yii::t('app', 'Visible'),
        Group::STATUS_HIDDEN => Yii::t('app', 'Hidden')
    ])->hint('可见：用户可在探索页面发现。') ?>


    <div class="alert alert-light"><i class="fa fa-info-circle"></i> 小组公告仅小组成员可见。</div>

    <?= $form->field($model, 'kanban', [
        'template' => "{input}",
    ])->widget(Yii::$app->setting->get('ojEditor')); ?>

    <div class="form-group">
    <div class="row"><div class="col-md-2 col-md-offset-5"><?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?></div></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
