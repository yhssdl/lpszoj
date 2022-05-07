<?php

use yii\helpers\Html;
use app\models\Solution;

/* @var $this yii\web\View */
/* @var $model app\models\Solution */

$this->title = $model->id;
$json = NULL;

if (!$model->canViewErrorInfo()) {
    return '暂无权限查看出错信息';
}

function subtaskHtml($id, $score, $verdict,$subtask_body)
{
    $scoregot = $score;
    $csscolor = 'panel-success';
    if ($verdict != 4) {
      $scoregot = 0;
      $csscolor = 'panel-warning';
    }
    return '<div class="panel ' . $csscolor . ' test-for-popup">
          <div class="panel-heading" role="tab" id="subtask-heading-' . $id . '">
              <h4 class="panel-title">
                  <a role="button" data-toggle="collapse"
                      href="#subtask-' . $id . '" aria-expanded="false" aria-controls="subtask-' . $id . '">
                      子任务 #' . $id . ', 分数: ' . $score . ', 得分: ' . $scoregot . '
                  </a> 
              </h4> 
          </div> 
          <div id="subtask-' . $id . '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="subtask-heading-' . $id . '"> 
              <div id="subtask-body-' . $id . '" class="panel-body">' . $subtask_body . 
              '</div>
          </div>
      </div>';
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
                                $subtask_body = $subtask_body . Solution::testHtml($testId, $cases[$j]);
                                $testId++;
                            }

                            echo subtaskHtml($i + 1, $score, $verdict,$subtask_body);


                        } else {
                            for ($j = 0; $j < count($cases); $j++) {
                                echo Solution::testHtml($testId, $cases[$j]);
                                $testId++;
                            }
                        }
                    }
                }


                ?>

            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php if (Yii::$app->setting->get('isShowError')) : ?>
    <script>
        var verdict = <?= $model->result; ?>;
        var CE = <?= Solution::OJ_CE; ?>;

        var json = '<?= $json ?>';
        if (verdict != CE) {
            json = JSON.parse(json);
            var subtasks = json.subtasks;
            var testId = 1;
            for (var i = 0; i < subtasks.length; i++) {
                var cases = subtasks[i].cases;
                var score = subtasks[i].score;
                var isSubtask = (subtasks.length != 1);
                if (isSubtask) {
                    var verdict = cases[cases.length - 1].verdict;
                    $("#run-info").append(subtaskHtml(i + 1, score, verdict));
                    for (var j = 0; j < cases.length; j++) {
                        var id = i + 1;
                        $('#subtask-body-' + id).append(testHtml(testId, cases[j]));
                        testId++;
                    }
                } else {
                    for (var j = 0; j < cases.length; j++) {
                        $("#run-info").append(testHtml(testId, cases[j]));
                        testId++;
                    }
                }
            }
            json = "";
        }
        if (verdict == CE) {
            $("#run-info").append(json);
        }
    </script>

<?php endif; ?>