<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Discuss */

$this->title = Yii::t('app', $model->title, [
    'nameAttribute' => '' . $model->title,
]);
?>
<div class="discuss-update">

    <p class="lead"><?= Html::encode($this->title) ?></p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
