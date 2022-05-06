<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\Discuss */

$this->title = Html::encode($model->title);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-view">
    <h1 class="news-title">
        <?= $this->title ?>
    </h1>
    <div class="news-meta">
        <span class="fa fa-clock-o icon-muted"></span> <?= Yii::$app->formatter->asDate($model->created_at) ?>
    </div>
    <div class="news-content">
        <?= Yii::$app->formatter->asMarkdown($model->content) ?>
    </div>
</div>
