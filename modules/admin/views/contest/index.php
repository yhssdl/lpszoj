<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Contests');
?>
<p class="lead">创建和管理公共比赛与题目集</p>
<div class="contest-index">


    <?php Pjax::begin(); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Contest'), ['create'], ['class' => 'btn btn-success btn-block']) ?>
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
        'rowOptions' => function($model, $key, $index, $grid) {
            return ['class' => 'animate__animated animate__fadeInUp'];
        },
        'tableOptions' => ['class' => 'table table-striped table-bordered table-text-center'],
        'columns' => [
            [
                'attribute' => 'id',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->id, ['contest/view', 'id' => $key]);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'title',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a(Html::encode($model->title), ['contest/view', 'id' => $key]);
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            [
                'attribute' => 'start_time',
                'format' => 'raw',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'end_time',
                'format' => 'raw',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'status',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->status == $model::STATUS_VISIBLE) {
                        return Yii::t('app', 'Public');
                    } else if ($model->status == $model::STATUS_PRIVATE) {
                        return Yii::t('app', 'Private');
                    } else {
                        return Yii::t('app', 'Hidden');
                    }
                },
                'enableSorting' => false,
                'format' => 'raw',
            ],
            [
                'attribute' => 'scenario',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->scenario == $model::SCENARIO_ONLINE) {
                        return Yii::t('app', 'Online');
                    } else {
                        return Yii::t('app', 'Offline');
                    }
                },
                'enableSorting' => false,
                'format' => 'raw',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
