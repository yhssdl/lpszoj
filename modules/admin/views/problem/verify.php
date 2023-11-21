<?php

use yii\helpers\Html;
use app\models\Solution;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $solutions array */
/* @var $model app\models\Problem */
/* @var $newSolution app\models\Solution */

$this->title = $model->title;
$this->params['model'] = $model;
$loadingImgUrl = Yii::getAlias('@web/images/loading.gif');
?>
<p class="lead"><?= Html::encode($this->title) ?></p>
<div class="solutions-view animate__animated animate__fadeInUp">
<div class="alert alert-light">
    <i class="fa fa-info-circle"></i> 提示：题目的验题状态将不会在前台展示．不会出现泄题情况</div>
    <div class="table-responsive">
        <table class="table table-bordered table-rank">
            <thead>
            <tr>
                <th width="60px"><?= Yii::t('app', 'Run ID') ?></th>
                <th width="100px"><?= Yii::t('app', 'Result') ?></th>
                <th width="60px"><?= Yii::t('app', 'Language') ?></th>
                <th width="70px"><?= Yii::t('app', 'Time') ?></th>
                <th width="80px"><?= Yii::t('app', 'Memory') ?></th>
                <th width="80px"><?= Yii::t('app', 'Code Length') ?></th>
                <th width="60px"><?= Yii::t('app', 'Submit Time') ?></th>                
            </tr>
            </thead>
            <tbody>
            <?php foreach ($solutions as $solution): ?>
                <tr>
                    <th><?= $solution['id'] ?></th>
                    <th>
                    <?php
                        if ($solution['result'] <= Solution::OJ_WAITING_STATUS) {
                            $waitingHtmlDom = 'waiting="true"';
                            $loadingImg = "<img src=\"{$loadingImgUrl}\">";
                        } else {
                            $waitingHtmlDom = 'waiting="false"';
                            $loadingImg = "";
                        }
                        $innerHtml =  'data-verdict="' . $solution['result'] . '" data-submissionid="' . $solution['id'] . '" ' . $waitingHtmlDom;
                        if ($solution['result'] == Solution::OJ_AC) {
                            $span = '<span class="text-success"' . $innerHtml . '>' . Solution::getResultList($solution['result']) . '</span>';
                            echo Html::a($span,
                                ['/solution/source', 'id' => $solution['id']],
                                ['onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                            );
                        } else {
                            $span = '<span class="text-danger" ' . $innerHtml . '>' . Solution::getResultList($solution['result']) . $loadingImg . '</span>';
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
                    <th>
                        <?= Html::tag('span', Yii::$app->formatter->asRelativeTime($solution['created_at']), ['title' => $solution['created_at']])?>
                    </th>                    
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($newSolution, 'language')->dropDownList($newSolution::getLanguageList())->label(false) ?>

    <?= $form->field($newSolution, 'source')->widget('app\widgets\codemirror\CodeMirror')->label(false); ?>

    <div class="form-group">
    <div class="row"><div class="col-md-2 col-md-offset-5"><?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?></div></div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$url = \yii\helpers\Url::toRoute(['/solution/verdict']);
$loadingImgUrl = Yii::getAlias('@web/images/loading.gif');
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
var waitingCount = $("span[waiting=true]").length;
if (waitingCount > 0) {
    console.log("There is waitingCount=" + waitingCount + ", starting submissionsEventCatcher...");
    var interval = null;
    var waitingQueue = [];
    $("span[waiting=true]").each(function(){
        waitingQueue.push($(this));
    });
    waitingQueue.reverse();
    var testWaitingsDone = function () {
        updateVerdictByKey(waitingQueue[0]);
        var waitingCount = $("span[waiting=true]").length;
        while (waitingCount < waitingQueue.length) {
            if (waitingCount < waitingQueue.length) {
                waitingQueue.shift();
            }
            if (waitingQueue.length === 0) {
                break;
            }
            updateVerdictByKey(waitingQueue[0]);
            waitingCount = $("span[waiting=true]").length;
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
