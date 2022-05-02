<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\polygon\models\Problem */

$this->title = Html::encode($model->title);
$this->params['model'] = $model;
?>
<div class="problem-update">

    <div class="alert alert-info"><i class="glyphicon glyphicon-info-sign"></i>   如果你对题面进行了修改并希望管理员同步到主题库，请在题目标题一栏标明以方便管理员及时处理</div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
