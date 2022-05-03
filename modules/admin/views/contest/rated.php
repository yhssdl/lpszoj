<?php

use yii\helpers\Html;
use app\models\Contest;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Contest */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->title;
?>
<div class="contest-view">

    <p class="lead"><?= Html::encode($this->title) ?></p>
    <?php if ($model->getRunStatus() == Contest::STATUS_ENDED): ?>
        <div class="alert alert-light"><i class=" glyphicon glyphicon-info-sign"></i> 点击下方按钮将计算参加该场比赛的用户在该场比赛所的积分。计算出来的积分用于在排行榜排名。重复点击只会计算一次。</div>
        <?= Html::a(Yii::t('app', 'Rated'), ['rated', 'id' => $model->id, 'cal' => 1], ['class' => 'btn btn-success btn-block']) ?>
        <br>
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
            'columns' => [
                [
                    'attribute' => 'who',
                    'label' => Yii::t('app', 'Who'),
                    'value' => function ($model, $key, $index, $column) {
                        return Html::a(Html::encode($model->user->nickname), ['/user/view', 'id' => $model->user->id]);
                    },
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'rating_change',
                    'label' => Yii::t('app', 'Rating change'),
                    'format' => 'raw'
                ]
            ],
        ]); ?>
    <?php else: ?>
        <div class="alert alert-light"><i class=" glyphicon glyphicon-info-sign"></i> 比赛尚未结束，请在比赛结束后再来计算积分。</div>
    <?php endif; ?>
</div>
