<?php

use app\models\Solution;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $model app\models\Contest */
/* @var $solution app\models\Solution */
/* @var $problem array */
/* @var $submissions array */

$this->title = Html::encode($model->title) . ' - ' . ($problem['title'] ?? null);
$this->params['model'] = $model;

if (isset($_COOKIE['theme']))
    $theme = $_COOKIE['theme'];
else
    $theme = "one-dark";

if (!Yii::$app->user->isGuest) {
    if ($model->language == -1)
        $solution->language = Yii::$app->user->identity->language;
    else
        $solution->language = $model->language;
    $solution->created_by = Yii::$app->user->id;
}
$problems = $model->problems;
$loginUserProblemSolvingStatus = $model->getLoginUserProblemSolvingStatus();
if (empty($problems)) {
    echo '<br><div class="alert alert-light"><i class=" fa fa-info-circle"></i> 当前没有添加题目，请联系管理员。</div>';
    return;
}

$nav = [];
foreach ($problems as $key => $p) {
    $nav[] = [
        'label' => 'P' . ($key + 1),
        'url' => [
            'problem',
            'id' => $model->id,
            'pid' => $key,
        ]
    ];
}
try{
    $sample_input = unserialize($problem['sample_input']);
    $sample_output = unserialize($problem['sample_output']);
}catch(\Throwable $e){
    $sample_input =  array("无","","");
    $sample_output =  array("无","","");
}
if($sample_input==false) $sample_input =  array("无","","");
if($sample_output==false) $sample_output =  array("无","","");

