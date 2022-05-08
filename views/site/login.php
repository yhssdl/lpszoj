<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Login');
$this->registerCss("
    .wrap > .container {
        background-color: transparent !important;
        max-width:1170px;
    }
    .radius {
        border-radius: 0px !important;
        box-shadow: none !important;
    }

    .border-radius-5 {
        margin-bottom: 20px;
        border-radius: 4px;
        box-shadow: 0 1px 5px 0 rgba(0,0,0,.1);
    }

    .site-login .login-title{
        font-size:28px;font-weight: bold;text-align:center;margin-top:20px;margin-bottom: 20px;
    }

    .site-login .left img,.form-signin .left img{
        width:100%;
        max-width:80%;
    }

    .login_bg_white {
        padding: 25px 15px 20px;
        background-color: #FFF;
    }");
?>
<div class="row">

    <div class="site-login col-md-10 col-md-offset-1 border-radius-5 login_bg_white  animate__animated animate__fadeInUp">

    <div class="col-md-6 hidden-xs left text-center">
        <img src="/images/login.png">
    </div>
    <div class="col-md-6">
    <div class="login-title">账户登录</div>
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => [
                'class' => 'form-signin'
            ]
        ]); ?>
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
            <?= Html::submitButton(Yii::t('app', 'Login'), ['class' => 'btn btn-primary  btn-block form-control', 'name' => 'login-button']) ?>
            
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    </div>
</div>