<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->id;
?>
<div class="user-view">
    <br>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="btn-group btn-group-justified">
                <div class="btn-group">
                    <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
                </div>
                <div class="btn-group">
                    <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]) ?>
                </div>
            </div>

        </div>
    </div>
</div>