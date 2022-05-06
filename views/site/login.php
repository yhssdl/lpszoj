<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Login');
?>
<div class="row">
    <div class="site-login col-md-8 col-md-offset-2 animate__animated animate__fadeInUp">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => [
                'class' => 'form-signin'
            ]
        ]); ?>
        <div class="alert alert-light"><i class="fa fa-info-circle"></i> 欢迎回来。</div>
        <img src="<?= Yii::getAlias('@web') . '/images/login.jpg' ?>" width="100%" class="card-img-top d-none d-md-block"><br><br>
        <?= $form->field($model, 'username', [
            'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-user"></i></span>{input}</div>{error}',
            'inputOptions' => [
                'placeholder' => $model->getAttributeLabel('username'),
            ],
        ])->label(false);
        ?>
        <?= $form->field($model, 'password', [
            'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-lock"></i></span>{input}</div>{error}',
            'inputOptions' => [
                'placeholder' => $model->getAttributeLabel('password'),
            ],
        ])->passwordInput()->label(false);
        ?>

        <?php if ($model->scenario == 'withCaptcha') : ?>
            <?= $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::className()); ?>
        <?php endif; ?>
        <span class="float-left"> <?= $form->field($model, 'rememberMe')->checkbox() ?></span>
        <span class="float-right">
            <?= Html::a('忘记密码', ['site/request-password-reset']) ?>
        </span>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Login'), ['class' => 'btn btn-success btn-block', 'name' => 'login-button']) ?>
            
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>