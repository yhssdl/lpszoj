<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'News');
?>
<div class="discuss-index">

    <p class="lead">创建和管理新闻</p>

  <p>
    <div class="row"><div class="col-md-2"><?= Html::a(Yii::t('app', 'Release news'), ['create'], ['class' => 'btn btn-success btn-block']) ?></div></div>
  </p>

    <?= GridView::widget([
        'layout' => '{items}{pager}',
        'pager' =>[
            'firstPageLabel' => Yii::t('app', 'First'),
            'prevPageLabel' => '« ',
            'nextPageLabel' => '» ',
            'lastPageLabel' => Yii::t('app', 'Last'),
            'maxButtonCount' => 10
        ],
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered table-text-center'],
        'rowOptions' => function($model, $key, $index, $grid) {
            return ['class' => 'animate__animated animate__fadeInUp'];
        },
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
