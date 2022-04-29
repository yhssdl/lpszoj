<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GroupSearch */
/* @var $form yii\widgets\ActiveForm */
?>


<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'options' => [
        'class' => ''
    ],
]); ?>
<div class="row">
    <div class="col-lg-10" style="margin-bottom: 1rem;">
        <?= $form->field($model, 'name')->textInput(['maxlength' => 128, 'autocomplete' => 'off', 'placeholder' => Yii::t('app', 'Title')])->label(false) ?>
    </div>

    <div class="col-lg-2" style="margin-bottom: 1rem;">
        <?= Html::submitButton('<span class="glyphicon glyphicon-search"></span> ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-block']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>