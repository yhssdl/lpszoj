<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Contest */

$this->title = Yii::t('app', 'Create Contest');

$model->status = $model::STATUS_HIDDEN;
$model->type = $model::TYPE_RANK_GROUP;
$model->scenario = $model::SCENARIO_ONLINE;

$model->language = -1;
$model->enable_clarify = 1;
$model->show_source = 0;
$model->enable_board = 1;
$model->enable_print = 0;
$model->punish_time = 20; //罚时初始值


?>
<p class="lead">创建一个新的公共比赛或题目集</p>
<div class="contest-create animate__animated animate__fadeInUp">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
