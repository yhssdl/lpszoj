<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Discuss */

$this->title = Yii::t('app', 'Create SQL');
?>
<div class="discuss-create">
    <p class="lead">创建一条 SQL 命令</p>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
