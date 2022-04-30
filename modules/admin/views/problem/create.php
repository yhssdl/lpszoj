<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Problem */

$this->title = Yii::t('app', 'Create Problem');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Problems'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="problem-create">
    <p class="lead">创建一道题目，不借助 Polygon 系统。</p>
    <p>创建题目前，请先阅读：<?= Html::a('出题要求', ['/wiki/problem'], ['target' => '_blank']) ?></p>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
