<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Signup');
?>


<div class="alert alert-success">
    <i class="fa fa-info-circle"></i> 欢迎注册<?= Yii::$app->setting->get('schoolName') ?>在线评测系统
</div>

<?php if (Yii::$app->setting->get('isUserReg')) : ?>
    <div class="animate__animated animate__fadeInUp">

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
            <div class="list-group text-center" style="margin-top: 1rem;"><div class="list-group-item">{image}<a href="#" class="text-secondary" data-toggle="tooltip" title="点击图片以重置验证码"><span class="fa fa-info-circle"></span></a></div></div>',
        ])->label(false);
        ?>


        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Signup'), ['class' => 'btn btn-success btn-block', 'name' => 'signup-button']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<?php else : ?>
    <div class="alert alert-light"><i class="fa fa-info-circle"></i> 当前未开放注册！</div>
<?php endif; ?>