<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use app\models\GroupUser;
use app\models\User;
use app\models\Contest;

/* @var $this yii\web\View */
/* @var $model app\models\Group */
/* @var $contestDataProvider yii\data\ActiveDataProvider */
/* @var $userDataProvider yii\data\ActiveDataProvider */
/* @var $newContest app\models\Contest */
/* @var $newGroupUser app\models\GroupUser */

$this->title = $model->name;
$scoreboardFrozenTime = Yii::$app->setting->get('scoreboardFrozenTime') / 3600;
?>
<div class="group-view">
    <div class="row">
        <div class="col-md-3">
            <h1><?= Html::a(Html::encode($this->title), ['/group/view', 'id' => $model->id]) ?></h1>
            <?php if (!Yii::$app->user->isGuest && ($model->role == GroupUser::ROLE_LEADER || Yii::$app->user->identity->isAdmin())): ?>
            <?= Html::a(Yii::t('app', 'Setting'), ['/group/update', 'id' => $model->id], ['class' => 'btn btn-default btn-block']) ?>
            <?php endif; ?>
            <hr>
            <p>
                <?= Yii::$app->formatter->asMarkdown($model->description); ?>
            </p>
            <hr>
            <p><?= Yii::t('app', 'Join Policy') ?>: <?= $model->getJoinPolicy() ?></p>
            <p><?= Yii::t('app', 'Status') ?>: <?= $model->getStatus() ?></p>
        </div>
        <div class="col-md-9">
            <div>
                <h2 style="display: inline">
                    <?= Yii::t('app', 'Homework'); ?>
                </h2>
                <?php if ($model->hasPermission()): ?>
                <?php Modal::begin([
                    'header' => '<h3>' . Yii::t('app', 'Create') . '</h3>',
                    'toggleButton' => [
                        'label' => Yii::t('app', 'Create'),
                        'tag' => 'a',
                        'style' => 'cursor:pointer;'
                    ]
                ]); ?>
                    <?php $form = ActiveForm::begin(); ?>
                    <?= $form->field($newContest, 'title')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
                    <?= $form->field($newContest, 'start_time')->widget('app\widgets\laydate\LayDate', [
                        'clientOptions' => [
                            'istoday' => true,
                            'type' => 'datetime'
                        ],
                        'options' => ['autocomplete' => 'off']
                    ]) ?>
                    <?= $form->field($newContest, 'end_time')->widget('app\widgets\laydate\LayDate', [
                        'clientOptions' => [
                            'istoday' => true,
                            'type' => 'datetime'
                        ],
                        'options' => ['autocomplete' => 'off']
                    ]) ?>

                    <?= $form->field($newContest, 'lock_board_time')->widget('app\widgets\laydate\LayDate', [
                        'clientOptions' => [
                            'istoday' => true,
                            'type' => 'datetime'
                        ]
                    ])->hint("如果不需要封榜请留空，当前会在比赛结束{$scoreboardFrozenTime}小时后才会自动在前台页面解除封榜限制。
                        如需提前结束封榜也可选择清空该表单项。
                    ") ?>

                    <?= $form->field($newContest, 'type')->radioList([
                        Contest::TYPE_RANK_SINGLE => Yii::t('app', 'Single Ranked'),
                        Contest::TYPE_RANK_GROUP => Yii::t('app', 'ICPC'),
                        Contest::TYPE_HOMEWORK => Yii::t('app', 'Homework'),
                        Contest::TYPE_OI => Yii::t('app', 'OI'),
                        Contest::TYPE_IOI => Yii::t('app', 'IOI'),
                    ])->hint('不同类型的区别只在于榜单的排名方式。详见：' . Html::a('比赛类型', ['/wiki/contest'], ['target' => '_blank'])) ?>

                    <?= $form->field($newContest, 'clarification')->radioList([
                                                0 => '关闭',
                                                1 => '开启',
                                                2 => '赛后开启',
                    ])->hint('答疑界面在比赛中可以根据需要开启或关闭。') ?>


                    <?= $form->field($newContest, 'language')->radioList([
                                  -1 => 'All',
                                  0 => 'C',
                                  1 => 'C++',
                                  2 => 'Java',
                                  3 => 'Python3',
                    ])->hint('为 All 时可以使用任意的语言编程，否则在比赛中只能以指定的语言编程并提交。') ?>


                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                <?php Modal::end(); ?>
                <?php endif; ?>
            </div>
            <?= GridView::widget([
                'layout' => '{items}{pager}',
                'dataProvider' => $contestDataProvider,
                'options' => ['class' => 'table-responsive'],
                'columns' => [
                    [
                        'attribute' => 'title',
                        'value' => function ($model, $key, $index, $column) {
                            return Html::a(Html::encode($model->title), ['/contest/view', 'id' => $key]);
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
                                $column = $model->getRunStatus(true) . ' ' . $link;
                            } else {
                                $column = $model->getRunStatus(true);
                            }
                            $userCount = $model->getContestUserCount();
                            return $column . ' ' . Html::a(' <span class="glyphicon glyphicon-user"></span>x'. $userCount, ['/contest/user', 'id' => $model->id]);
                        },
                        'format' => 'raw',
                        'options' => ['width' => '220px']
                    ],
                    [
                        'attribute' => 'start_time',
                        'options' => ['width' => '150px']
                    ],
                    [
                        'attribute' => 'end_time',
                        'options' => ['width' => '150px']
                    ],
                    [
                        'value' => function ($model, $key, $index, $column) {
                            return Html::a('<span class="glyphicon glyphicon-cog"></span>', ['/homework/update', 'id' => $key],['title' => Yii::t('app', 'Setting')]);
                        },
                        'format' => 'raw',
                        'visible' => $model->hasPermission(),
                        'options' => ['width' => '50px']
                    ]
                ],
            ]); ?>

            <div>
                <h2 style="display: inline">
                    <?= Yii::t('app', 'Member'); ?>
                </h2>
                <?php if ($model->hasPermission()): ?>
                    <?php Modal::begin([
                        'header' => '<h3>' . Yii::t('app', 'Invite Member') . '</h3>',
                        'toggleButton' => [
                            'label' => Yii::t('app', 'Invite Member'),
                            'tag' => 'a',
                            'style' => 'cursor:pointer;'
                        ]
                    ]); ?>
                    <?php $form = ActiveForm::begin(); ?>
                    <p class="hint-block">1. 一个用户占据一行，每行格式为<code>username</code>。</p>
                      <p class="hint-block">2. 必须是已经注册过的用户。</p>
                      <?= $form->field($newGroupUser, 'username')->textarea(['rows' => 10]) ?>
                      <?php if (Yii::$app->setting->get('isGroupJoin')): ?>    
                        <?= $form->field($newGroupUser, 'role')->radioList(['2'=>'邀请中','4'=>'普通成员'],['value'=>[4]]) ?>
                      <?php else: ?>
                        <?= $form->field($newGroupUser, 'role')->radioList(['2'=>'邀请中'],['value'=>[2]]) ?>
                      <?php endif; ?>  
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                    <?php Modal::end(); ?>
                <?php endif; ?>
            </div>
            <?= GridView::widget([
                'layout' => '{items}{pager}',
                'dataProvider' => $userDataProvider,
                'options' => ['class' => 'table-responsive solution-index'],
                'columns' => [
                    [
                        'attribute' => Yii::t('app', 'Role'),
                        'header' => Html::a(Yii::t('app', 'Role').'<span class="glyphicon glyphicon-sort-by-alphabet"></span>', ['/group/view', 'id' => $model->id , 'sort' => 1]),
                        'value' => function ($date) {
                            
                            return GroupUser::getRoleName($date['role']);
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
                        'header' => Html::a(Yii::t('app', 'Solved').'<span class="glyphicon glyphicon-sort-by-alphabet-alt"></span>', ['/group/view', 'id' => $model->id , 'sort' => 0]),
                        'value' => function ($date) {
                            return $date['solved']==''?0:$date['solved'];
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
                        'class' => 'yii\grid\ActionColumn','header' => '操作',
                        'template' => '{user-update} {user-delete}',
                        'buttons' => [
                            'user-update' => function ($url, $date) {
                                $url = '/group/user-update?id='.$date['id'];
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
                                $url = '/group/user-delete?id='.$date['id'];
                                $options = [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'aria-label' => Yii::t('yii', 'Delete'),
                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                ];
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options);
                            }
                        ],
                        'visible' => $model->hasPermission(),
                        'options' => ['width' => '90px']
                    ]
                ],
            ]); ?>
        </div>
    </div>
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
    'options' => ['id' => 'solution-info']
]); ?>
    <div id="solution-content">
    </div>
<?php Modal::end(); ?>