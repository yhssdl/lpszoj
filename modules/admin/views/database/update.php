<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Discuss */

$this->title = Yii::t('app', 'Create SQL');
?>
<div class="discuss-update">

    <p class="lead">编辑 SQL 命令</p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
