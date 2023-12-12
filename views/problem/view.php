<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use app\models\Solution;
use app\models\Problem;
use app\models\User;
/* @var $this yii\web\View */
/* @var $model app\models\Problem */
/* @var $solution app\models\Solution */
/* @var $submissions array */

$this->title = $model->id . ' - ' . $model->title;
$label_i = 0;
$this->registerJsFile(Yii::getAlias('@web/js/splitter.min.js'));
$this->registerJs("
Split(['.problem-left', '.problem-right'], {
    sizes: [50, 50],
});
");
$this->registerCss("
    body {
        overflow: hidden;
    }
    .wrap {
        display: flex;
        flex-direction: column;
        padding: 0;
        margin: 0;
        height: 100%;
    }
  
    .wrap > .container {
        padding: 0;
        display: flex;
        flex-direction: column;
        flex: 1 1 0;
        overflow: hidden;
    }
    .main-container {
        height: 100%;
    }
    .problem-container {
        padding: 20px 20px 4px 20px;
        display: flex;
        flex-direction: column;
        height: 100%;
        background: #fff;
    }
    .problem-splitter {
        display: flex;
        flex-direction: row;
        flex: 1 1 0;
        overflow: hidden;
    }
    .problem-left {
        overflow: hidden;
        display: flex;
        flex-direction: column;
        flex: 1 0 0;
    }
    .problem-left, .problem-right {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-flex-direction: column;
        -ms-flex-direction: column;
        flex-direction: column;
        height: 100%;
    }
    .problem-description {
        overflow-x: hidden;
        height: 100%;
    }
    .problem-header {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid rgb(225, 228, 232);
    }
    .problem-header .problem-meta {
        display: flex;
        text-align: center;
        color: #666;
        font-size: 12px;
        margin: 0px;
    }
    .problem-header .separator {
        width: 1px;
        height: 100%;
        margin: 0px 20px;
        background: rgb(238, 238, 238);
    }
    .problem-right > .problem-editor {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .problem-right .problem-editor .code-input {
        height: 100%;
        overflow: hidden;
    }
    .problem-wrap {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        overflow: hidden;
    }
    .problem-footer {
        display: flex;
        padding: 5px;
        border-top: 1px solid #eee;
    }
    .problem-left .problem-footer {
        justify-content: flex-end;
    }
    .problem-right .problem-footer {
        justify-content: space-between;
    }
    .CodeMirror {
        height: 100%;
        font-family: Menlo,Monaco,Consolas,Courier New,monospace;
    }
    .gutter {
        background-color: #eee;
        background-repeat: no-repeat;
        background-position: 50%;
    }
    .gutter.gutter-vertical {
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAFAQMAAABo7865AAAABlBMVEVHcEzMzMzyAv2sAAAAAXRSTlMAQObYZgAAABBJREFUeF5jOAMEEAIEEFwAn3kMwcB6I2AAAAAASUVORK5CYII=');
    }

    .gutter.gutter-horizontal {
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAeCAYAAADkftS9AAAAIklEQVQoU2M4c+bMfxAGAgYYmwGrIIiDjrELjpo5aiZeMwF+yNnOs5KSvgAAAABJRU5ErkJggg==');
    }
");
if (!Yii::$app->user->isGuest) {
    $solution->language = Yii::$app->user->identity->language;
    $solution->created_by = Yii::$app->user->id;
}

if (isset($_COOKIE['theme']))
    $theme = $_COOKIE['theme'];
else
    $theme = "solarized";

$model->setSamples();

$loadingImgUrl = Yii::getAlias('@web/images/loading.gif');
$previousProblemID = $model->getPreviousProblemID();
$nextProblemID = $model->getNextProblemID();
?>

<div class="main-container">
    <div class="problem-container">
        <div class="problem-splitter">
            <div class="problem-left">
                <div class="text-center content-title"><?= Html::encode($this->title) ?></div>
                <div class="problem-description">
                    <div class="content-header"><?= Yii::t('app', 'Description') ?></div>
                    <div class="content-wrapper">
                        <?= Yii::$app->formatter->asMarkdown($model->description) ?>
                    </div>

                    <div class="content-header"><?= Yii::t('app', 'Input') ?></div>
                    <div class="content-wrapper">
                        <?= Yii::$app->formatter->asMarkdown($model->input) ?>
                    </div>

                    <div class="content-header"><?= Yii::t('app', 'Output') ?></div>
                    <div class="content-wrapper">
                        <?= Yii::$app->formatter->asMarkdown($model->output) ?>
                    </div>

                    <div class="content-header"><?= Yii::t('app', 'Examples') ?></div>
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
                        <div class="content-header"><?= Yii::t('app', 'Hint') ?></div>
                        <div class="content-wrapper">
                            <?= Yii::$app->formatter->asMarkdown($model->hint) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($model->source)) : ?>
                        <div class="content-header"><?= Yii::t('app', 'Source') ?></div>
                        <div class="content-wrapper">
                            <?= Yii::$app->formatter->asMarkdown($model->source) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($model->tags)) : ?>
                        <div class="content-header"><?= Yii::t('app', 'Tags') ?></div>
                        <div class="content-wrapper">
                            <?php
                            $tags = explode(',', $model->tags);
                            $tagsCount = count($tags);
                            if ($tagsCount > 0) {
                                $res = '<span>';
                                foreach ((array)$tags as $tag) {
                                    $label = Problem::getColorLabel($label_i);
                                    $label_i = $label_i + 1;
                                    $res .= Html::a(Html::encode($tag), [
                                        '/problem/index', 'tag' => $tag
                                    ], ['class' => $label]);
                                    $res .= ' ';
                                }
                                $res .= '</span>';
                            }

                            echo $res;

                            ?>

                        </div>
                    <?php endif; ?>
                </div>
                <div class="problem-footer">
                    <?= Html::a(
                        '<span class="fa fa-arrow-left"></span> 上一题',
                        $previousProblemID ? ['/problem/view', 'id' => $previousProblemID] : 'javascript:void(0);',
                        ['class' => 'btn btn-default', 'disabled' => !$previousProblemID]
                    ) ?>&nbsp;

                    <?= Html::a(
                        '下一题 <span class="fa fa-arrow-right"></span>',
                        $nextProblemID ? ['/problem/view', 'id' => $nextProblemID] : 'javascript:void(0);',
                        ['class' => 'btn btn-default', 'disabled' => !$nextProblemID]
                    ) ?>
                </div>
            </div>
            <div class="problem-right">

                <div class="problem-header">
                    <div class="problem-meta">
                        <div>
                            <p><?= Yii::t('app', 'Time Limit') ?> </p>
                            <p><?= intval($model->time_limit) ?> 秒</p>
                        </div>
                        <div class="separator"></div>
                        <div>
                            <p><?= Yii::t('app', 'Memory Limit') ?> </p>
                            <p><?= $model->memory_limit ?> MB</p>
                        </div>
                        <div class="separator"></div>
                        <div class="problem-submit-count">
                            <p>通过次数</p>
                            <p><?= $model->accepted ?></p>
                        </div>
                        <div class="separator"></div>
                        <div class="problem-accepted-count">
                            <p>提交次数</p>
                            <p><?= $model->submit ?></p>
                        </div>
                        <div class="separator"></div>
                        <div>
                            <p>
                                <?= Html::a(
                                    '<span class="fa fa-tasks"></span> ' . Yii::t('app', 'Stats'),
                                    ['/problem/statistics', 'id' => $model->id],
                                    ['view' => 'classic']
                                ) ?>
                            </p>
                            <p></p>
                        </div>
                    </div>
                </div>
                <?php $form = ActiveForm::begin(['options' => ['class' => 'problem-editor']]); ?>

                <div>
                    <div style="float:left;height: 34px;padding: 6px 12px;">
                        <?= Yii::t('app', 'Language') ?>：
                    </div>
                    <div style="float:left;">
                        <?= $form->field($solution, 'language', ['options' => ['style' => 'margin: 0']])
                            ->dropDownList($solution::getLanguageList(), ['style' => 'width: auto'])->label(false) ?>
                    </div>

                    <div style="float:right;">
                        <select id="solution-theme" class="form-control" name="solution-theme" style="width: auto" aria-required="true">
                            <option value="solarized" <?php if ($theme == "solarized") echo "selected=''"; ?>>solarized</option>
                            <option value="material" <?php if ($theme == "material") echo "selected=''"; ?>>material</option>
                            <option value="monokai" <?php if ($theme == "monokai") echo "selected=''"; ?>>monokai</option>
                        </select>
                    </div>
                    <div style="float:right;height: 34px;padding: 6px 12px;">
                        <?= Yii::t('app', 'Theme') ?>：
                    </div>
                </div>

                <?= $form->field($solution, 'source', ['options' => ['class' => 'code-input']])
                    ->widget('app\widgets\codemirror\CodeMirror')->label(false); ?>

                <div class="problem-footer">
                    <?php
                    if (Yii::$app->user->isGuest) {
                        echo '<span><i class=" fa fa-info-circle"></i> 登录以提交代码</span>';
                    } else {
                        echo Html::submitButton('<span class="fa fa-send"></span> ' . Yii::t('app', 'Submit'), ['class' => 'btn btn-success']);

                        if (Yii::$app->setting->get('isDiscuss')) {
                            echo Html::a(
                                '<span class="fa fa-comment"></span> ' . Yii::t('app', 'Discuss'),
                                ['/problem/discuss', 'id' => $model->id],
                                ['class' => 'btn btn-default']
                            );
                        }

                        if (!empty($model->solution)) {
                            $bShow = $model->show_solution || ($model->isSolved() && $model->show_solution == 0);
                            if ($bShow)
                                echo Html::a(
                                    '<i class="fa fa-dropbox"></i> ' . Yii::t('app', '题解'),
                                    ['/problem/solution', 'id' => $model->id],
                                    ['class' => 'btn btn-default']
                                );
                            else
                                echo '<button type="button" class="btn btn-default disabled" title= "提交程序正确后才能查看。"><i class="fa fa-dropbox"></i>' . Yii::t('app', '题解') . '</button>';
                        }
                    }
                    ?>

                    <?php if (!Yii::$app->user->isGuest && !empty($submissions)) : ?>
                        <div>
                            <?php Modal::begin([
                                'header' => Yii::t('app', 'Submit') . '：' . Html::encode($model->id . '. ' . $model->title),
                                'toggleButton' => [
                                    'label' => '我的提交',
                                    'class' => 'btn btn-default'
                                ]
                            ]); ?>
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
                                                } else {
                                                    $span = '<span class="text-danger" ' . $innerHtml . '>' . Solution::getResultList($sub['result']) . $loadingImg . '</span>';
                                                }
                                                if ($solution->canViewResult()) {
                                                    echo Html::a(
                                                        $span,
                                                        ['/solution/result', 'id' => $sub['id']],
                                                        ['onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                                                    );
                                                    }else{
                                                        echo $span;
                                                    }
                                                
                                                ?>
                                            </td>
                                            <?php if ($solution->canViewSource()) : ?>
                                            <td>
                                                <?= Html::a(
                                                    '<span class="fa fa-pencil-square-o"></span>',
                                                    ['/solution/source', 'id' => $sub['id']],
                                                    ['title' => '查看源码', 'onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                                                ) ?>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php Modal::end(); ?>

                            <?php $sub = $submissions[0]; ?>
                            <span>&nbsp;&nbsp;<?= Yii::$app->formatter->asRelativeTime($sub['created_at']) ?>&nbsp;&nbsp;</span>
                            <span>
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
                                } else {
                                    $span = '<span class="text-danger" ' . $innerHtml . '>' . Solution::getResultList($sub['result']) . $loadingImg . '</span>';  
                                }
                                if ($solution->canViewResult()) {
                                    echo Html::a(
                                        $span,
                                        ['/solution/result', 'id' => $sub['id']],
                                        ['onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                                    );
                                }else{
                                    echo $span;
                                }
                             
                                ?>
                            </span>
                            <?php if ($solution->canViewSource()) : ?>
                            <span>
                                &nbsp;<?= Html::a(
                                                '<span class="fa fa-pencil-square-o"></span>',
                                                ['/solution/source', 'id' => $sub['id']],
                                                ['title' => '查看源码', 'onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                                            )
                                            ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php Modal::begin([
    'options' => ['id' => 'solution-info']
]); ?>
<div id="solution-content">
</div>
<?php Modal::end(); ?>

<?php
$code_lang = "";
if($solution->language == 3){
    $code_lang = "editor.setOption(\"mode\", \"python\");";
}
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
$("#solution-theme").on("change", function () {
    var sel_theme = document.getElementById("solution-theme").value;
    editor.setOption("theme", sel_theme);
    editor.setOption("mode", "python");
    document.cookie = "theme=" + sel_theme;
});
$("#solution-language").on("change", function () {
    var sel_lang = document.getElementById("solution-language").value;
    if(sel_lang=='3'){
        editor.setOption("mode", "python");
        document.cookie = "code_lang=python";
    }
    else{
        editor.setOption("mode", "text/x-c++src");
        document.cookie = "code_lang=text/x-c++src";
    }
});
$code_lang
EOF;
$this->registerJs($js);
?>