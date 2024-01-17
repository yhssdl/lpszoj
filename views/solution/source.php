<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\models\Solution */

$this->title = $model->id;

if (!$model->canViewSource()) {
    return '暂无权限查看源码';
}
?>
<div class="solution-view">
        <div class="row">
            <div class="col-xs-6">
                <p><?= Yii::t('app', 'Submit Time') ?>：<?= $model->created_at ?></p>
            </div>
            <div class="col-xs-6">
            <p><?= Yii::t('app', 'Run ID') ?>: <?= Html::a($model->id, ['/solution/detail', 'id' => $model->id],['target'=>'_blank']) ?></p>
            </div>
        </div>
        <div><pre class="line-numbers"><code id="pre_code" class="language-cpp"><?= Html::encode($model->source) ?></code></pre></div>
</div>