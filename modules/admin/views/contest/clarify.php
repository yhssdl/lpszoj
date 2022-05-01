<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $new_clarify app\models\Discuss */

$this->title = Html::encode($model->title);

if ($discuss != null) {
    echo $this->render('_clarify_view', [
        'clarify' => $discuss,
        'new_clarify' => $new_clarify
    ]);
    return;
}
?>
<p class="lead">比赛 <?= Html::a(Html::encode($model->title), ['view', 'id' => $model->id]) ?> 答疑。</p>
<div>

    <?= GridView::widget([
        'layout' => '{items}{pager}',
        'pager' =>[
            'firstPageLabel' => Yii::t('app', 'First'),
            'prevPageLabel' => '« ',
            'nextPageLabel' => '» ',
            'lastPageLabel' => Yii::t('app', 'Last'),
            'maxButtonCount' => 10
        ],
        'dataProvider' => $clarifies,
        'tableOptions' => ['class' => 'table table-striped table-bordered table-text-center'],
        'rowOptions' => function($model, $key, $index, $grid) {
            return ['class' => 'animate__animated animate__fadeInUp'];
        },

        'columns' => [
            [
                'attribute' => 'who',
                'label' => Yii::t('app', 'Who'),
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->user->username . ' [' . $model->user->nickname . ']', ['/user/view', 'id' => $model->user->id]);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'title',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->title, [
                        'contest/clarify',
                        'id' => $model->entity_id,
                        'cid' => $model->id
                    ]);
                },
                'format' => 'raw'
            ],
            'created_at',
            'updated_at'
        ]
    ]); ?>

</div>
