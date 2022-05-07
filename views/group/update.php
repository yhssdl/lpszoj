<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Group */

$this->title = $model->name;
?>
<div class="group-update">

    <p class="lead"><?= Html::a(Html::encode($this->title), ['/group/view', 'id' => $model->id]) ?></p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <hr>
    <div class="row"><div class="col-md-2 col-md-offset-5">
    <?= Html::a('删除该小组', ['/group/delete', 'id' => $model->id], [
        'class' => 'btn btn-danger btn-block',
        'data-confirm' => '此操作会把该小组的比赛信息及提交记录全部删除，且不可恢复，你确定要删除吗？',
        'data-method' => 'post',
    ]) ?></div></div>

</div>
