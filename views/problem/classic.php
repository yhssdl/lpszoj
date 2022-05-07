<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use app\models\Solution;

/* @var $this yii\web\View */
/* @var $model app\models\Problem */
/* @var $solution app\models\Solution */
/* @var $submissions array */

$this->title = $model->id . ' - ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Problems'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

if (!Yii::$app->user->isGuest) {
    $solution->language = Yii::$app->user->identity->language;
}
if(isset($_COOKIE['theme']))
    $theme = $_COOKIE['theme'];
else 
    $theme = "solarized";

$model->setSamples();

$loadingImgUrl = Yii::getAlias('@web/images/loading.gif');
$previousProblemID = $model->getPreviousProblemID();
$nextProblemID = $model->getNextProblemID();
?>
<div class="row">

    <?php if ($this->beginCache('problem-' . $model->id)) : ?>
        <div class="col-md-9 problem-view">

            <h1><?= Html::encode($this->title) ?></h1>

            <div class="content-wrapper">
                <?= Yii::$app->formatter->asMarkdown($model->description) ?>
            </div>

            <h3><?= Yii::t('app', 'Input') ?></h3>
            <div class="content-wrapper">
                <?= Yii::$app->formatter->asMarkdown($model->input) ?>
            </div>

            <h3><?= Yii::t('app', 'Output') ?></h3>
            <div class="content-wrapper">
                <?= Yii::$app->formatter->asMarkdown($model->output) ?>
            </div>

            <h3><?= Yii::t('app', 'Examples') ?></h3>
            <div class="content-wrapper">
                <div class="sample-test">
                    <div class="input">
                        <h4><?= Yii::t('app', 'Input') ?></h4>
                        <pre><?= Html::encode($model->sample_input) ?></pre>
                    </div>
                    <div class="output">
                        <h4><?= Yii::t('app', 'Output') ?></h4>
                        <pre><?= Html::encode($model->sample_output) ?></pre>
                    </div>

                    <?php if ($model->sample_input_2 != '' || $model->sample_output_2 != '') : ?>
                        <div class="input">
                            <h4><?= Yii::t('app', 'Input') ?></h4>
                            <pre><?= Html::encode($model->sample_input_2) ?></pre>
                        </div>
                        <div class="output">
                            <h4><?= Yii::t('app', 'Output') ?></h4>
                            <pre><?= Html::encode($model->sample_output_2) ?></pre>
                        </div>
                    <?php endif; ?>

                    <?php if ($model->sample_input_3 != '' || $model->sample_output_3 != '') : ?>
                        <div class="input">
                            <h4><?= Yii::t('app', 'Input') ?></h4>
                            <pre><?= Html::encode($model->sample_input_3) ?></pre>
                        </div>
                        <div class="output">
                            <h4><?= Yii::t('app', 'Output') ?></h4>
                            <pre><?= Html::encode($model->sample_output_3) ?></pre>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($model->hint)) : ?>
                <h3><?= Yii::t('app', 'Hint') ?></h3>
                <div class="content-wrapper">
                    <?= Yii::$app->formatter->asMarkdown($model->hint) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($model->source)) : ?>
                <h3><?= Yii::t('app', 'Source') ?></h3>
                <div class="content-wrapper">
                    <?= Yii::$app->formatter->asMarkdown($model->source) ?>
                </div>
            <?php endif; ?>
        </div>
        <?php $this->endCache(); ?>
    <?php endif; ?>
    <div class="col-md-3 problem-info">
        <div class="panel panel-default">
            <!-- Table -->
            <table class="table">
                <tbody>
                    <tr>
                        <td><?= Yii::t('app', 'Time Limit') ?></td>
                        <td>
                            <?= Yii::t('app', '{t, plural, =1{# second} other{# seconds}}', ['t' => intval($model->time_limit)]); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Memory Limit') ?></td>
                        <td><?= $model->memory_limit ?> MB</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php Modal::begin([
            'header' => Yii::t('app', 'Submit') . '：' . Html::encode($model->id . '. ' . $model->title),
            'size' => Modal::SIZE_LARGE,
            'toggleButton' => [
                'label' => '<span class="fa fa-plus"></span> ' . Yii::t('app', 'Submit'),
                'class' => 'btn btn-success'
            ]
        ]); ?>
        <?php if (Yii::$app->user->isGuest) : ?>
            <?= app\widgets\login\Login::widget(); ?>
        <?php else : ?>
            <?php $form = ActiveForm::begin(); ?>
            <div>
                <div style="float:left;height: 34px;padding: 6px 12px;">
                    <?= Yii::t('app', 'Language') ?>：
                </div>
                <div style="float:left;">
                    <?= $form->field($solution, 'language', ['options' => ['style' => 'margin: 0']])
                        ->dropDownList($solution::getLanguageList(), ['style' => 'width: auto'])->label(false) ?>

                </div>

                <div style="float:right;">
                    <select id="solution-theme" class="form-control" name="solution-theme" style="width: auto" aria-required="true" onchange="themeChange()">
                        <option value="solarized" <?php if ($theme == "solarized") echo "selected=''"; ?>>solarized</option>
                        <option value="material" <?php if ($theme == "material") echo "selected=''"; ?>>material</option>
                        <option value="monokai" <?php if ($theme == "monokai") echo "selected=''"; ?>>monokai</option>
                    </select>

                </div>
                <div style="float:right;height: 34px;padding: 6px 12px;">
                    <?= Yii::t('app', 'Theme') ?>：
                </div>
                <div style="clear:both"></div>

            </div>

            <?= $form->field($solution, 'source')->widget('app\widgets\codemirror\CodeMirror')->label(false); ?>


            <div class="form-group">
            <div class="row"><div class="col-md-4 col-md-offset-4"><?= Html::submitButton('<span class="fa fa-send"></span> ' . Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?></div></div>
            </div>
            <?php ActiveForm::end(); ?>
        <?php endif; ?>
        <?php Modal::end(); ?>

        <?php if (Yii::$app->setting->get('isDiscuss')) : ?>
            <?= Html::a(
                '<span class="fa fa-comment"></span> ' . Yii::t('app', 'Discuss'),
                ['/problem/discuss', 'id' => $model->id],
                ['class' => 'btn btn-default']
            )
            ?>
        <?php endif; ?>
        <?php if (!empty($model->solution)) {
            if (!empty($model->solution)) {
                $bShow = $model->show_solution || ($model->isSolved() && $model->show_solution == 0);
                if ($bShow)
                    echo Html::a(
                        '<i class="fa fa-dropbox"></i> ' . Yii::t('app', '题解'),
                        ['/problem/solution', 'id' => $model->id],
                        ['class' => 'btn btn-default']
                    );
                else
                    echo '<button type="button" class="btn btn-default disabled" title= "提交程序正确后才能查看。"><i class="fa fa-dropbox"></i> ' . Yii::t('app', '题解') . '</button>';
            }
        }
        ?>
        <?= Html::a(
            '<span class="fa fa-signal"></span> ' . Yii::t('app', 'Stats'),
            ['/problem/statistics', 'id' => $model->id],
            ['class' => 'btn btn-default']
        ) ?>

        <hr />

        <?= Html::a(
            '<span class="fa fa-arrow-left"></span> 上一题',
            $previousProblemID ? ['/problem/view', 'id' => $previousProblemID, 'view' => 'classic'] : 'javascript:void(0);',
            ['class' => 'btn btn-default', 'style' => 'width: 40%', 'disabled' => !$previousProblemID]
        ) ?>

        <?= Html::a(
            '下一题 <span class="fa fa-arrow-right"></span>',
            $nextProblemID ? ['/problem/view', 'id' => $nextProblemID, 'view' => 'classic'] : 'javascript:void(0);',
            ['class' => 'btn btn-default', 'style' => 'width: 50%', 'disabled' => !$nextProblemID]
        ) ?>

        <?php if (!Yii::$app->user->isGuest && !empty($submissions)) : ?>
            <div class="panel panel-default" style="margin-top: 40px">
                <div class="panel-heading"><?= Yii::t('app', 'Submissions') ?></div>
                <!-- Table -->
                <table class="table">
                    <tbody>
                        <?php foreach ($submissions as $sub) : ?>
                            <tr>
                                <td title="<?= $sub['created_at'] ?>">
                                    <?= Yii::$app->formatter->asRelativeTime($sub['created_at']) ?>
                                </td>
                                <td>
                                    <?php
                                    if ($sub['result'] <= Solution::OJ_WAITING_STATUS) {
                                        $waitingHtmlDom = 'waiting="true"';
                                        $loadingImg = "<img src=\"{$loadingImgUrl}\">";
                                    } else {
                                        $waitingHtmlDom = 'waiting="false"';
                                        $loadingImg = "";
                                    }
                                    $innerHtml =  'data-verdict="' . $sub['result'] . '" data-submissionid="' . $sub['id'] . '" ' . $waitingHtmlDom;
                                    if ($sub['result'] == Solution::OJ_AC) {
                                        $span = '<span class="text-success"' . $innerHtml . '>' . Solution::getResultList($sub['result']) . '</span>';
                                        echo Html::a(
                                            $span,
                                            ['/solution/source', 'id' => $sub['id']],
                                            ['onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                                        );
                                    } else {
                                        $span = '<span class="text-danger" ' . $innerHtml . '>' . Solution::getResultList($sub['result']) . $loadingImg . '</span>';
                                        echo Html::a(
                                            $span,
                                            ['/solution/result', 'id' => $sub['id']],
                                            ['onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                                        );
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?= Html::a(
                                        '<span class="fa fa-pencil-square-o"></span>',
                                        ['/solution/source', 'id' => $sub['id']],
                                        ['title' => '查看源码', 'onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php Modal::begin([
    'options' => ['id' => 'solution-info']
]); ?>
<div id="solution-content">
</div>
<?php Modal::end(); ?>

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
<script>
    function themeChange() {
        var sel_theme = document.getElementById("solution-theme").value;
        editor.setOption("theme", sel_theme);
        document.cookie = "theme=" + sel_theme;
    }
</script>