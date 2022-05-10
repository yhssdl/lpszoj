<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $user \app\models\LoginForm */
/* @var $title string */

$this->registerCss('
#login-form .form-control {
  position: relative;
  height: auto;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  padding: 10px;
  font-size: 16px;
}
.site-login .login-title{
  font-size:28px;font-weight: bold;text-align:center;margin-top:20px;margin-bottom: 20px;
}
.site-login .left img,.form-signin .left img{
  width:100%;
  max-width:80%;
}
');
?>
<div class="panel panel-default">
  <div class="panel-body">
    <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>
    <div class="row site-login ">
      <div class="col-md-6 hidden-xs left text-center">
        <img src="/images/login.png">
      </div>
      <div class="col-md-6">
      <div class="login-title"><?= Yii::t('app', 'User Login') ?></div>
        <?= $form->field($user, 'username', [
          'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-user"></i></span>{input}</div>{error}',
          'inputOptions' => [
            'placeholder' => $user->getAttributeLabel('username'),
          ],
        ])->label(false);
        ?>
        <?= $form->field($user, 'password', [
          'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-lock"></i></span>{input}</div>{error}',
          'inputOptions' => [
            'placeholder' => $user->getAttributeLabel('password'),
          ],
        ])->passwordInput()->label(false);
        ?>
        <div class="form-group">
          <?= Html::submitButton(Yii::t('app', 'Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
          <?= Html::a(Yii::t('app', 'Signup'), ['/site/signup']) ?>
        </div>
      </div>

    </div>



    <?php ActiveForm::end(); ?>
  </div>
</div>