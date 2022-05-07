<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Contest */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $generatorForm app\modules\admin\models\GenerateUserForm */

$this->title = $model->title;
$contest_id = $model->id;
?>
<p class="lead">管理比赛 <?= Html::a(Html::encode($model->title), ['view', 'id' => $model->id]) ?> 打星用户。</p>

<div class="row"><div class="col-md-2">
<?php Modal::begin([
    'header' => '添加打星用户',
    'toggleButton' => ['label' => Yii::t('app', '添加打星用户'), 'class' => 'btn btn-success btn-block']
]); ?>
<?= Html::beginForm(['contest/star', 'id' => $model->id]) ?>
<div class="alert alert-light"><i class="fa fa-info-circle"></i> 在这里填写用户名，必须是已经注册本比赛的用户。一个名字占据一行，请自行删除多余的空行。</div>
<div class="form-group">
    <?= Html::textarea('user', '', ['class' => 'form-control', 'rows' => 10]) ?>
</div>

<div class="form-group">
<div class="row"><div class="col-md-4 col-md-offset-4"><?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?></div></div>
</div>
<?= Html::endForm(); ?>
<?php Modal::end(); ?>
</div></div>

<p></p>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '{items}{pager}',
    'pager' => [
        'firstPageLabel' => Yii::t('app', 'First'),
        'prevPageLabel' => '« ',
        'nextPageLabel' => '» ',
        'lastPageLabel' => Yii::t('app', 'Last'),
        'maxButtonCount' => 10
    ],
    'options' => ['class' => 'table-responsive'],
    'rowOptions' => function ($model, $key, $index, $grid) {
        return ['class' => 'animate__animated animate__fadeInUp'];
    },
    'tableOptions' => ['class' => 'table table-striped table-bordered table-text-center'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => Yii::t('app', 'Username'),
            'value' => function ($model, $key, $index, $column) {
                return Html::a($model->user->username, ['/user/view', 'id' => $model->user->id]);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => Yii::t('app', 'Nickname'),
            'value' => function ($model, $key, $index, $column) {
                return Html::a($model->user->nickname, ['/user/view', 'id' => $model->user->id]);
            },
            'format' => 'raw'
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $model, $key) use ($contest_id) {
                    $options = [
                        'title' => Yii::t('yii', 'Delete'),
                        'aria-label' => Yii::t('yii', 'Delete'),
                        'data-confirm' => '取消打星？',
                        'data-method' => 'post',
                    ];
                    return Html::a('<span class="fa fa-trash"></span>', Url::toRoute(['contest/star', 'id' => $contest_id, 'uid' => $model->user->id]), $options);
                },
            ]
        ],
    ],
]); ?>