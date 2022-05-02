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

    <?= $form->field($model, 'id', ['template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Problem ID').'</span>{input}</div>{hint}'])->textInput(['placeholder' => '可不填'])
        ->hint('<i class="glyphicon glyphicon-info-sign"></i> 此处用于指定题目ID，若不填，新建题目时题目ID会自动增长。新建题目时填写的ID不能为已经存在的ID') ?>

    <?= $form->field($model, 'title', ['template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Title').'</span>{input}</div>'])->textInput() ?>

    <?= $form->field($model, 'time_limit', [
        'template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Time Limit').'</span>{input}<span class="input-group-addon">'.Yii::t('app', 'Second').'</span></div>{hint}',
    ])->textInput(['maxlength' => 128, 'autocomplete'=>'off'])->hint('<i class="glyphicon glyphicon-info-sign"></i> 单个测试点时间限制：Java 和 Python 有 2s 额外时间')  ?>

    <?= $form->field($model, 'memory_limit', [
        'template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Memory Limit').'</span>{input}<span class="input-group-addon">MB</span></div>{hint}',
    ])->textInput(['maxlength' => 128, 'autocomplete'=>'off'])->hint('<i class="glyphicon glyphicon-info-sign"></i> 空间限制：Java 和 Python 有 64MB 额外空间')  ?>

    <?= $form->field($model, 'status')->radioList([
        1 => Yii::t('app', 'Visible'),
        0 => Yii::t('app', 'Hidden'),
        2 => Yii::t('app', 'Private')
    ])->hint(Yii::t('app', '<i class="glyphicon glyphicon-info-sign"></i> 可见：题目将在首页展示，任何用户可见。隐藏：题目仅在后台显示。私有：题目标题在前台可见，但信息仅VIP用户可见')) ?>

    <?= $form->field($model, 'description')->widget(Yii::$app->setting->get('ojEditor')); ?>

    <?= $form->field($model, 'input')->widget(Yii::$app->setting->get('ojEditor')); ?>

    <?= $form->field($model, 'output')->widget(Yii::$app->setting->get('ojEditor')); ?>

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

    <?= $form->field($model, 'spj')->radioList([
        '1' => Yii::t('app', 'Yes'),
        '0' => Yii::t('app', 'No')
    ]) ?>


    <?= $form->field($model, 'hint')->widget(Yii::$app->setting->get('ojEditor')); ?>

    <?= $form->field($model, 'source')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'tags')->textarea(['maxlength' => true, 'placeholder' => '可不填'])
        ->hint('<i class="glyphicon glyphicon-info-sign"></i> 多标签用逗号隔开。如：dfs, bfs, dp, 暴力，贪心，最短路') ?>

    <?= $form->field($model, 'contest_id')->label(Yii::t('app', 'Contest ID'))->dropDownList(\app\models\Contest::getContestList()) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
