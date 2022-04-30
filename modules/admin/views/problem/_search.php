<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ProblemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="problem-search row">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'class' => 'form-inline',
            'data-pjax' => 1
        ],
    ]); ?>


    <div class="col-md-3">
        <?= $form->field($model, 'id', [
            'template' => "{label}\n<div class=\"input-group btn-group-justified\"><span style='width:30%' class=\"input-group-addon\"><span class='glyphicon glyphicon-book'></span></span>{input}</div>",
        ])->textInput(['maxlength' => 128, 'autocomplete' => 'off', 'placeholder' => Yii::t('app', 'Problem ID')])->label(false) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'title', [
            'template' => "{label}\n<div class=\"input-group btn-group-justified\"><span style='width:30%' class=\"input-group-addon\">标题</span>{input}</div>",
        ])->textInput(['maxlength' => 128, 'autocomplete' => 'off', 'placeholder' => Yii::t('app', 'Title')])->label(false) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'source', [
            'template' => "{label}\n<div class=\"input-group btn-group-justified\"><span style='width:30%' class=\"input-group-addon\">来源</span>{input}</div>",
        ])->textInput(['maxlength' => 128, 'autocomplete' => 'off', 'placeholder' => Yii::t('app', 'Source')])->label(false) ?>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <div class="btn-group btn-group-justified">
                <div class="btn-group">
                    <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> " . Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                </div>
                <div class="btn-group">
                    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>