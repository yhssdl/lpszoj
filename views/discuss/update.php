<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Discuss */
/* @var $form yii\widgets\ActiveForm */
$this->title = $model->title;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Problem'), 'url' => ['/problem/index']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($model->problem->title), 'url' => ['problem/view', 'id' => $model->problem->id]];
?>

<div class="contest-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($model->entity == \app\models\Discuss::ENTITY_PROBLEM && $model->parent_id == 0): ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?php endif; ?>

    <?= $form->field($model, 'content')->widget(Yii::$app->setting->get('ojEditor'))->label(false); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
