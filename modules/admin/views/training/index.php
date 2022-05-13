<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Training');
?>
<p class="lead">创建和管理训练项目</p>
<div class="contest-index">

    <p>
        <div class="row"><div class="col-md-2"> <?= Html::a(Yii::t('app', 'Create Training'), ['create'], ['class' => 'btn btn-success btn-block']) ?></div></div>
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
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'label' => '训练名称',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a(Html::encode($model->name), ['training/view', 'id' => $key]);
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            [
                'attribute' => 'description',
                'format' => 'raw',
                'enableSorting' => false,
            ], 
            [
                'attribute' => 'status',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->status == \app\models\Group::STATUS_VISIBLE) {
                        return Yii::t('app', 'Visible');
                    } else if ($model->status == \app\models\Group::STATUS_HIDDEN) {
                        return Yii::t('app', 'Hidden');
                    } else {
                        return Yii::t('app', 'Private');
                    }
                },
                'enableSorting' => false,
                'format' => 'raw',
            ],
            [
                'attribute' => 'created_by',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->user) {
                        return Html::a(Html::encode($model->user->nickname), ['/user/view', 'id' => $model->user->id]);
                    }
                    return '';
                },
                'enableSorting' => false,
                'format' => 'raw',
            ],
            ['class' => 'yii\grid\ActionColumn',
            'contentOptions' => ['class'=>'a_just']
            ],
        ],
    ]); ?>
</div>
