<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SolutionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="solution-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'class' => 'form-inline',
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'problem_id', [
                'template' => "{label}\n<div class=\"input-group btn-group-justified\"><span style='width:30%' class=\"input-group-addon\"><span class='fa fa-book'></span></span>{input}</div>",
            ])->textInput(['maxlength' => 128, 'autocomplete'=>'off', 'placeholder' => Yii::t('app', 'Problem ID')])->label(false) ?>
        </div>
        <div class="col-lg-2">
        <?= $form->field($model, 'username', [
            'template' => "{label}\n<div class=\"input-group btn-group-justified\"><span style='width:30%' class=\"input-group-addon\"><span class='fa fa-user'></span></span>{input}</div>",
        ])->textInput(['maxlength' => 128, 'autocomplete'=>'off', 'placeholder' => Yii::t('app', 'Who')])->label(false) ?>
        </div>
        <div class="col-lg-3" >
        <?= $form->field($model, 'result', [
            'template' => "{label}\n<div class=\"input-group btn-group-justified\"><span style='width:30%' class=\"input-group-addon\">" . Yii::t('app', 'Result') . "</span>{input}</div>",
        ])->dropDownList($model::getResultList())->label(false) ?>
        </div>
        <div class="col-lg-3">
        <?= $form->field($model, 'language', [
            'template' => "{label}\n<div class=\"input-group btn-group-justified\"><span style='width:30%' class=\"input-group-addon\">" . Yii::t('app', 'Lang') . "</span>{input}</div>",
        ])->dropDownList($model::getLanguageList())->label(false) ?>
        </div>
        <div class="col-lg-2">
        <div class="form-group">
            <div class="btn-group btn-group-justified">
                <div class="btn-group">
                    <?= Html::submitButton('<span class="fa fa-search"></span> '.Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                </div>
                <div class="btn-group">
                    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
                </div>
            </div>        
        </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<br>