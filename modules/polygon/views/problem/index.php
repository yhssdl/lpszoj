<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\polygon\models\ProblemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Problems');


?>
<p class="lead">欢迎访问 Polygon 出题系统</p>
<div class="row"><div class="col-md-2"><?= Html::a(Yii::t('app', 'Create Problem'), ['/polygon/problem/create'], ['class' => 'btn btn-success btn-block']) ?></div></div>
<br>
<div class="problem-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?><br>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items}{pager}',
        'options' => ['class' => 'table-responsive'],
        'rowOptions' => function($model, $key, $index, $grid) {
            return ['class' => 'animate__animated animate__fadeInUp'];
        },
        'tableOptions' => ['class' => 'table table-striped table-bordered table-text-center'],
        'columns' => [
            [
                'attribute' => 'id',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->id, ['/polygon/problem/view', 'id' => $key]);
                },
                'format' => 'raw',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'title',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a(Html::encode($model->title), ['/polygon/problem/view', 'id' => $key]);
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'text-align:left;'],
                'headerOptions' => ['style' => 'text-align:left;'],
                'enableSorting' => false,
            ],
            [
                'attribute' => 'created_by',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->user) {
                        return Html::a(Html::encode($model->user->nickname), ['/user/view', 'id' => $model->user->id]);
                    }
                    return '';
                },
                'format' => 'raw',
                'enableSorting' => false,
            ],
            
            ['class' => 'yii\grid\ActionColumn']
                
            
        ],
    ]); ?>
</div>