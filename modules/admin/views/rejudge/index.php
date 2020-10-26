<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $rejudge app\modules\admin\models\Rejudge */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('app', 'Rejudge');
?>

<div class="contest-form">

    <?php $form = ActiveForm::begin(); ?>

    <h3>对提交记录进行重判，以下三个输入框，选填其中一个</h3>

    <?= $form->field($rejudge, 'problem_id')->label(Yii::t('app', 'Problem ID'))->hint('重判该题号的所有提交记录') ?>

    <?= $form->field($rejudge, 'contest_id')->label(Yii::t('app', 'Contest ID'))->dropDownList($rejudge->getContestIdList())->hint('重判该场比赛的所有提交记录') ?>

    <?= $form->field($rejudge, 'run_id')->label(Yii::t('app', 'Run ID'))->hint('重判该提交记录') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
