<?php

use yii\helpers\Html;
$problems = $model->problems;
$showDown = $pass;
if($t_model->punish_time==0 && $passProblem<$problemSum ) $pass = false;
?>

<div>
    <div class="panel panel-<?php if($pass) echo "success"; else echo "info"; ?>">
        <div class="panel-heading">
            <h4 class="panel-title" style="cursor:pointer" data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $pos ?>" aria-expanded="true">
                <?= $t_model->title ?>
                <a class="pull-right openswitch"><span class="fa fa-angle-double-<?php if($showDown) echo "down"; else echo "up";?>" title="<?php if($showDown) echo "展开"; else echo "收起";?>"></span></a>
                <span class="pull-right" style="margin-right:20px;"> 共<?= $problemSum ?>题<?php if($pass) echo "，已完成";?> </span>
            </h4>
        </div>
        <div id="collapse<?= $pos ?>" class="panel-collapse collapse <?php if(!$showDown) echo "in" ?>" aria-expanded="true">
            <div class="panel-body">
            <?php if(!$pass) : ?>
            <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 本小节共 <strong><?= $problemSum ?></strong> 题，需要完成 <strong><?= $t_model->punish_time ? $t_model->punish_time : $problemSum ?></strong> 题才能通过。</div>
            <?php endif; ?> 
            <?php if ($model->description) : ?>
            <?= Yii::$app->formatter->asMarkdown($model->description) ?>
            <?php endif; ?>
           
                <table class="table table-bordered table-problem-list table-striped ">
                <thead>
                <tr>
                    <th width="80px">#</th>
                    <th><?= Yii::t('app', 'Problem Name') ?></th>
                    <th width="160px">正确 / 提交</th>
                    <th width="120px">解答状态</th>
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
                                        &nbsp;
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
            <!--panel-collapse -->
        </div>
        <!-- panel -->
    </div>










</div>