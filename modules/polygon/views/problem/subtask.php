<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\polygon\models\Problem */

$this->title = $model->title;
$this->params['model'] = $model;

$model->setSamples();
?>
<div class="animate__animated animate__fadeInUp">
    <?php if (Yii::$app->setting->get('oiMode')) : ?>
        <div class="alert alert-light"><i class="fa fa-info-circle"></i> 如果题目需要配置子任务的，可以在下面填写子任务的配置，配置仅在主题库生效。参考：<?= Html::a('子任务配置要求', ['/wiki/oi']) ?></div>
        <?= Html::beginForm() ?>

        <div class="form-group">
            <?= Html::label(Yii::t('app', 'Subtask'), 'subtaskContent', ['class' => 'sr-only']) ?>

            <?= \app\widgets\codemirror\CodeMirror::widget(['name' => 'subtaskContent', 'value' => $subtaskContent]);  ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>
        </div>
        <?= Html::endForm(); ?>
    <?php else : ?>
        <div class="alert alert-light"><i class="fa fa-info-circle"></i> 当前 OJ 运行模式不是 OI 模式，要启用子任务编辑，需要在后台设置页面启用 OI 模式。<div>
            <?php endif; ?>
            </div>