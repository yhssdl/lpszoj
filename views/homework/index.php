<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Contest;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Homework');
?>
<div class="contest-index">
    <p><?= Html::a(Yii::t('app', 'Create'), ['/homework/create'], ['class' => 'btn btn-success']) ?></p>
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
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'title',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a(Html::encode($model->title), ['/homework/view', 'id' => $key]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'status',
                'value' => function ($model, $key, $index, $column) {
                    $link = Html::a(Yii::t('app', 'Register »'), ['/contest/register', 'id' => $model->id]);
                    if (!Yii::$app->user->isGuest && $model->isUserInContest()) {
                        $link = '<span class="well-done">' . Yii::t('app', 'Registration completed') . '</span>';
                    }
                    if ($model->status == Contest::STATUS_VISIBLE &&
                        !$model->isContestEnd() &&
                        $model->scenario == Contest::SCENARIO_ONLINE) {
                        $column = $model->getRunStatus(1) . ' ' . $link;
                    } else {
                        $column = $model->getRunStatus(1);
                    }
                    $userCount = $model->getContestUserCount();
                    return $column . ' ' . Html::a(' <span class="fa fa-user"></span>x'. $userCount, ['/homework/user', 'id' => $model->id]);
                },
                'format' => 'raw',
            ],
            'start_time',
            'end_time',
            [
                'attribute' => 'created_by',
                'value' => function ($model, $key, $index, $column) {
                    return $model->user->nickname;
                }
            ]
        ],
    ]); ?>
</div>
