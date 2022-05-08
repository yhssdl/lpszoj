<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Signup');
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



<?php if (Yii::$app->setting->get('isUserReg')) : ?>
    <div class="site-login col-md-10 col-md-offset-1 border-radius-5 login_bg_white  animate__animated animate__fadeInUp">
        <div class="col-md-6 hidden-xs left text-center">
            <img src="/images/reg.png">
        </div>
        <div class="col-md-6">
            <div class="login-title">账户注册</div>


            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

            <?= $form->field($model, 'username', [
                'template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'User') . '</span>{input}</div>',
            ])->textInput(['maxlength' => 128, 'autocomplete' => 'off']) ?>


            <?= $form->field($model, 'email', [
                'template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Email') . '</span>{input}</div>',
            ])->textInput(['maxlength' => 128, 'autocomplete' => 'off']) ?>


            <?= $form->field($model, 'studentNumber', [
                'template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Student Number') . '</span>{input}</div>',
            ])->textInput(['maxlength' => 128, 'autocomplete' => 'off', 'placeholder' => '可不填']) ?>


            <?= $form->field($model, 'password', [
                'template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Password') . '</span>{input}</div>',
            ])->passwordInput(['maxlength' => 128, 'autocomplete' => 'off']) ?>


            <?= $form->field($model, 'verifyCode', [
                'inputOptions' => [
                    'placeholder' => $model->getAttributeLabel('verifyCode'),
                ],
            ])->widget(\yii\captcha\Captcha::class, [
                'template' => '<div class="input-group btn-group-justified">{input}</div>
            <div class="list-group text-center" style="margin-top: 1rem;"><div class="list-group-item">{image}</div></div>',
            ])->label(false);
            ?>


            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Signup'), ['class' => 'btn btn-success btn-block', 'name' => 'signup-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php else : ?>
    <div class="alert alert-light"><i class="fa fa-info-circle"></i> 当前未开放注册！</div>
<?php endif; ?>