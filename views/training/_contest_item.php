<?php

use yii\helpers\Html;

$problems = $model->problems;
?>

<div>
    <table class="table table-bordered table-problem-list table-striped ">
        <thead>
            <tr>
                <th colspan="4"><?= $t_model->title ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($problems as $key => $p) : ?>
                <tr class="animate__animated animate__fadeInUp">
                    <th><?= Html::a('P' . ($key + 1), ['/training/problem', 'id' => $model->id, 'pid' => $key, '#' => 'problem-anchor']) ?></th>
                    <td><?= Html::a(Html::encode($p['title']), ['/training/problem', 'id' => $model->id, 'pid' => $key, '#' => 'problem-anchor']) ?></td>
                    <th>
                        <?= $submissionStatistics[$p['problem_id']]['solved'] . ' / ' . $submissionStatistics[$p['problem_id']]['submit'] ?>
                    </th>
                    <th>
                        <?php if (!isset($loginUserProblemSolvingStatus[$p['problem_id']])) : ?>
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