<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use app\models\GroupUser;
use yii\widgets\ListView;
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
    <div class="row">

        <div class="col-md-9">

            <?php if ($contestDataProvider->count > 0) {
                echo ListView::widget([
                    'dataProvider' => $contestDataProvider,
                    'itemView' => $model->hasPermission() ? '_contest_item1' : '_contest_item',
                    'itemOptions' => ['tag' => false],
                    'layout' => '{items}<p></p>{pager}',
                    'options' => ['class' => 'list-group animate__animated animate__fadeInUp'],

                    'pager' => [
                        'firstPageLabel' => Yii::t('app', 'First'),
                        'prevPageLabel' => '« ',
                        'nextPageLabel' => '» ',
                        'lastPageLabel' => Yii::t('app', 'Last'),
                        'linkOptions' => ['class' => 'page-link'],
                        'maxButtonCount' => 10,
                    ]
                ]);
            } else {

                echo '<div class="alert alert-light"><i class=" glyphicon glyphicon-info-sign"></i> 组长还未创建题目。</div>';
            }
            ?>
        </div>
        <div class="col-md-3">
            <div>
                <?php if ($model->kanban) : ?>
                    <div class="list-group-item list-group-item-action"><?= Yii::$app->formatter->asMarkdown($model->kanban) ?></div><br>
                <?php endif; ?>
            </div>
            <div class="list-group">
                <div class="list-group-item"><?= Yii::t('app', 'Join Policy') ?><span class="float-right"> <?= $model->getJoinPolicy() ?></span></div>
                <div class="list-group-item"><?= Yii::t('app', 'Status') ?><span class="float-right"> <?= $model->getStatus() ?></span></div>
            </div>

            <?php if ($model->hasPermission()) : ?>
                <?php Modal::begin([
                    'header' => Yii::t('app', 'Create Contest'),
                    'toggleButton' => [
                        'label' => Yii::t('app', 'Create Contest'),
                        'tag' => 'a',
                        'style' => 'cursor:pointer;',
                        'class' => 'btn btn-success btn-block'
                    ]
                ]); ?>
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($newContest, 'title', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Title') . '</span>{input}</div>'])->textInput()->label(false) ?>


                <?= $form->field($newContest, 'start_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Start Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
                    'clientOptions' => [
                        'istoday' => true,
                        'type' => 'datetime'
                    ],
                    'options' => ['autocomplete' => 'off']
                ]) ?>
                <?= $form->field($newContest, 'end_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'End Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
                    'clientOptions' => [
                        'istoday' => true,
                        'type' => 'datetime'
                    ],
                    'options' => ['autocomplete' => 'off']
                ]) ?>

                <?= $form->field($newContest, 'lock_board_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Lock Board Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
                    'clientOptions' => [
                        'istoday' => true,
                        'type' => 'datetime'
                    ]
                ])->hint("如果不需要封榜请留空，当前会在比赛结束{$scoreboardFrozenTime}小时后才会自动在前台页面解除封榜限制。
                        如需提前结束封榜也可选择清空该表单项。
                    ") ?>

                <?= $form->field($newContest, 'type')->radioList([
                    Contest::TYPE_RANK_SINGLE => Yii::t('app', 'Single Ranked'),
                    Contest::TYPE_RANK_GROUP => Yii::t('app', 'ACM/ICPC'),
                    Contest::TYPE_HOMEWORK => Yii::t('app', 'Homework'),
                    Contest::TYPE_OI => Yii::t('app', 'OI'),
                    Contest::TYPE_IOI => Yii::t('app', 'IOI'),
                ])->hint('不同类型的区别只在于榜单的排名方式。详见：') ?>

                <?= $form->field($newContest, 'enable_clarify')->radioList([
                    0 => '关闭',
                    1 => '开启',
                ])->hint('答疑界面在作业中可以根据需要开启或关闭。') ?>


                <?= $form->field($newContest, 'language')->radioList([
                    -1 => 'All',
                    0 => 'C',
                    1 => 'C++',
                    2 => 'Java',
                    3 => 'Python3',
                ])->hint('为 All 时可以使用任意的语言编程，否则在比赛中只能以指定的语言编程并提交。') ?>


                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>
                </div>
                <?php ActiveForm::end(); ?>
                <?php Modal::end(); ?>
            <?php endif; ?>
            <br>
            <?php if (!Yii::$app->user->isGuest && ($model->role == GroupUser::ROLE_LEADER || Yii::$app->user->identity->isAdmin())) : ?>
                <?= Html::a(Yii::t('app', 'Setting'), ['/group/update', 'id' => $model->id], ['class' => 'btn btn-success btn-block']) ?>
            <?php endif; ?>
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