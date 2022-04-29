<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'News');
?>
<div class="discuss-index">

    <h3 class="lead">创建和管理新闻。</h3>

    <p>
        <?= Html::a(Yii::t('app', 'Release news'), ['create'], ['class' => 'btn btn-success btn-block']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title',
            'created_at:datetime',
            [
                'attribute' => 'status',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->status) {
                        return Yii::t('app', 'Visible');
                    } else {
                        return Yii::t('app', 'Hidden');
                    }
                },
                'format' => 'raw',
            ],
            //'updated_at',
            //'content:ntext',
            //'status',
            //'entity_id',
            //'entity',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
