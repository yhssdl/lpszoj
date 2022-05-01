<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $rejudge app\modules\admin\models\Rejudge */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Rejudge');
?>

<p class="lead">对已有的提交进行重判</p>

<div class="contest-form animate__animated animate__fadeInUp">

    <?php $form = ActiveForm::begin(); ?>

    <div class="alert alert-info"><i class="glyphicon glyphicon-info-sign"></i> 请根据实际需要，选填以下三个输入框其中一个。</div>

    <div class="alert alert-light"><i class="glyphicon glyphicon-info-sign"></i> 重判该题号的所有提交记录。</div>
    <?= $form->field($rejudge, 'problem_id', ['template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Problem ID').'</span>{input}</div>'])->textInput() ?>


    <div class="alert alert-light"><i class="glyphicon glyphicon-info-sign"></i> 重判该场比赛的所有提交记录。</div>
    <?= $form->field($rejudge, 'contest_id', ['template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Contest ID').'</span>{input}</div>'])->textInput() ?>

    <div class="alert alert-light"><i class="glyphicon glyphicon-info-sign"></i> 重判该提交记录。</div>
    <?= $form->field($rejudge, 'run_id', ['template' => '<div class="input-group"><span class="input-group-addon">'.Yii::t('app', 'Run ID').'</span>{input}</div>'])->textInput() ?>

    <div class="alert alert-warning"><i class="glyphicon glyphicon-info-sign"></i> 重判提交将带来不可预知的后果，对于进行中或已经结束的比赛，在非必要的情况下（特指数据造水了）不应该进行重判。</div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-danger btn-block']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
