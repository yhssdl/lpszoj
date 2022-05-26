<?php

use yii\helpers\Html;
use app\models\Solution;

/* @var $this yii\web\View */
/* @var $model app\models\Solution */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Status'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="table-responsive">
    <table class="table table-bordered table-rank">
        <thead>
            <tr>
                <th width="120px"><?= Yii::t('app', 'Run ID') ?></th>
                <th width="120px"><?= Yii::t('app', 'Author') ?></th>
                <th width="200px"><?= Yii::t('app', 'Problem') ?></th>
                <th width="80px"><?= Yii::t('app', 'Lang') ?></th>
                <th><?= Yii::t('app', 'Verdict') ?></th>
                <?php if (Yii::$app->setting->get('oiMode')) : ?>
                    <th width="80px"><?= Yii::t('app', 'Score') ?></th>
                <?php endif; ?>
                <th><?= Yii::t('app', 'Time') ?></th>
                <th><?= Yii::t('app', 'Memory') ?></th>
                <th><?= Yii::t('app', 'Code Length') ?></th>
                <th><?= Yii::t('app', 'Submit Time') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th><?= $model->id ?></th>
                <th><?= Html::a(Html::encode($model->user->nickname), ['/user/view', 'id' => $model->created_by]) ?></th>
                <th><?= Html::a(Html::encode($model->problem_id . ' - ' . $model->problem->title), ['/problem/view', 'id' => $model->problem_id]) ?></th>
                <th><?= Solution::getLanguageList($model->language) ?></th>
                <th>
                    <?php if ($model->canViewResult()) {
                        echo Solution::getResultList($model->result);
                    } else {
                        echo Solution::getResultList(Solution::OJ_WT0);
                    } ?>
                </th>
                <?php if (Yii::$app->setting->get('oiMode')) : ?>
                    <th width="80px">
                        <?php
                        if ($model->canViewResult()) {
                            echo $model->score;
                        } else {
                            echo '-';
                        }
                        ?>
                    </th>
                <?php endif; ?>
                <th>
                    <?php
                    if ($model->canViewResult()) {
                        echo $model->time;
                    } else {
                        echo '-';
                    }
                    ?> MS
                </th>
                <th>
                    <?php
                    if ($model->canViewResult()) {
                        echo $model->memory;
                    } else {
                        echo '-';
                    }
                    ?> KB
                </th>
                <th><?= $model->code_length ?></th>
                <th><?= Html::tag('span', Yii::$app->formatter->asRelativeTime($model->created_at), ['title' => $model->created_at]) ?></th>
            </tr>
        </tbody>
    </table>
</div>
<?php if ($model->canViewResult()) : ?>
    <hr>
    <h3>测试点：<?= $model->getTestCount() ?> 个，通过：<?= $model->getPassedTestCount() ?> 个</h3>
    <h3>
        <?php for ($i = 1; $i <= $model->getTestCount(); $i++) : ?>
            <?php if ($i <= $model->getPassedTestCount()) : ?>
                <span class="fa fa-check-circle text-success"></span>
            <?php else : ?>
                <span class="fa fa-remove-circle text-danger"></span>
            <?php endif; ?>
        <?php endfor; ?>
    </h3>
<?php endif; ?>

<?php if ($model->canViewSource()) : ?>
    <hr>
    <div class="pre">
        <p><?= Html::encode($model->source) ?></p>
    </div>
<?php endif; ?>

<?php if ($model->solutionInfo != null && $model->canViewResult()) : ?>
    <hr>
    <h3><?= Yii::t('app', 'Judgement Protocol') ?>:</h3>
    <div id="run-info">
        <?php
        $json = $model->solutionInfo->run_info;
        $json = str_replace("<", "&lt;", $json);
        $json = str_replace(">", "&gt;", $json);
        $json = str_replace(PHP_EOL, "<br>", $json);
        $json = str_replace("\\n", "<br>", $json);
        $json = str_replace("'", "\'", $json);
        $json = str_replace("\\r", "", $json);


        // echo $json;
        // 将JSON数据解码为PHP对象
        $obj = json_decode($json);
        $testId = 1;

        if($obj!=null){
            // 通过对象循环
            foreach ($obj as $key => $subtasks) {
                for ($i = 0; $i < count($subtasks); $i++) {
                    $cases = $subtasks[$i]->cases;
                    $score = $subtasks[$i]->score;
                    $isSubtask = count($subtasks) != 1;
                    if ($isSubtask) {
                        $verdict = $cases[count($cases) - 1]->verdict;
                        $subtask_body = "";
                        for ($j = 0; $j < count($cases); $j++) {
                            $id = $i + 1;
                            $subtask_body = $subtask_body . Solution::testHtml($model,$testId, $cases[$j]);
                            $testId++;
                        }
                        echo Solution::subtaskHtml($i + 1, $score, $verdict, $subtask_body);
                    } else {
                        for ($j = 0; $j < count($cases); $j++) {
                            echo Solution::testHtml($model,$testId, $cases[$j]);
                            $testId++;
                        }
                    }
                }
            }

        }


        ?>
    </div>
<?php endif; ?>