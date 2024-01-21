<?php

use app\models\Solution;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\bootstrap\Nav;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Contest */
/* @var $solution app\models\Solution */
/* @var $problem array */
/* @var $submissions array */

$this->title = Html::encode($model->title) . ' - ' . ($problem['title'] ?? null);
$this->params['model'] = $model;

$this->registerJsFile(Yii::getAlias('@web/js/splitter.min.js'));
$this->registerJs("Split(['.problem-left', '.problem-right'], {sizes: [60, 40],});");
$this->registerCss("
    body {
        overflow: hidden;
    }
    .wrap {
        display: flex;
        flex-direction: column;
        padding: 0;
        margin: 0;
    }

    .container{
        width:100%;
    }

    .wrap > .container {
        display: flex;
        flex-direction: column;
        flex: 1 1 0;
        overflow: hidden;
    }

    .flex-col{
        display: flex;
        flex-direction: column;
        flex: 1 1 0;
        overflow: hidden;        
    }

    .flex-row{
        display: flex;
        flex-direction: row;
        flex: 1 1 0;
        overflow: hidden;        
    }    
    
    .flex-title{
        align-items:center;
        padding:0 8px;
        display: flex;
        justify-content:space-between;
    }

    .pagination{
        margin:12px 0;
    }

    .container > .main_body ,.contest-view{
        display: flex;
        flex-direction: column;
        flex: 1 1 0;
        overflow: hidden;
    }

    .contest-view{
        margin-top:-15px;
    }

    .contest-info > .progress {
        margin-bottom:10px;
    }


    #p0{
        height: 100%;
        display: flex;
        flex-direction: column;
        flex: 1 1 0;
    }
    .problem-container {
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

    .problem-description {
        overflow-x: hidden;
        height: 100%;
    }

    .problem-right > .problem-editor {
        padding:12px 0 0 12px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .problem-right .problem-editor .code-input {
        height: 100%;
        overflow: hidden;
    }

    .problem-footer > .flex-row{
        align-items:center;
        padding-top:12px;
        justify-content:space-between;
        flex-wrap:wrap;
    }

    .modal-body .table td{
        border-top:unset;
        border-bottom:1px solid #ddd;
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
    .pagination > li > a {
        padding:6px 6px;
    }
    .content-title{
        margin:0;
    }
    .content-title .btn{
        padding:6px 3px;
    }
    .footer{
        display: none;
    }
");


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
<div class="main-container flex-col">
    <div class="problem-container flex-col">
        <div class="problem-splitter flex-row">
            <div class="problem-left flex-col">
                <div class="flex-title">
                    <div class="content-title text-left"><?= Html::encode('P' . (1 + $problem['num']). '. ' . $problem['title']) ?>&nbsp;&nbsp;
                        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == User::ROLE_ADMIN) {
                            echo Html::a('<span class="fa fa-edit"></span>',
                            ['/admin/problem/update', 'id' => $problem['id']],['class' => 'btn btn-link','target'=>'_blank','data-pjax' => '0']);
                            }
                        ?>
                        <div class="btn btn-link" style="cursor:unset">
                            <i class="fa fa-clock-o" title="<?= Yii::t('app', 'Time Limit') ?>: <?= intval($problem['time_limit']) ?> 秒"></i>
                        </div>

                        <div class="btn btn-link" style="cursor:unset">
                            <i class="fa fa-microchip" title="<?= Yii::t('app', 'Memory Limit') ?>: <?=$problem['memory_limit'] ?> MB"></i>
                        </div>
                    </div>
                    <div class="text-right">
                        <ul id="w0" class="pagination nav">
                            <?php 
                                foreach ($problems as $key => $p) {
                                    $p = $key + 1;
                                    if($problem['num']== $key)
                                        echo "<li class='active'><a href='problem?id=$model->id&amp;pid=$key'>P$p</a></li>";
                                    else
                                        echo "<li><a href='problem?id=$model->id&amp;pid=$key'>P$p</a></li>";
                                }
                            ?>
                        </ul>
                    </div>
                </div>
                
                <div class="problem-description">
        

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
                </div>
            </div>

            <div class="problem-right">
                <?php if ($model->isContestEnd() && time() < strtotime($model->end_time) + 5 * 60 && !Yii::$app->user->isGuest && $model->isUserInContest() && !Yii::$app->user->identity->isAdmin() && !Yii::$app->user->identity->isVip()) : ?>
                    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 比赛已结束，比赛结束五分钟后开放提交。</div>
                <?php elseif ($model->isContestEnd() && !Yii::$app->user->isGuest && !$model->isUserInContest() && !Yii::$app->user->identity->isAdmin() && !Yii::$app->user->identity->isVip()) : ?>

                    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 比赛注册已关闭，你没有提交评测的权限。阅读比赛公告或联系管理员以了解如何补题。</div>

                <?php else : ?>
                    <?php if (Yii::$app->user->isGuest) : ?>
                        <?= app\widgets\login\Login::widget(); ?>
                    <?php else : ?>
                        <?php $form = ActiveForm::begin(['options' => ['class' => 'problem-editor']]);  ?>
                        <div class="flex-title">
                            <div >
                                <div style="display: inline-block;"><?= Yii::t('app', 'Language') ?>：</div>
                                <?php if ($model->language == -1) : ?>
                                    <?= $form->field($solution, 'language', ['options' => ['style' => 'margin: 0;display: inline-block;']])
                                        ->dropDownList($solution::getLanguageList(), ['style' => 'width: auto'])->label(false) ?>
                                <?php else : ?>
                                    <?= $form->field($solution, 'language', ['options' => ['style' => 'margin: 0;display: inline-block;']])
                                        ->dropDownList($solution::getLanguageList(), ['style' => 'width: auto', 'disabled' => "disabled"])->label(false) ?>
                                <?php endif; ?>
                            </div>

                            <div>
                                <div style="display: inline-block;"><?= Yii::t('app', 'Theme') ?>：</div>
                                <div style="margin: 0;display: inline-block;">
                                    <select id="solution-theme" class="form-control" name="solution-theme" style="width: auto" aria-required="true">
                                        <option value="one-dark" <?php if ($theme == "one-dark") echo "selected=''"; ?>>one-dark</option>
                                        <option value="solarized" <?php if ($theme == "solarized") echo "selected=''"; ?>>solarized</option>
                                        <option value="material" <?php if ($theme == "material") echo "selected=''"; ?>>material</option>
                                        <option value="monokai" <?php if ($theme == "monokai") echo "selected=''"; ?>>monokai</option>
                                    </select>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                        </div>
                        <?= $form->field($solution, 'source', ['options' => ['class' => 'code-input']])
                            ->widget('app\widgets\codemirror\CodeMirror')->label(false); ?>

                        <div class="problem-footer">
                            <div class="flex-row">
                                <div class="btn-group">
                                <?php
                                    echo Html::submitButton('<span class="fa fa-send"></span> ' . Yii::t('app', 'Submit'), ['class' => 'btn btn-success']);

                                    if (!empty($problem['solution'])) {
                                        $bShow  = false;
                                        if (Yii::$app->setting->get('isEnableShowSolution')==1 && ($problem['show_solution']==1 || (isset($loginUserProblemSolvingStatus[$problem['id']]) && $loginUserProblemSolvingStatus[$problem['id']] == \app\models\Solution::OJ_AC))){
                                            $bShow  = true;  
                                        } else if( !Yii::$app->user->isGuest && ( (Yii::$app->setting->get('isAdminShowSolution')==1 && Yii::$app->user->identity->role >= User::ROLE_TEACHER) || (Yii::$app->setting->get('isAdminShowSolution')==2 && Yii::$app->user->identity->role == User::ROLE_ADMIN)) ) {
                                            $bShow  = true;  
                                        }
                                        if ($bShow) {
                                            echo '<div class="btn-group">';
                                            echo Html::a(
                                                '<i class="fa fa-dropbox"></i> ' . Yii::t('app', '题解'),
                                                ['/problem/solution', 'id' => $problem['id']],
                                                ['class' => 'btn btn-default','title' => '查看源码', 'onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                                            );
                                            echo "</div>";
                                        } 
                                    }
                                ?>
                                <?php if (!empty($submissions)) : ?>
                                        <?php Modal::begin([
                                            'header' => $problem['title'],
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
                                                            } else {
                                                                $span = '<span class="text-danger" ' . $innerHtml . '>' . Solution::getResultList($sub['result']) . $loadingImg . '</span>';
                                                            }
                                                            if ($solution->canViewResult() && !$bIOContesting) {
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
                                    <?php endif; ?>
                                </div>
                            

                                <?php if (!empty($submissions)) : ?>
                                    <div style="padding:6px 12px">
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
                                            } else {
                                                $span = '<span class="text-danger" ' . $innerHtml . '>' . Solution::getResultList($sub['result']) . $loadingImg . '</span>';  
                                            }
                                            if ($solution->canViewResult() && !$bIOContesting) {
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
                        </div>
                        <?php ActiveForm::end(); ?>
                    <?php endif; ?>
                <?php endif; ?>
            
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
    url = $(this).attr('href');
    if(url.indexOf("result") == -1){
        html = "<iframe id='modal-iframe' src='"+url+"' frameborder='0' width='100%' scrolling='no'></iframe>";
        $('#solution-content').html(html);
        $('#solution-info').modal('show');
    }else{
        $.ajax({
            url: $(this).attr('href'),
            type:'post',
            error: function(){alert('error');},
            success:function(html){
                $('#solution-content').html(html);
                $('#solution-info').modal('show');
            }
        });
    }
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
