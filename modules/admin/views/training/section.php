<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use app\models\Contest;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\Homework */

$this->title = Html::encode($model->title);
$this->params['model'] = $model;
$problems = $model->problems;
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

                                <?= Html::beginForm(['updateproblem', 'id' => $model->id]) ?>

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

                        <?= Html::beginForm(['addproblem', 'id' => $model->id]) ?>
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


        <?= $form->field($model, 'description')->widget(Yii::$app->setting->get('ojEditor')); ?>

        <?= $form->field($model, 'punish_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', '过关题数') . '</span>{input}</div>{hint}'])->textInput()->hint('完成并通过指定的题目数量即可过关，如果为<code>-1</code>时需要通过所有题目。') ?>

        <?= $form->field($model, 'enable_clarify')->radioList([
            0 => '未通过前隐藏后续小节',
            1 => '显示后续小节',
        ])->label(false) ?>



        <?= $form->field($model, 'language')->radioList([
            -1 => 'All',
            0 => 'C',
            1 => 'C++',
            2 => 'Java',
            3 => 'Python3',
        ])->hint('为 All 时可以使用任意的语言编程，否则在比赛中只能以指定的语言编程并提交。') ?>

        <div class="form-group">
        <div class="row"><div class="col-md-2 col-md-offset-5">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>
            </div></div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<hr>
<div class="row"><div class="col-md-2 col-md-offset-5">
    <?= Html::a('删除该小节', ['delete_section', 'id' => $model->id], [
        'class' => 'btn btn-danger btn-block',
        'data-confirm' => '此操作不可恢复，你确定要删除吗？',
        'data-method' => 'post',
    ]) ?>
    </div></div>
</div>

</div>