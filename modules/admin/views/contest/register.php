<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use app\models\Contest;

/* @var $this yii\web\View */
/* @var $model app\models\Contest */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $generatorForm app\modules\admin\models\GenerateUserForm */

$this->title = $model->title;
$contest_id = $model->id;
?>
<p class="lead">管理比赛 <?= Html::a(Html::encode($model->title), ['view', 'id' => $model->id]) ?> 参赛用户。</p>

<div class="btn-group btn-group-justified">
    <div class="btn-group">
        <?= Html::a('打星', ['contest/star', 'id' => $model->id], ['class' => 'btn btn-default', 'target' => '_blank']) ?>
    </div>
    <div class="btn-group">
        <?php Modal::begin([
            'header' => Yii::t('app', 'Add participating user'),
            'toggleButton' => ['label' => Yii::t('app', 'Add participating user'), 'class' => 'btn btn-success'],
        ]); ?>

        <?= Html::beginForm(['contest/register', 'id' => $model->id]) ?>
        <div class="alert alert-light"><i class="fa fa-info-circle"></i> 请把要参赛用户的用户名复制到此处，一个名字占据一行，请自行删除多余的空行。</div>
        <div class="form-group">
            <?= Html::textarea('user', '', ['class' => 'form-control', 'rows' => 10]) ?>
        </div>

        <div class="form-group">
        <div class="row"><div class="col-md-4 col-md-offset-4"><?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?></div></div>
        </div>
        <?= Html::endForm(); ?>
        <?php Modal::end(); ?>
    </div>


    <div class="btn-group">
        <?php Modal::begin([
            'header' => Yii::t('app', 'Generate user for the contest'),
            'toggleButton' => ['label' => Yii::t('app', 'Generate user for the contest'), 'class' => 'btn btn-success'],
        ]); ?>
        <div class="alert alert-danger"><i class="fa fa-info-circle"></i> 重复使用此功能会删除已生成的帐号，请勿在分发账号后进行此操作。</div>
        <div class="alert alert-light"><i class="fa fa-info-circle"></i> 前缀不应更改，不同比赛的前缀都不一样，是为了可以一直保留比赛榜单。</div>
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($generatorForm, 'prefix', [
            'template' => "<div class=\"input-group\"><span class=\"input-group-addon\">" . Yii::t('app', 'Prefix') . "</span>{input}</div>",
            'options' => ['class' => '']
        ])->textInput([
            'maxlength' => true, 'value' => 'c' . $model->id . 'user', 'disabled' => true
        ])->label(false) ?>
        <br>
        <?= $form->field($generatorForm, 'team_number', [
            'template' => "<div class=\"input-group\"><span class=\"input-group-addon\">数量</span>{input}</div>",
            'options' => ['class' => '']
        ])->textInput(['maxlength' => true, 'value' => '50'])->label(false) ?>

        <br>
        <div class="alert alert-light"><i class="fa fa-info-circle"></i> 请把所有的用户昵称复制到此处，一个昵称占一行，请自行删除多余的空行。</div>

        <?= $form->field($generatorForm, 'names')->textarea(['rows' => 10])->label(false) ?>

        <div class="form-group">
        <div class="row"><div class="col-md-4 col-md-offset-4"><?= Html::submitButton(Yii::t('app', 'Generate'), ['class' => 'btn btn-success btn-block']) ?></div></div>
        </div>

        <?php ActiveForm::end(); ?>
        <?php Modal::end(); ?>
    </div>
    <div class="btn-group">
        <?= Html::a(Yii::t('app', 'Copy these accounts to distribute'), ['contest/printuser', 'id' => $model->id], ['class' => 'btn btn-default', 'target' => '_blank']) ?>
    </div>



</div>
<br>

<?= GridView::widget([
    'layout' => '{items}{pager}',
    'pager' => [
        'firstPageLabel' => Yii::t('app', 'First'),
        'prevPageLabel' => '« ',
        'nextPageLabel' => '» ',
        'lastPageLabel' => Yii::t('app', 'Last'),
        'maxButtonCount' => 10
    ],
    'dataProvider' => $dataProvider,
    'tableOptions' => ['class' => 'table table-striped table-bordered table-text-center'],
    'rowOptions' => function ($model, $key, $index, $grid) {
        return ['class' => 'animate__animated animate__fadeInUp'];
    },
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
            'attribute' => 'user_password',
            'value' => function ($contestUser, $key, $index, $column) use ($model) {
                if ($model->scenario == Contest::SCENARIO_OFFLINE) {
                    return $contestUser->user_password;
                } else {
                    return '线上赛无法提供密码';
                }
            },
            'format' => 'raw',
            'visible' => $model->scenario == Contest::SCENARIO_OFFLINE
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $model, $key) use ($contest_id) {
                    $options = [
                        'title' => Yii::t('yii', 'Delete'),
                        'aria-label' => Yii::t('yii', 'Delete'),
                        'data-confirm' => '删除该项，也会删除该用户在此比赛中的提交记录，确定删除？',
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ];
                    return Html::a('<span class="fa fa-trash"></span>', Url::toRoute(['contest/register', 'id' => $contest_id, 'uid' => $model->user->id]), $options);
                },
            ]
        ],
    ],
]); ?>