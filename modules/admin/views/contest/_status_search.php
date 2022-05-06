<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SolutionSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $contest_id integer */
/* @var $nav string */
?>

<div class="solution-search row">

    <?php $form = ActiveForm::begin([
        'action' => ['status', 'id' => $contest_id],
        'method' => 'get',
        'options' => [
            'class' => 'form-inline',
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="col-md-3">
        <?= $form->field($model, 'problem_id', [
            'template' => "{label}\n<div class=\"input-group  btn-group-justified\"><span style='width:60px'  class=\"input-group-addon\"><span class='fa fa-book'></span></span>{input}</div>",
        ])->dropDownList($nav)->label(false) ?>
    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'username', [
            'template' => "{label}\n<div class=\"input-group  btn-group-justified\"><span style='width:60px'  class=\"input-group-addon\"><span class='fa fa-user'></span></span>{input}</div>",
        ])->textInput(['maxlength' => 128, 'autocomplete' => 'off', 'placeholder' => Yii::t('app', 'Who')])->label(false) ?>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'result', [
            'template' => "{label}\n<div class=\"input-group  btn-group-justified\"><span style='width:45%'  class=\"input-group-addon\">" . Yii::t('app', 'Result') . "</span>{input}</div>",
        ])->dropDownList($model::getResultList())->label(false) ?>
    </div>

    <div class="col-md-2">
        <?= $form->field($model, 'language', [
            'template' => "{label}\n<div class=\"input-group  btn-group-justified\"><span style='width:60px'  class=\"input-group-addon\">" . Yii::t('app', 'Lang') . "</span>{input}</div>",
        ])->dropDownList($model::getLanguageList())->label(false) ?>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <div class="btn-group  btn-group-justified">
                <div class="btn-group">
                    <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                </div>
                <div class="btn-group">
                    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>