<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use app\models\Contest;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\Homework */

$this->title = Html::encode($model->title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group'), 'url' => ['/group/index']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($model->group->name), 'url' => ['/group/view', 'id' => $model->group->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($model->title), 'url' => ['/contest/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Setting');
$this->params['model'] = $model;
$problems = $model->problems;
$scoreboardFrozenTime = Yii::$app->setting->get('scoreboardFrozenTime') / 3600;
$contest_id = $model->id;
?>
<div class="homework-update">
    <p class="lead"><?= Yii::t('app', 'Problems') ?></p>
        <div class="table-responsive table-problem-list1">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="70px">#</th>
                        <th width="120px"><?= Yii::t('app', 'Problem ID') ?></th>
                        <th><?= Yii::t('app', 'Problem Name') ?></th>
                        <th width="200px"><?= Yii::t('app', 'Operation') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($problems as $key => $p) : ?>
                        <tr>
                            <th><?= Html::a('P' . ($key + 1), ['view', 'id' => $model->id, 'action' => 'problem', 'problem_id' => $key]) ?></th>
                            <th><?= Html::a($p['problem_id'], '') ?></th>
                            <td><?= Html::a(Html::encode($p['title']), ['view', 'id' => $model->id, 'action' => 'problem', 'problem_id' => $key]) ?></td>
                            <th>
                                <div class="btn-group">
                                    <?php Modal::begin([
                                        'header' => Yii::t('app', 'Modify') . ' : P' . (1 + $key),
                                        'toggleButton' => ['label' => Yii::t('app', 'Modify'), 'class' => 'btn btn-success'],
                                    ]); ?>

                                    <?= Html::beginForm(['/homework/updateproblem', 'id' => $model->id]) ?>

                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><?= Html::label(Yii::t('app', 'Current Problem ID'), 'problem_id') ?></span>
                                            <?= Html::textInput('problem_id', $p['problem_id'], ['class' => 'form-control', 'readonly' => 1]) ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><?= Html::label(Yii::t('app', 'New Problem ID'), 'new_problem_id') ?></span>
                                            <?= Html::textInput('new_problem_id', $p['problem_id'], ['class' => 'form-control']) ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>
                                    </div>
                                    <?= Html::endForm(); ?>

                                    <?php Modal::end(); ?>

                                    <?= Html::a(Yii::t('app', 'Delete'), [
                                        'deleteproblem',
                                        'id' => $model->id,
                                        'pid' => $p['problem_id']
                                    ], [
                                        'class' => 'btn btn-danger',
                                        'data' => [
                                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                            'method' => 'post',
                                        ],
                                    ]) ?>
                                </div>
                            </th>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>
                            <?php Modal::begin([
                                'header' => Yii::t('app', 'Add a problem'),
                                'toggleButton' => ['label' => Yii::t('app', 'Add a problem'), 'class' => 'btn btn-success'],
                            ]); ?>

                            <?= Html::beginForm(['/homework/addproblem', 'id' => $model->id]) ?>
                            <div class="alert alert-light"><i class="fa fa-info-circle"></i> 多个题目可以用空格或逗号键分开；连续题目，可以用 1001-1005 这样的格式。</div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><?= Html::label(Yii::t('app', 'Problem ID'), 'problem_id') ?></span>
                                    <?= Html::textInput('problem_id', '', ['class' => 'form-control']) ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>
                            </div>
                            <?= Html::endForm(); ?>

                            <?php Modal::end(); ?>
                        </th>
                        <th></th>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="homework-form">

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'title', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Title') . '</span>{input}</div>'])->textInput() ?>


            <?= $form->field($model, 'start_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Start Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
                'clientOptions' => [
                    'istoday' => true,
                    'type' => 'datetime'
                ],
                'options' => ['autocomplete' => 'off']
            ]) ?>

            <?= $form->field($model, 'end_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'End Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
                'clientOptions' => [
                    'istoday' => true,
                    'type' => 'datetime'
                ],
                'options' => ['autocomplete' => 'off']
            ]) ?>

            <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 封榜仅对 ACM/ICPC 或作业有效，请不要在其它赛制启用，否则可能出现未知行为。如果不需要封榜请留空，当前会在比赛结束 <?= $scoreboardFrozenTime ?> 小时后才会自动在前台页面解除封榜限制。如需提前结束封榜也可选择清空该表单项。使用封榜功能，后台管理界面的比赛榜单仍然处于实时榜单。</div>

            <?= $form->field($model, 'lock_board_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Lock Board Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
                'clientOptions' => [
                    'istoday' => true,
                    'type' => 'datetime'
                ]
            ]) ?>

            <?= $form->field($model, 'description')->widget(Yii::$app->setting->get('ojEditor')); ?>

            <?= $form->field($model, 'editorial')->widget(Yii::$app->setting->get('ojEditor')); ?>

            <?= $form->field($model, 'type')->radioList([
                Contest::TYPE_RANK_SINGLE => Yii::t('app', 'Single Ranked'),
                Contest::TYPE_RANK_GROUP => Yii::t('app', 'ACM/ICPC'),
                Contest::TYPE_HOMEWORK => Yii::t('app', 'Homework'),
                Contest::TYPE_OI => Yii::t('app', 'OI'),
                Contest::TYPE_IOI => Yii::t('app', 'IOI'),
            ])->hint('不同类型的区别只在于榜单的排名方式。如需使用OI比赛，请在后台设置页面启用OI模式。') ?>

            <?= $form->field($model, 'enable_clarify')->radioList([
                0 => '关闭',
                1 => '开启',
            ])->hint('答疑界面在作业中可以根据需要开启或关闭。') ?>

            <?= $form->field($model, 'language')->radioList([
                -1 => 'All',
                0 => 'C',
                1 => 'C++',
                2 => 'Java',
                3 => 'Python3',
            ])->hint('为 All 时可以使用任意的语言编程，否则在比赛中只能以指定的语言编程并提交。') ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <hr>
        <p class="lead">
            <?= Yii::t('app', 'Announcements') ?>
            <?php Modal::begin([
                'header' => Yii::t('app', 'Make an announcement'),
                'toggleButton' => ['label' => Yii::t('app', 'Create'), 'class' => 'btn btn-xs btn-success'],
            ]); ?>

            <?php $form = ActiveForm::begin(); ?>
        <div class="alert alert-light"><i class="fa fa-info-circle"></i> 公告发布后将显示在比赛界面中。也可以使用 <?= Html::a('全局公告', ['/admin/setting']) ?>。</div>
        <?= $form->field($newAnnouncement, 'content')->textarea(['rows' => 6]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>

        <?php Modal::end(); ?>
        </p>

        <?= \yii\grid\GridView::widget([
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
            'dataProvider' => $announcements,
            'columns' => [
                'content:ntext',
                'created_at:datetime',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete}',
                    'buttons' => [
                        'delete' => function ($url, $model, $key) use ($contest_id) {
                            $options = [
                                'title' => Yii::t('yii', 'Delete'),
                                'aria-label' => Yii::t('yii', 'Delete'),
                                'data-confirm' => '删除该项公告，确定删除？',
                                'data-method' => 'post',
                                'data-pjax' => '0',
                            ];
                            return Html::a('<span class="fa fa-trash"></span>', Url::toRoute(['homework/delete_announcement', 'contest_id' => $contest_id, 'id' => $model->id]), $options);
                        },
                    ]
                ],
            ],
  
        ]) ?>

        <hr>

        <?= Html::a('删除该比赛', ['/homework/delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-block',
            'data-confirm' => '此操作不可恢复，你确定要删除吗？',
            'data-method' => 'post',
        ]) ?>
    </div>

</div>