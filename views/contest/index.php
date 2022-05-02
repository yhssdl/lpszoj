<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Contest;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Contests');
?>
<div class="contest-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

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
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            [
                'attribute' => 'title',
                'value' => function ($model, $key, $index, $column) {

                    $base_title = Html::a(Html::encode($model->title), ['/contest/view', 'id' => $key], ['class' => 'text-dark']);

                    if ($model->ext_link) {
                        if ($model->invite_code) {
                            return $base_title . '<span class="problem-list-tags"><span class="label label-info">' . $model->invite_code . '<i class="glyphicon glyphicon-lock" style="margin-left:4px"></i></span> <span class="label label-warning"> 重定向 <i class="glyphicon glyphicon-share-alt"></i>' . '</span></span>';
                        }
                        return $base_title . '<span class="problem-list-tags label label-info"> 重定向 <i class="glyphicon glyphicon-share-alt"></i>' . '</span>';
                    }

                    $stat = "";

                    if (!Yii::$app->user->isGuest && $model->isUserInContest()) {
                        $stat = '<span class="label label-success">参赛 <i class="glyphicon glyphicon-check"></i></span> ';
                    }

                    $people_cnt = Html::a($model->getContestUserCount() . ' <i class="glyphicon glyphicon-user"></i>', ['/contest/user', 'id' => $model->id], ['class' => 'label label-primary']);

                    return $base_title . '<span class="problem-list-tags">' . $stat . $people_cnt . '</span>';
                },
                'format' => 'raw',
                'enableSorting' => false,
                'headerOptions' => ['style' => 'min-width:400px;']
            ],
            [
                'attribute' => 'status',
                'value' => function ($model, $key, $index, $column) {
                    return $model->getRunStatus(true);
                },
                'format' => 'raw',
                'enableSorting' => false,
                'headerOptions' => ['style' => 'width:120px;min-width:100px;']
            ],
            [
                'attribute' => 'start_time',
                'enableSorting' => false,
                'headerOptions' => ['style' => 'width:180px;min-width:180px;']
            ],
            [
                'attribute' => 'end_time',
                'value' => function ($model, $key, $index, $column) {
                    if (strtotime($model->end_time) >= Contest::TIME_INFINIFY) {
                        $column = "一直开放";
                    } else {
                        $column = $model->end_time;
                    }
                    return $column;
                },
                'enableSorting' => false,
                'headerOptions' => ['style' => 'width:180px;min-width:180px;']
            ]
        ],
        'pager' => [
            'linkOptions' => ['class' => 'page-link'],
            'maxButtonCount' => 5,
        ]
    ]); ?>
</div>