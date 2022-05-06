<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Contest */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Contests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
?>

<p class="lead">比赛 <?= Html::a(Html::encode($model->title), ['view', 'id' => $model->id]) ?> 题解编辑。</p>

<div class="contest-view animate__animated animate__fadeInUp">
    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 题解内容在比赛结束后，才会出现在前台的比赛页面中供用户查看。</div>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'editorial')->widget(Yii::$app->setting->get('ojEditor'))->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>