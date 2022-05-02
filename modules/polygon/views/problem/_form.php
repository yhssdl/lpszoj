<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Problem */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="problem-form animate__animated animate__fadeInUp">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'title', ['template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Title').'</span>{input}</div>{hint}'])
    ->textInput()->hint('<i class="glyphicon glyphicon-info-sign"></i>  题目标题（也可标注需要管理员留意的信息）') ?>

  
    <?= $form->field($model, 'time_limit', [
        'template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Time Limit').'</span>{input}<span class="input-group-addon">'.Yii::t('app', 'Second').'</span></div>{hint}',
    ])->textInput(['maxlength' => 128, 'autocomplete'=>'off'])->hint('<i class="glyphicon glyphicon-info-sign"></i> 单个测试点时间限制：Java 和 Python 有 2s 额外时间')  ?>

    <?= $form->field($model, 'memory_limit', [
        'template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Memory Limit').'</span>{input}<span class="input-group-addon">MB</span></div>{hint}',
    ])->textInput(['maxlength' => 128, 'autocomplete'=>'off'])->hint('<i class="glyphicon glyphicon-info-sign"></i> 空间限制：Java 和 Python 有 64MB 额外空间')  ?>


    <?= $form->field($model, 'description')->widget(Yii::$app->setting->get('ojEditor')) ?>

    <?= $form->field($model, 'input')->widget(Yii::$app->setting->get('ojEditor')) ?>

    <?= $form->field($model, 'output')->widget(Yii::$app->setting->get('ojEditor')) ?>

    <hr>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'sample_input')->label(Yii::t('app', 'Sample Input 1'))->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'sample_output')->label(Yii::t('app', 'Sample Output 1'))->textarea(['rows' => 6]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'sample_input_2')->label(Yii::t('app', 'Sample Input 2'))->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'sample_output_2')->label(Yii::t('app', 'Sample Output 2'))->textarea(['rows' => 6]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'sample_input_3')->label(Yii::t('app', 'Sample Input 3'))->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'sample_output_3')->label(Yii::t('app', 'Sample Output 3'))->textarea(['rows' => 6]) ?>
        </div>
    </div>
    <hr>

    <?= $form->field($model, 'spj')->radioList([
        '1' => Yii::t('app', 'Yes'),
        '0' => Yii::t('app', 'No')
    ])?>

    <?= $form->field($model, 'hint')->widget(Yii::$app->setting->get('ojEditor')) ?>

    <?= $form->field($model, 'tags')->textarea(['maxlength' => true, 'placeholder' => '可不填'])
        ->hint('<i class="glyphicon glyphicon-info-sign"></i> 多标签用逗号隔开。如：dfs, bfs, dp, 暴力，贪心，最短路') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
