<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = Yii::t('app', 'Create User');
?>
<div class="user-create">

    <p class="lead"><?= Html::encode($this->title) ?></p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
