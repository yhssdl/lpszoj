<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\polygon\models\Problem */

$this->title = $model->title;
$this->params['model'] = $model;
?>
<div class="problem-solution animate__animated animate__fadeInUp">

    <div class="alert alert-light"><i class="glyphicon glyphicon-info-sign"></i> 您可以在此处为题目编写详细的解答过程</div>


    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'solution')->widget(Yii::$app->setting->get('ojEditor'))->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>