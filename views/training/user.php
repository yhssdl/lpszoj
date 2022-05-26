<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use app\models\GroupUser;
use app\models\User;
use app\models\Contest;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $model app\models\Group */
/* @var $contestDataProvider yii\data\ActiveDataProvider */
/* @var $userDataProvider yii\data\ActiveDataProvider */
/* @var $newContest app\models\Contest */
/* @var $newGroupUser app\models\GroupUser */

$this->title = $model->name;
?>
<?= Nav::widget([
    'items' => [
        [
            'label' => $this->title,
            'url' => ['training/view', 'id' => $model->id]
        ],
        [
            'label' => Yii::t('app', 'Member'),
            'url' => ['training/user', 'id' => $model->id]
        ],
    ],
    'options' => ['class' => 'nav-tabs']
]) ?>
<br>
<div class="group-view">
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
    'options' => ['class' => 'table-responsive solution-index'],
    'columns' => [
        [
            'attribute' => Yii::t('app', 'Role'),
            'header' => Html::a(Yii::t('app', 'Role') . '<span class="fa fa-sort-alpha-asc"></span>', ['user', 'id' => $model->id, 'sort' => 1]),
            'value' => function ($date) {
                $url = '/group/user-update?id=' . $date['id'];
                $options = [
                    'title' => Yii::t('yii', 'Update'),
                    'aria-label' => Yii::t('yii', 'Update'),
                    'data-pjax' => '0',
                    'onclick' => 'return false',
                    'data-click' => "user-manager"
                ];
                return Html::a(GroupUser::getRoleName($date['role']), $url, $options);
            },
            'format' => 'raw',
            'options' => ['width' => '150px']
        ],
        [
            'attribute' => Yii::t('app', 'Username'),
            'value' => function ($date) {
                $user = User::findOne($date['user_id'])->toArray();
                return Html::a(Html::encode($user['username']), ['/user/view', 'id' => $date['user_id']]);
            },
            'format' => 'raw',
            'visible' => $model->hasPermission(),
        ],
        [
            'attribute' => Yii::t('app', 'Nickname'),
            'value' => function ($date) {
                $user = User::findOne($date['user_id'])->toArray();
                return Html::a(Html::encode($user['nickname']), ['/user/view', 'id' => $date['user_id']], ['title' => $user['username']]);
            },
            'format' => 'raw',
        ],
        [
            'attribute' => Yii::t('app', 'Solved'),
            'header' => Html::a(Yii::t('app', 'Solved') . '<span class="fa fa-sort-alpha-asc-alt"></span>', ['user', 'id' => $model->id, 'sort' => 0]),
            'value' => function ($date) {
                return $date['solved'] == '' ? 0 : $date['solved'];
            },
            'format' => 'raw',
        ],
        [
            'attribute' => Yii::t('app', 'Join time'),
            'value' => function ($date) {
                return Yii::$app->formatter->asRelativeTime($date['created_at']);
            },
            'options' => ['width' => '150px']
        ]
    ],
]); ?>

</div>
<?php
$js = <<<EOF
$('[data-click=user-manager]').click(function() {
    $.ajax({
        url: $(this).attr('href'),
        type:'post',
        error: function(){alert('error');},
        success:function(html){
        $('#solution-content').html(html);
        $('#solution-info').modal('show');
    }
    });
});
EOF;
$this->registerJs($js);
?>
<?php Modal::begin([
    'header' => '管理用户',
    'options' => ['id' => 'solution-info']
]); ?>
<div id="solution-content">
</div>
<?php Modal::end(); ?>