<?php

use yii\grid\GridView;
use yii\helpers\Html;
use app\models\Group;
use app\models\GroupUser;

/* @var $this yii\web\View */
/* @var $model app\models\Group */
/* @var $userDataProvider yii\data\ActiveDataProvider */

$this->title = Html::encode($model->name);



?>
<p class="lead"><?= Html::a(Html::encode($model->name), ['/group/view', 'id' => $model->id]) ?></p>
<?php if (Yii::$app->user->isGuest) : ?>
    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 请先登录后再尝试加入小组</div>
<?php else : ?>

    <div class="row">
    <?php if ($model->getRole() == GroupUser::ROLE_INVITING) : ?>
        <p class="lead">邀请你加入小组：</p>
        
        <div class="col-md-2"><?= Html::a('同意加入', ['/group/accept', 'id' => $model->id, 'accept' => 1], ['class' => 'btn btn-success btn-block']) ?></div>
        <div class="col-md-2"><?= Html::a('残忍拒绝', ['/group/accept', 'id' => $model->id, 'accept' => 0], ['class' => 'btn btn-danger btn-block']) ?></div>
    <?php elseif ($model->join_policy == Group::JOIN_POLICY_APPLICATION) : ?>
        <div class="col-md-2"><?= Html::a('申请加入', ['/group/accept', 'id' => $model->id, 'accept' => 3], ['class' => 'btn btn-success btn-block']) ?></div>
    <?php elseif ($model->join_policy == Group::JOIN_POLICY_FREE) : ?>
        <div class="col-md-2"><?= Html::a('加入小组', ['/group/accept', 'id' => $model->id, 'accept' => 2], ['class' => 'btn btn-success btn-block']) ?></div>
    <?php endif; ?>
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
        'rowOptions' => function ($model, $key, $index, $grid) {
            return ['class' => 'animate__animated animate__fadeInUp'];
        },
        'dataProvider' => $userDataProvider,
        'options' => ['class' => 'table-responsive'],
        'columns' => [
            [
                'attribute' => 'role',
                'value' => function ($model, $key, $index, $column) {
                    return $model->getRole(true);
                },
                'format' => 'raw',
                'options' => ['width' => '150px']
            ],
            [
                'attribute' => Yii::t('app', 'Nickname'),
                'value' => function ($model, $key, $index, $column) {
                    return Html::a(Html::encode($model->user->nickname), ['/user/view', 'id' => $model->user->id]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model, $key, $index, $column) {
                    return Yii::$app->formatter->asRelativeTime($model->created_at);
                },
                'options' => ['width' => '150px']
            ]
        ],
    ]); ?>

<?php endif; ?>