<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Problem */

$this->title = Yii::t('app', $model->title);
$this->params['model'] = $model;
$previousProblemID = $model->getPreviousProblemID(true);
$nextProblemID = $model->getNextProblemID(true);
?>
<div class="row">
    <div class="col-md-9 text-left">
        <div class="content-title"><?= $model->id . " : ".Html::encode($this->title) ?></div>
    </div>
    <div class="col-md-3 text-right">
        <div class="btn btn-link">
            <?= Html::a(
                '<i class="fa fa-arrow-left"></i>',
                $previousProblemID ? ['solution', 'id' => $previousProblemID] : 'javascript:void(0);',
                ['title'=>'上一题','disabled' => !$previousProblemID]
            ) ?>
        </div>
        <div class="btn btn-link">
            <?= Html::a(
            '<i class="fa fa-arrow-right"></i>',
            $nextProblemID ? ['solution', 'id' => $nextProblemID] : 'javascript:void(0);',
            ['title' => '下一题',  'disabled' => !$nextProblemID]
            ) ?>
        </div>      
    </div>
</div>
<div class="problem-solution animate__animated animate__fadeInUp">

    <div class="alert alert-light">
        <i class=" fa fa-info-circle"></i> 您可以在此处为题目编写详细的解答过程。注意，若题目内容是被用户可见的，那么此处填写的题解也会被用户可见。查看题解按钮会出现在前台题目详情页面中。
    </div>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'show_solution')->radioList([
        '1' => Yii::t('app', '任何时候都可以查看解题'),
        '0' => Yii::t('app', '提交程序正确后才能查看。')
    ])?>

    <?= $form->field($model, 'solution')->widget(Yii::$app->setting->get('ojEditor'))->label(false) ?>

    <div class="form-group">
    <div class="row"><div class="col-md-2 col-md-offset-5"><?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?></div></div>
    </div>

    <?php ActiveForm::end(); ?>
</div>