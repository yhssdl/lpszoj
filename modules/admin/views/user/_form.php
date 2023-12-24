<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'newPassword')->textInput() ?>

    <?= $form->field($model, 'nickname')->textInput() ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'memo')->textInput() ?>

    <?= $form->field($model, 'role')->radioList([
        $model::ROLE_PLAYER => '参赛用户',
        $model::ROLE_USER => '普通用户',
        $model::ROLE_VIP => 'VIP用户',
        $model::ROLE_TEACHER => '管理教师',
        $model::ROLE_ADMIN => '系统管理员'
    ]) ?>

    <div class="form-group">
    <div class="row"><div class="col-md-2 col-md-offset-5"><?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?></div></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
