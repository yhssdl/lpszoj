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
$scoreboardFrozenTime = Yii::$app->setting->get('scoreboardFrozenTime') / 3600;
?>
<?= Nav::widget([
    'items' => [
        [
            'label' => $this->title,
            'url' => ['group/view', 'id' => $model->id]
        ],
        [
            'label' => Yii::t('app', 'Member'),
            'url' => ['group/user', 'id' => $model->id]
        ],
    ],
    'options' => ['class' => 'nav-tabs']
]) ?>
<br>
<div class="group-view">


    <?php if ($model->hasPermission()) : ?>
        <?php Modal::begin([
            'header' => Yii::t('app', 'Invite Member'),
            'toggleButton' => [
                'label' => Yii::t('app', 'Invite Member'),
                'tag' => 'a',
                'style' => 'cursor:pointer;',
                'class' => 'btn btn-success btn-block'
            ]
        ]); ?>
        <?php $form = ActiveForm::begin(); ?>
        <p class="hint-block">1. 一个用户占据一行，每行格式为<code>username</code>。</p>
        <p class="hint-block">2. 必须是已经注册过的用户。</p>
        <?= $form->field($newGroupUser, 'username')->textarea(['rows' => 10]) ?>
        <?php if (Yii::$app->setting->get('isGroupJoin')) : ?>
            <?= $form->field($newGroupUser, 'role')->radioList(['2' => '邀请中', '4' => '普通成员'], ['value' => [4]]) ?>
        <?php else : ?>
            <?= $form->field($newGroupUser, 'role')->radioList(['2' => '邀请中'], ['value' => [2]]) ?>
        <?php endif; ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
        <?php Modal::end(); ?>
        <br>
    <?php endif; ?>
</div>
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
            'header' => Html::a(Yii::t('app', 'Role') . '<span class="glyphicon glyphicon-sort-by-alphabet"></span>', ['/group/view', 'id' => $model->id, 'sort' => 1]),
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
            'header' => Html::a(Yii::t('app', 'Solved') . '<span class="glyphicon glyphicon-sort-by-alphabet-alt"></span>', ['/group/view', 'id' => $model->id, 'sort' => 0]),
            'value' => function ($date) {
                return $date['solved'] == '' ? 0 : $date['solved'];
            },
            'format' => 'raw',
        ],
        [
            'attribute' => Yii::t('app', 'Created At'),
            'value' => function ($date) {
                return Yii::$app->formatter->asRelativeTime($date['created_at']);
            },
            'options' => ['width' => '150px']
        ],
        [
            'class' => 'yii\grid\ActionColumn', 'header' => '操作',
            'template' => '{user-update} {user-delete}',
            'buttons' => [
                'user-update' => function ($url, $date) {
                    $url = '/group/user-update?id=' . $date['id'];
                    $options = [
                        'title' => Yii::t('yii', 'Update'),
                        'aria-label' => Yii::t('yii', 'Update'),
                        'data-pjax' => '0',
                        'onclick' => 'return false',
                        'data-click' => "user-manager"
                    ];
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                },
                'user-delete' => function ($url, $date) {
                    $url = '/group/user-delete?id=' . $date['id'];
                    $options = [
                        'title' => Yii::t('yii', 'Delete'),
                        'aria-label' => Yii::t('yii', 'Delete'),
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ];
                    return Html::a('<span class="fa fa-trash"></span>', $url, $options);
                }
            ],
            'visible' => $model->hasPermission(),
            'options' => ['width' => '90px']
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