<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $profile app\models\UserProfile */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<?php if (Yii::$app->setting->get('isChangeNickName')==1): ?>
	<?= $form->field($model, 'nickname')->textInput() ?>
<?php elseif(Yii::$app->setting->get('isChangeNickName')==2 && $model->username === $model->nickname ): ?>
	<?= $form->field($model, 'nickname')->textInput() ?>
     	<p class="hint-block">
           昵称只能修改一次，请谨慎修改。<br>
      </p>
<?php else: ?>
	<?= $form->field($model, 'nickname')->dropDownList([$model->nickname=>$model->nickname]) ?>	
<?php endif; ?>	

<?= $form->field($profile, 'qq_number')->textInput() ?>

<?= $form->field($profile, 'student_number')->textInput() ?>

<?= $form->field($profile, 'gender')->radioList([Yii::t('app', 'Male'), Yii::t('app', 'Female')]) ?>

<?= $form->field($profile, 'major')->textInput() ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>
</div>

<?php ActiveForm::end(); ?>
