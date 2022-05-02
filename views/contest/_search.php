<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ContestSearch */
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
    <div class="col-lg-10">
        <?= $form->field($model, 'title')->textInput(['maxlength' => 128, 'autocomplete' => 'off', 'placeholder' => Yii::t('app', 'Title')])->label(false) ?>
    </div>
    <div class="col-lg-2">
        <div class="btn-group btn-block search-submit">
            <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i> ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-block']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>