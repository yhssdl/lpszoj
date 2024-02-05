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

$this->title = $model->id . ' : ' . $model->title;
$label_i = 0;
$this->registerJsFile(Yii::getAlias('@web/js/splitter.min.js'));
$this->registerJs("Split(['.problem-left', '.problem-right'], { minSize: 200,sizes: [60, 40],});");
$this->registerCss("
@media screen and (min-width: 768px) {
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


    .container > .main_body {
        display: flex;
        flex-direction: column;
        flex: 1 1 0;
        overflow: hidden;
    }

    .main-container {
        height: 100%;
    }

    .problem-container {
        height: 100%;
        background: #fff;
    }

    .problem-footer > .flex-row{
        align-items:center;
        padding-top:12px;
        justify-content:space-between;
        flex-wrap:wrap;
    }

    .flex-title{
        align-items:center;
        padding:0 8px;
        display: flex;
        justify-content:space-between;
    }

    .problem-description {
        overflow-x: hidden;
        height: 100%;
    }

    .problem-right > .problem-editor {
        padding-left:12px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .problem-right .problem-editor .code-input {
        height: 100%;
        overflow: hidden;
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

    .gutter.gutter-horizontal {
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAeCAYAAADkftS9AAAAIklEQVQoU2M4c+bMfxAGAgYYmwGrIIiDjrELjpo5aiZeMwF+yNnOs5KSvgAAAABJRU5ErkJggg==');
    }

    .footer{
        display: none;
    }
}
@media screen and (max-width: 767px) {
    .problem-left,.problem-right{
        width:100% !important;
    }
    .flex-title{
        align-items:center;
        padding:0 8px;
        display: flex;
        justify-content:space-between;
    }
    .flex-row1{
        display: flex;
        flex-direction: row;
        flex: 1 1 0;
        overflow: hidden;   
        justify-content:space-between;    
        align-items:center;
    }    
}
");

if (!Yii::$app->user->isGuest) {
    $solution->language = Yii::$app->user->identity->language;
    $solution->created_by = Yii::$app->user->id;
}

if (isset($_COOKIE['theme']))
    $theme = $_COOKIE['theme'];
else
    $theme = "one-dark";

$model->setSamples();

$loadingImgUrl = Yii::getAlias('@web/images/loading.gif');
$previousProblemID = $model->getPreviousProblemID();
$nextProblemID = $model->getNextProblemID();
?>

<div class="main-container flex-col">
    <div class="problem-container flex-col">
        <div class="problem-splitter  flex-row">
            <div class="problem-left flex-col">
                <div class="row">
                    <div class="col-md-6 col-xs-6">
                        <div class="text-left content-title"><?= Html::encode($this->title) ?>
                            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == User::ROLE_ADMIN) {
                                echo Html::a('<span class="fa fa-edit"></span> ',
                                ['/admin/problem/update', 'id' => $model->id],['class' => 'btn btn-link','target'=>'_blank','title'=>'编辑']);
                                }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-6">
                        <div class="text-right">
                            <div class="btn btn-link" style="cursor:unset">
                                <i class="fa fa-clock-o" title="<?= Yii::t('app', 'Time Limit') ?>: <?= intval($model->time_limit) ?> 秒"></i>
                            </div>

                            <div class="btn btn-link" style="cursor:unset">
                                <i class="fa fa-microchip" title="<?= Yii::t('app', 'Memory Limit') ?>: <?= $model->memory_limit ?> MB"></i>
                            </div>
                            <div class="btn btn-link" style="cursor:unset">
                                <i class="fa fa-share-square-o" title="通过次数: <?= $model->accepted ?>"></i>
                            </div>
                            <div class="btn btn-link">
                                <?= Html::a('<span class="fa fa-tasks"></span> ',
                                        ['/problem/statistics', 'id' => $model->id],
                                        ["title"=>"提交次数: $model->submit"]
                                    ) ?>
                            </div>
                            <div class="btn btn-link">
                                <?= Html::a(
                                    '<i class="fa fa-arrow-left"></i>',
                                    $previousProblemID ? ['/problem/view', 'id' => $previousProblemID] : 'javascript:void(0);',
                                    ['title'=>'上一题','disabled' => !$previousProblemID]
                                ) ?>
                            </div>
                            <div class="btn btn-link">
                            <?= Html::a(
                                '<i class="fa fa-arrow-right"></i>',
                                $nextProblemID ? ['/problem/view', 'id' => $nextProblemID] : 'javascript:void(0);',
                                ['title' => '下一题',  'disabled' => !$nextProblemID]
                            ) ?>
                            </div>                            
                        </div>
                    </div>
                </div>

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
            </div>
            <div class="problem-right">
                <?php $form = ActiveForm::begin(['options' => ['class' => 'problem-editor']]); ?>
                <div class="flex-title">
                    <div>
                        <div style="display: inline-block;"><?= Yii::t('app', 'Language') ?>：</div>
                        <?= $form->field($solution, 'language', ['options' =>['style' => 'margin: 0;display: inline-block;']])
                            ->dropDownList($solution::getLanguageList(), ['style' => 'width: auto'])->label(false) ?>
                    </div>
                    <div style="float:right;">
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
                    <div class="flex-row flex-row1">
                        <?php if (Yii::$app->user->isGuest): ?> 
                            <span><i class=" fa fa-info-circle"></i> 登录以提交代码</span>;
                        <?php else: ?> 
                            <div class="btn-group">
                            <?php
                                echo Html::submitButton('<span class="fa fa-send"></span> ' . Yii::t('app', 'Submit'), ['class' => 'btn btn-success']);

                                if (Yii::$app->setting->get('isDiscuss')) {
                                    echo Html::a(
                                        '<span class="fa fa-comment"></span> ' . Yii::t('app', 'Discuss'),
                                        ['/problem/discuss', 'id' => $model->id],
                                        ['class' => 'btn btn-default']
                                    );
                                }
                                if (!empty($model->solution)) {
                                    $bShow  = false;
                                    if (Yii::$app->setting->get('isEnableShowSolution')==1 && ($model->show_solution || $model->isSolved())){
                                        $bShow  = true;  
                                    } else if( !Yii::$app->user->isGuest && ( (Yii::$app->setting->get('isAdminShowSolution')==1 && Yii::$app->user->identity->role >= User::ROLE_TEACHER) || (Yii::$app->setting->get('isAdminShowSolution')==2 && Yii::$app->user->identity->role == User::ROLE_ADMIN)) ) {
                                        $bShow  = true;  
                                    }
                                    if ($bShow)
                                        echo Html::a(
                                            '<i class="fa fa-file-code-o"></i> ' . Yii::t('app', '题解'),
                                            ['/problem/solution', 'id' => $model->id],
                                            ['class' => 'btn btn-default','title' => '查看源码', 'onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                                        );
                                }
                            ?>
                            <?php if (!empty($submissions)) : ?>
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
                                <?php endif; ?>
                            </div>
                        

                            <?php if (!empty($submissions)) : ?>
                                <div>
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
                        <?php endif; ?>
                    </div>
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
    interval = setInterval(testWaitingsDone, 500);
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