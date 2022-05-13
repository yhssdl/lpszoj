<?php

use app\models\User;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Group */
$this->title = Yii::t('app', 'Create Training');
?>
<div class="group-create">
    <p class="lead"><?= Html::encode($this->title) ?></p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
