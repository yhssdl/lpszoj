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
</div>
