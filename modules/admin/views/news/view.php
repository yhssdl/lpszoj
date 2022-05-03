<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Discuss */

$this->title = $model->title;
?>
<div class="discuss-view">

    <p class="lead"> 预览《<?= Html::encode($model->title) ?>》发布渲染效果</p>

    <div class="btn-group btn-group-justified">
        <div class="btn-group">
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </div>
        <div class="btn-group">
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], ['class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        </div>
    </div>
    <p></p>

    <div class="list-group">
        <div class="list-group-item animate__animated animate__fadeInUp">
         <?= Yii::$app->formatter->asMarkdown($model->content) ?> </div>
    </div>
</div>