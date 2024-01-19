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

if (!Yii::$app->user->isGuest) {
    $solution->language = Yii::$app->user->identity->language;
    $solution->created_by = Yii::$app->user->id;
}
if (isset($_COOKIE['theme']))
    $theme = $_COOKIE['theme'];
else
    $theme = "one-dark";
    
$label_i = 0;
$model->setSamples();

$loadingImgUrl = Yii::getAlias('@web/images/loading.gif');
$previousProblemID = $model->getPreviousProblemID();
$nextProblemID = $model->getNextProblemID();
?>
<div class="row">

    <?php if ($this->beginCache('problem-' . $model->id)) : ?>
        <div class="col-md-9 problem-view">

            <div class="text-center content-title"><?= Html::encode($this->title) ?>
                <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == User::ROLE_ADMIN) {
                    echo Html::a('<span class="fa fa-edit"></span> ',
                    ['/admin/problem/update', 'id' => $model->id],['class' => 'btn btn-link','target'=>'_blank']);
                    }
                ?>
            </div>

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
        <?php $this->endCache(); ?>
    <?php endif; ?>
    <div class="col-md-3 problem-info">
        <div class="panel panel-default">
            <div class="content-header text-center">题目参数</div>
            <!-- Table -->
            <table class="table">
                <tbody>
                    <tr>
                        <td><?= Yii::t('app', 'Time Limit') ?></td>
                        <td>
                            <i class="fa fa-clock-o"></i> <?= Yii::t('app', '{t, plural, =1{# second} other{# seconds}}', ['t' => intval($model->time_limit)]); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', 'Memory Limit') ?></td>
                        <td><i class="fa fa-microchip"></i> <?= $model->memory_limit ?> MB</td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', '提交次数') ?></td>
                        <td><i class="fa fa-share-square-o"></i> <?= $model->submit ?></td>
                    </tr>
                    <tr>
                        <td><?= Yii::t('app', '通过次数') ?></td>
                        <td><i class="fa fa-check-square-o"></i> <?= $model->accepted ?></td>
                    </tr>

                </tbody>
            </table>
        </div>
        <div class="btn-group btn-group-justified">
            <div class="btn-group">
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
                        <div class="row">
                            <div class="col-md-4 col-md-offset-4"><?= Html::submitButton('<span class="fa fa-send"></span> ' . Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?></div>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                <?php endif; ?>
                <?php Modal::end(); ?>
            </div>


            <?php if (Yii::$app->setting->get('isDiscuss')) : ?>
                <div class="btn-group">
                    <?= Html::a(
                        '<span class="fa fa-comment"></span> ' . Yii::t('app', 'Discuss'),
                        ['/problem/discuss', 'id' => $model->id],
                        ['class' => 'btn btn-default']
                    )
                    ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($model->solution)) {
                
                $bShow  = false;
                if (Yii::$app->setting->get('isEnableShowSolution')==1 && ($model->show_solution || $model->isSolved())){
                    $bShow  = true;  
                } else if( !Yii::$app->user->isGuest && ( (Yii::$app->setting->get('isEnableShowSolution')==2 && Yii::$app->user->identity->role >= User::ROLE_TEACHER) || (Yii::$app->setting->get('isEnableShowSolution')==3 && Yii::$app->user->identity->role == User::ROLE_ADMIN)) ) {
                    $bShow  = true;  
                }
                if ($bShow) {
                    echo '<div class="btn-group">';
                    echo Html::a(
                        '<i class="fa fa-dropbox"></i> ' . Yii::t('app', '题解'),
                        ['/problem/solution', 'id' => $model->id],
                        ['class' => 'btn btn-default','title' => '查看源码', 'onclick' => 'return false', 'data-click' => "solution_info", 'data-pjax' => 0]
                    );
                    echo "</div>";
                }
            }
            ?>
            <div class="btn-group">
                <?= Html::a(
                    '<span class="fa fa-signal"></span> ' . Yii::t('app', 'Stats'),
                    ['/problem/statistics', 'id' => $model->id],
                    ['class' => 'btn btn-default']
                ) ?>
            </div>
        </div>

        <hr />
        <div class="btn-group btn-group-justified">
            <div class="btn-group">
                <?= Html::a(
                    '<span class="fa fa-arrow-left"></span> 上一题',
                    $previousProblemID ? ['/problem/view', 'id' => $previousProblemID, 'view' => 'classic'] : 'javascript:void(0);',
                    ['class' => 'btn btn-default', 'disabled' => !$previousProblemID]
                ) ?>
            </div>
            <div class="btn-group">
                <?= Html::a(
                    '下一题 <span class="fa fa-arrow-right"></span>',
                    $nextProblemID ? ['/problem/view', 'id' => $nextProblemID, 'view' => 'classic'] : 'javascript:void(0);',
                    ['class' => 'btn btn-default',  'disabled' => !$nextProblemID]
                ) ?>
            </div>
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
                                    $innerHtml =  'data-verdict="' . $sub['result'] . '" data-submissionid="' . $sub['id'] . '" ' . $waitingHtmlDom;
                                    if ($sub['result'] == Solution::OJ_AC) {
                                        $span = '<span class="text-success"' . $innerHtml . '>' . Solution::getResultList($sub['result']) . '</span>';
                                    } else {
                                        $span = '<span class="text-muted" ' . $innerHtml . '>' . Solution::getResultList($sub['result']) . $loadingImg . '</span>';
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
            //console.log(obj.result+":"+obj.css);
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