$loadingImgUrl = Yii::getAlias('@web/images/loading.gif');
?>
<div class="problem-view">
    <div class="text-center">
        <?= Nav::widget([
            'items' => $nav,
            'options' => ['class' => 'pagination']
        ]) ?>
    </div>
    <div class="row animate__animated animate__fadeInUp">
        <div class="col-md-9 problem-view">
            <?php if ($this->beginCache('contest_problem_view' . $model->id . '_' . $problem['num'] . '_ ' . $problem['id'])) : ?>
                <div class="text-center content-title"><?= Html::encode('P' . (1 + $problem['num']). '. ' . $problem['title']) ?></div>

                <div class="content-header"><?= Yii::t('app', 'Description') ?></div>
                <div class="content-wrapper">
                    <?= Yii::$app->formatter->asMarkdown($problem['description']) ?>
                </div>

                <div class="content-header"><?= Yii::t('app', 'Input') ?></div>
                <div class="content-wrapper">
                    <?= Yii::$app->formatter->asMarkdown($problem['input']) ?>
                </div>

                <div class="content-header"><?= Yii::t('app', 'Output') ?></div>
                <div class="content-wrapper">
                    <?= Yii::$app->formatter->asMarkdown($problem['output']) ?>
                </div>

                <div class="content-header"><?= Yii::t('app', 'Examples') ?></div>
                <div class="content-wrapper">
                    <div class="sample-test">
                        <div class="input">
                            <h4><?= Yii::t('app', 'Input') ?></h4>
                            <pre><?= $sample_input[0] ?></pre>
                        </div>
                        <div class="output">
                            <h4><?= Yii::t('app', 'Output') ?></h4>
                            <pre><?= $sample_output[0] ?></pre>
                        </div>

                        <?php if ($sample_input[1] != '' || $sample_output[1] != '') : ?>
                            <div class="input">
                                <h4><?= Yii::t('app', 'Input') ?></h4>
                                <pre><?= $sample_input[1] ?></pre>
                            </div>
                            <div class="output">
                                <h4><?= Yii::t('app', 'Output') ?></h4>
                                <pre><?= $sample_output[1] ?></pre>
                            </div>
                        <?php endif; ?>

                        <?php if ($sample_input[2] != '' || $sample_output[2] != '') : ?>
                            <div class="input">
                                <h4><?= Yii::t('app', 'Input') ?></h4>
                                <pre><?= $sample_input[2] ?></pre>
                            </div>
                            <div class="output">
                                <h4><?= Yii::t('app', 'Output') ?></h4>
                                <pre><?= $sample_output[2] ?></pre>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($problem['hint'])) : ?>
                    <div class="content-header"><?= Yii::t('app', 'Hint') ?></div>
                    <div class="content-wrapper">
                        <?= Yii::$app->formatter->asMarkdown($problem['hint']) ?>
                    </div>
                <?php endif; ?>
                <?php $this->endCache(); ?>
            <?php endif; ?>
            <div class="content-header" id="submit-code"><?= Yii::t('app', 'Submit') ?></div><br>
            <div class="content-wrapper">
                <?php if ($model->isContestEnd() && time() < strtotime($model->end_time) + 5 * 60 && !Yii::$app->user->isGuest && $model->isUserInContest() && !Yii::$app->user->identity->isAdmin() && !Yii::$app->user->identity->isVip()) : ?>
                    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 比赛已结束，比赛结束五分钟后开放提交。</div>
                <?php elseif ($model->isContestEnd() && !Yii::$app->user->isGuest && !$model->isUserInContest() && !Yii::$app->user->identity->isAdmin() && !Yii::$app->user->identity->isVip()) : ?>

                    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 比赛注册已关闭，你没有提交评测的权限。阅读比赛公告或联系管理员以了解如何补题。</div>

                <?php else : ?>
                    <?php if (Yii::$app->user->isGuest) : ?>
                        <?= app\widgets\login\Login::widget(); ?>
                    <?php else : ?>
                        <?php $form = ActiveForm::begin(); ?>


                        <div>
                            <div style="float:left;height: 34px;padding: 6px 12px;">
                                <?= Yii::t('app', 'Language') ?>：
                            </div>
                            <div style="float:left;">
                                <?php if ($model->language == -1) : ?>
                                    <?= $form->field($solution, 'language', ['options' => ['style' => 'margin: 0']])
                                        ->dropDownList($solution::getLanguageList(), ['style' => 'width: auto'])->label(false) ?>
                                <?php else : ?>

                                    <?= $form->field($solution, 'language', ['options' => ['style' => 'margin: 0']])
                                        ->dropDownList($solution::getLanguageList(), ['style' => 'width: auto', 'disabled' => "disabled"])->label(false) ?>
                                <?php endif; ?>

                            </div>

                            <div style="float:right;">
                                <select id="solution-theme" class="form-control" name="solution-theme" style="width: auto" aria-required="true">
                                    <option value="one-dark" <?php if ($theme == "one-dark") echo "selected=''"; ?>>one-dark</option>
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
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-3 problem-info">
            <div class="panel panel-default">
            <div class="content-header text-center">题目参数</div>
                <!-- Table -->
                <table class="table">
                    <tbody>
                        <tr>
                            <td><?= Yii::t('app', 'Time Limit') ?></td>
                            <td>
                            <i class="fa fa-clock-o"></i> <?= Yii::t('app', '{t, plural, =1{# second} other{# seconds}}', ['t' => intval($problem['time_limit'])]); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?= Yii::t('app', 'Memory Limit') ?></td>
                            <td><i class="fa fa-save"></i> <?= $problem['memory_limit'] ?> MB</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="btn-group btn-group-justified">
                <div class="btn-group">
                    <a class="btn btn-success" href="#submit-code">
                        <span class="fa fa-plus"></span> <?= Yii::t('app', 'Submit') ?>
                    </a>
                </div>
                <?php if (!empty($problem['solution']) && Yii::$app->setting->get('isEnableShowSolution') && $model->show_solution) {
                    
                    $bShow = (isset($loginUserProblemSolvingStatus[$problem['id']]) && $loginUserProblemSolvingStatus[$problem['id']] == \app\models\Solution::OJ_AC);
                    if ($bShow) {
                        echo '<div class="btn-group">';
                        echo Html::a(
                            '<i class="fa fa-dropbox"></i> ' . Yii::t('app', '题解'),
                            ['/problem/solution', 'id' => $problem['id']],
                            ['class' => 'btn btn-default']
                        );
                        echo "</div>";
                    } 
                }
            ?>
            </div>

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
                                        // OI 比赛过程中结果不可见
                                        $bIOContesting = false;
                                        if ($model->type == \app\models\Contest::TYPE_OI && !$model->isContestEnd()) {
                                            $waitingHtmlDom = 'waiting="false"';
                                            $loadingImg = "";
                                            $sub['result'] = 0;
                                            $bIOContesting = true;
                                        }
                                        $innerHtml =  'data-verdict="' . $sub['result'] . '" data-submissionid="' . $sub['id'] . '" ' . $waitingHtmlDom;
                                        if ($sub['result'] == Solution::OJ_AC) {
                                            $span = '<span class="text-success"' . $innerHtml . '>' . Solution::getResultList($sub['result']) . '</span>';
                                            if ($solution->canViewResult()) {
                                                echo Html::a(
                                                    $span,
                                                    ['/solution/result', 'id' => $sub['id']],
                                                    ['onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                                                );
                                            }else{
                                                echo $span;
                                            }
                                        } else {
                                            $span = '<span class="text-danger" ' . $innerHtml . '>' . Solution::getResultList($sub['result']) . $loadingImg . '</span>';
                                            if ($solution->canViewResult() && !$bIOContesting) {
                                                echo Html::a(
                                                    $span,
                                                    ['/solution/result', 'id' => $sub['id']],
                                                    ['onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                                                );
                                            }else{
                                                echo $span;
                                            }
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
                </div>
            <?php endif; ?>
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
    $.ajaxSettings.async = false;
    $.get({
        url: "{$url}?id=" + submission.attr('data-submissionid'),
        success: function(data) {
            var obj = JSON.parse(data);
            submission.attr("waiting", obj.waiting);
            submission.text(obj.result);
            submission.attr("class", obj.css);
            if (obj.waiting === "true") {
                submission.append('<img src="{$loadingImgUrl}" alt="loading">');
            }
        }
    });
}
var waitingCount = $("span[waiting=true]").length;
if (waitingCount > 0) {
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
        if (interval && waitingCount === 0) {
            clearInterval(interval);
            interval = null;
        }
    }
    interval = setInterval(testWaitingsDone, 1000);
}
$("#solution-theme").on("change", function () {
    var sel_theme = document.getElementById("solution-theme").value;
    editor.setOption("theme", sel_theme);
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
