<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Problem */

$this->title = Yii::t('app', $model->title);
$this->params['model'] = $model;
?>
<p class="lead"><?= Html::encode($this->title) ?></p>
<div class="problem-solution animate__animated animate__fadeInUp">

    <div class="alert alert-light">
        <i class=" fa fa-info-circle"></i> 您可以在此处为题目编写详细的解答过程。注意，若题目内容是被用户可见的，那么此处填写的题解也会被用户可见。查看题解按钮会出现在前台题目详情页面中。
    </div>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'solution')->widget(Yii::$app->setting->get('ojEditor'))->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>