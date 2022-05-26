<?php

use yii\helpers\Html;
use app\models\Solution;

/* @var $this yii\web\View */
/* @var $model app\models\Solution */

$this->title = $model->id;
$json = NULL;

if (!$model->canViewResult()) {
    return '暂无权限查看出错信息';
}
?>
<div class="solution-view">
    <h3><?= Yii::t('app', 'Run ID') ?>: <?= Html::a($model->id, ['/solution/detail', 'id' => $model->id]) ?></h3>
    <?php if ($model->solutionInfo != null) : ?>
        <?php if ($model->result == Solution::OJ_CE) : ?>
            <pre><?= \yii\helpers\HtmlPurifier::process($model->solutionInfo->run_info) ?></pre>
        <?php else : ?>
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
    <?php endif; ?>
</div>