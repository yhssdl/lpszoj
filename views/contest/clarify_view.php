<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $clarify app\models\Discuss */
/* @var $newClarify app\models\Discuss */

$this->params['model'] = $model;
?>
<div style="padding-top: 20px">
    <div class="alert alert-light">
        <h3><?= Html::encode($clarify->title) ?></h3>
        <hr>
        <?= Yii::$app->formatter->asMarkdown($clarify->content) ?>
        <hr>
        <span class="fa fa-user"></span> <?= $clarify->user->nickname ?>
        &nbsp;•&nbsp;
        <span class="fa fa-clock-o"></span> <?= Yii::$app->formatter->asRelativeTime($clarify->created_at) ?>
    </div>
    <?php foreach ($clarify->reply as $reply): ?>
        <div class="alert alert-light">
            <?= Yii::$app->formatter->asMarkdown($reply->content) ?>
            <hr>
            <span class="fa fa-user"></span> <?= Html::encode($reply->user->nickname) ?>
            &nbsp;•&nbsp;
            <span class="fa fa-clock-o"></span> <?= Yii::$app->formatter->asRelativeTime($reply->created_at) ?>
        </div>
    <?php endforeach; ?>

        <?php if ($model->getRunStatus() == \app\models\Contest::STATUS_RUNNING): ?>
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($newClarify, 'content')->widget(Yii::$app->setting->get('ojEditor')); ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Reply'), ['class' => 'btn btn-success btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
        <?php else: ?>
            <div class="alert alert-light"><i class=" fa fa-info-circle"></i> <?= Yii::t('app', 'The contest has ended.') ?></div>
        <?php endif; ?>

</div>
