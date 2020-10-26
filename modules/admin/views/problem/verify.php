<?php

use yii\helpers\Html;
use app\models\Solution;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $solutions array */
/* @var $model app\models\Problem */
/* @var $newSolution app\models\Solution */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Problems'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['model'] = $model;
?>
<div class="solutions-view">
    <h1>
        <?= Html::encode($model->title) ?>
    </h1>
    <p class="text-muted">提示：题目的验题状态将不会在前台展示．不会出现泄题情况</p>
    <div class="table-responsive">
        <table class="table table-bordered table-rank">
            <thead>
            <tr>
                <th width="60px"><?= Yii::t('app', 'Run ID') ?></th>
                <th width="60px"><?= Yii::t('app', 'Submit Time') ?></th>
                <th width="100px"><?= Yii::t('app', 'Result') ?></th>
                <th width="60px"><?= Yii::t('app', 'Language') ?></th>
                <th width="70px"><?= Yii::t('app', 'Time') ?></th>
                <th width="80px"><?= Yii::t('app', 'Memory') ?></th>
                <th width="80px"><?= Yii::t('app', 'Code Length') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($solutions as $solution): ?>
                <tr>
                    <th><?= $solution['id'] ?></th>
                    <th>
                        <?= $solution['created_at'] ?>
                    </th>
                    <th>
                    <?php
                        $loadingImgUrl = Yii::getAlias('@web/images/loading.gif');
                        if ($solution['result'] <= Solution::OJ_WAITING_STATUS) {
                            $waitingHtmlDom = 'waiting="true"';
                            $loadingImg = "<img src=\"{$loadingImgUrl}\">";
                        } else {
                            $waitingHtmlDom = 'waiting="false"';
                            $loadingImg = "";
                        }
                        $innerHtml =  'data-verdict="' . $solution['result'] . '" data-submissionid="' . $solution['id'] . '" ' . $waitingHtmlDom;
                        if ($solution['result'] == Solution::OJ_AC) {
                            $span = '<strong class="text-success"' . $innerHtml . '>' . Solution::getResultList($solution['result']) . '</strong>';
                            echo Html::a($span,
                                ['/solution/source', 'id' => $solution['id']],
                                ['onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                            );
                        } else {
                            $span = '<strong class="text-danger" ' . $innerHtml . '>' . Solution::getResultList($solution['result']) . $loadingImg . '</strong>';
                            echo Html::a($span,
                                ['/solution/result', 'id' => $solution['id']],
                                ['onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                            );
                        }
                    ?>

                    </th>
                    <th>
                        <?= Html::a(Solution::getLanguageList($solution['language']), ['/solution/detail', 'id' => $solution['id']], ['target' => '_blank']) ?>
                    </th>
                    <th>
                        <?= $solution['time']  .' '. Yii::t('app', 'MS')?>
                    </th>
                    <th>
                        <?= $solution['memory'] . ' KB'?>
                    </th>
                    <th>
                        <?= $solution['code_length'] ?>
                    </th>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <hr>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($newSolution, 'language')->dropDownList($newSolution::getLanguageList()) ?>

    <?= $form->field($newSolution, 'source')->widget('app\widgets\codemirror\CodeMirror'); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$url = \yii\helpers\Url::toRoute(['/solution/verdict']);
$js = <<<EOF
$('[data-click=solution_info]').click(function() {
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

function updateVerdictByKey(submission) {
    $.get({
        url: "{$url}?id=" + submission.attr('data-submissionid'),
        success: function(data) {
            var obj = JSON.parse(data);
            submission.attr("waiting", obj.waiting);
            submission.text(obj.result);
            if (obj.verdict === "4") {
                submission.attr("class", "text-success")
            }
            if (obj.waiting === "true") {
                submission.append('<img src="{$loadingImgUrl}" alt="loading">');
            }
        }
    });
}
var waitingCount = $("strong[waiting=true]").length;
if (waitingCount > 0) {
    console.log("There is waitingCount=" + waitingCount + ", starting submissionsEventCatcher...");
    var interval = null;
    var waitingQueue = [];
    $("strong[waiting=true]").each(function(){
        waitingQueue.push($(this));
    });
    waitingQueue.reverse();
    var testWaitingsDone = function () {
        updateVerdictByKey(waitingQueue[0]);
        var waitingCount = $("strong[waiting=true]").length;
        while (waitingCount < waitingQueue.length) {
            if (waitingCount < waitingQueue.length) {
                waitingQueue.shift();
            }
            if (waitingQueue.length === 0) {
                break;
            }
            updateVerdictByKey(waitingQueue[0]);
            waitingCount = $("strong[waiting=true]").length;
        }
        console.log("There is waitingCount=" + waitingCount + ", starting submissionsEventCatcher...");
        
        if (interval && waitingCount === 0) {
            console.log("Stopping submissionsEventCatcher.");
            clearInterval(interval);
            interval = null;
        }
    }
    interval = setInterval(testWaitingsDone, 1000);
}
EOF;
$this->registerJs($js);
?>
