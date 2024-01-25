<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Contest;

/* @var $this yii\web\View */
/* @var $model app\models\Contest */
/* @var $solution app\models\Solution */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $data array */

$this->title = $model->title;
$this->params['model'] = $model;

$problems = $model->problems;
$loginUserProblemSolvingStatus = $model->getLoginUserProblemSolvingStatus();
$submissionStatistics = $model->getSubmissionStatistics();
?>
<br>
<div class="contest-overview ">
    <?php
        if ($model->description){
            echo '<div class="alert alert-light">';
            echo Yii::$app->formatter->asMarkdown($model->description);
            echo '</div>';
        }
    ?>
    <div>
        <table class="table table-bordered table-problem-list table-striped ">
            <thead>
                <tr>
                    <th width="80px">#</th>
                    <?php
                    if ($model->isContestEnd()) {
                        echo "<th width='100px'>题号</th>";
                    }
                    ?>
                    <th><?= Yii::t('app', 'Problem Name') ?></th>
                    <th width="160px">正确 / 提交</th>
                    <th width="120px">解答状态</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($problems as $key => $p) : ?>
                    <tr class="animate__animated animate__fadeInUp">
                        <th><?= Html::a('P' . ($key + 1), ['/contest/problem', 'id' => $model->id, 'pid' => $key, '#' => 'problem-anchor']) ?></th>
                        <?php
                        if ($model->isContestEnd()) {
                            echo "<th>" . Html::a($p['problem_id'], ['/problem/view', 'id' => $p['problem_id']]) . "</th>";
                        }
                        ?>
                        <td><?= Html::a(Html::encode($p['title']), ['/contest/problem', 'id' => $model->id, 'pid' => $key, '#' => 'problem-anchor'],['data-pjax' => '0']) ?></td>
                        <th>
                            <?php
                            if ($model->type == Contest::TYPE_OI && $model->getRunStatus() == Contest::STATUS_RUNNING) {
                                echo '? / ' . $submissionStatistics[$p['problem_id']]['submit'];
                            } else {
                                echo $submissionStatistics[$p['problem_id']]['solved'] . ' / ' . $submissionStatistics[$p['problem_id']]['submit'];
                            }
                            ?>
                        </th>
                        <th>
                            <?php if (!isset($loginUserProblemSolvingStatus[$p['problem_id']])) : ?>

                            <?php elseif ($model->type == Contest::TYPE_OI && $model->getRunStatus() == Contest::STATUS_RUNNING) : ?>
                                <span class="fa fa-question-circle"></span>
                            <?php elseif ($loginUserProblemSolvingStatus[$p['problem_id']] == \app\models\Solution::OJ_AC) : ?>
                                <span class="fa fa-check text-success" title="正确解答"></span>
                            <?php elseif ($loginUserProblemSolvingStatus[$p['problem_id']] < 4) : ?>
                                <span class="fa fa-question-circle text-muted" title="等待测评"></span>
                            <?php else : ?>
                                <span class="fa fa-remove text-danger" title="未正确解答"></span>
                            <?php endif; ?>
                        </th>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    if ($dataProvider->count > 0) {
        echo '<br>';
        echo GridView::widget([
            'layout' => '{items}{pager}',
            'pager' => [
                'firstPageLabel' => Yii::t('app', 'First'),
                'prevPageLabel' => '« ',
                'nextPageLabel' => '» ',
                'lastPageLabel' => Yii::t('app', 'Last'),
                'maxButtonCount' => 10
            ],
            'dataProvider' => $dataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) {
                return ['class' => 'animate__animated animate__fadeInUp'];
            },
            'options' => ['class' => 'table-responsive'],
            'columns' => [

                [
                    'attribute' => Yii::t('app', 'Announcement'),
                    'value' => function ($model, $key, $index, $column) {
                        return Yii::$app->formatter->asMarkdown($model->content);
                    },
                    'format' => 'html',
                ],
                [
                    'attribute' => 'created_at',
                    'options' => ['width' => '150px'],
                    'format' => 'datetime'
                ],
            ],
        ]);
    }
    ?>
</div>