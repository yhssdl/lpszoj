<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ContestPrint */
/* @var $contest app\models\Contest */

$this->title = '创建打印源代码';
?>
<div class="print-source-create">

    <p class="lead"><?= Html::encode($this->title) ?></p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
