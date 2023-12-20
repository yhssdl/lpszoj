<?php

use app\models\User;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="solution-search row">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'class' => 'form-inline',
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="col-md-2">
        <?= $form->field($model, 'username', [
            'template' => "{label}\n<div class=\"input-group\"><span class=\"input-group-addon\"><span class='fa fa-user'></span></span>{input}</div>",
        ])->textInput(['maxlength' => 128, 'autocomplete' => 'off', 'placeholder' => Yii::t('app', 'Username')])->label(false) ?>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'nickname', [
            'template' => "{label}\n<div class=\"input-group\"><span class=\"input-group-addon\"><span class='fa fa-user-o'></span></span>{input}</div>",
        ])->textInput(['maxlength' => 128, 'autocomplete' => 'off', 'placeholder' => Yii::t('app', 'Nickname')])->label(false) ?>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'email', [
            'template' => "{label}\n<div class=\"input-group\"><span class=\"input-group-addon\"><span class='fa fa-envelope'></span></span>{input}</div>",
        ])->textInput(['maxlength' => 128, 'autocomplete' => 'off', 'placeholder' => Yii::t('app', 'Email')])->label(false) ?>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'pagesize', [
            'template' => "{label}\n<div class=\"input-group\"><span class=\"input-group-addon\"><span class='fa fa-list'></span></span>{input}</div>",
        ])->dropDownList([
            50 => '每页50项',
            100 => '每页100项',
            200 => '每页200项',
            500 => '每页500项',
        ])->label(false) ?>
    </div>   

    <div class="col-md-2">
        <?= $form->field($model, 'role', [
            'template' => "{label}\n<div class=\"input-group\"><span class=\"input-group-addon\">" . Yii::t('app', 'Role') . "</span>{input}</div>",
        ])->dropDownList([
            '' => '所有用户',
            User::STATUS_DISABLE => '禁用用户',
            User::ROLE_PLAYER => '参赛用户',
            User::ROLE_USER => '普通用户',
            User::ROLE_VIP => 'VIP 用户',
            User::ROLE_ADMIN => '管理员',

        ])->label(false) ?>
    </div>    

    <div class="col-md-2">
        <div class="form-group">
            <div class="btn-group  btn-group-justified">
                <div class="btn-group">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                </div>
                <div class="btn-group">
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>