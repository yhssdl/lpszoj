<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use app\models\Contest;
use app\models\User;
/* @var $this yii\web\View */
/* @var $model app\models\Contest */
/* @var $searchModel app\models\SolutionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $data array */

$this->title = $model->title;
$this->params['model'] = $model;
$problems = $model->problems;

$nav = [];
$nav[''] = Yii::t('app', 'Please select');
foreach ($problems as $key => $p) {
    $nav[$p['problem_id']] = 'P' . ($key + 1) . '-' . $p['title'];
}
$userInContest = $model->isUserInContest();
$isContestEnd = $model->isContestEnd();
?>
<div class="solution-index" style="margin-top: 20px">
    <?php if ($model->isScoreboardFrozen()) : ?>
        <p class="text-center">现已是封榜状态，榜单将不再实时更新，只显示封榜前的提交及您个人的所有提交记录。</p>
    <?php endif; ?>
    <?php Pjax::begin() ?>
    <?php if ($model->type != Contest::TYPE_OI || $isContestEnd) : ?>
        <?= $this->render('_status_search', ['model' => $searchModel, 'nav' => $nav, 'contest_id' => $model->id]); ?><br>
    <?php endif; ?>

    <?= GridView::widget([
        'layout' => '{items}{pager}',
        'pager' => [
            'firstPageLabel' => Yii::t('app', 'First'),
            'prevPageLabel' => '« ',
            'nextPageLabel' => '» ',
            'lastPageLabel' => Yii::t('app', 'Last'),
            'maxButtonCount' => 10
        ],
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            return ['class' => 'animate__animated animate__fadeInUp'];
        },
        'options' => ['class' => 'table-responsive'],
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'columns' => [
            [
                'attribute' => 'id',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->id, ['/solution/detail', 'id' => $model->id], ['target' => '_blank']);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'who',
                'value' => function ($model, $key, $index, $column) {
                    if (isset($model->user)) {
                        return Html::a($model->user->colorname, ['/user/view', 'id' => $model->created_by]);
                    }
                },
                'format' => 'raw'
            ],
            [
                'label' => Yii::t('app', 'Problem'),
                'value' => function ($model, $key, $index, $column) {
                    $res = $model->getProblemInContest();
                    if (!isset($model->problem)) {
                        return null;
                    }
                    if (!isset($res->num)) {
                        return $model->problem->title;
                    }
                    return Html::a(
                        'P' . ($res->num + 1) . ' - ' . $model->problem->title,
                        ['/contest/problem', 'id' => $res->contest_id, 'pid' => $res->num]
                    );
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'result',
                'value' => function ($solution, $key, $index, $column) use ($model, $userInContest, $isContestEnd) {
                    // OI 比赛模式未结束时不返回具体结果
                    if ($model->type == Contest::TYPE_OI && !$isContestEnd) {
                        return Yii::t('app', 'Pending');
                    }

                    if ($solution->canViewResult()) {
                        return Html::a(
                            $solution->getResult(),
                            'javaScript:void(0);',
                            ['onclick' => 'solution_info_click(this)', 'data-url' => '/solution/result?id='.$solution->id]
                        );
                    } else {
                        return $solution->getResult();
                    }
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            [
                'attribute' => 'score',
                'visible' => $model->type == Contest::TYPE_IOI || $model->type == Contest::TYPE_HOMEWORK ||
                    ($model->type == Contest::TYPE_OI && $isContestEnd),
                'enableSorting' => false
            ],
            [
                'attribute' => 'time',
                'value' => function ($solution, $key, $index, $column) use ($model, $isContestEnd) {
                    // OI 比赛模式未结束时不返回具体结果
                    if ($model->type == \app\models\Contest::TYPE_OI && !$isContestEnd) {
                        return "－";
                    }
                    return $solution->time  . ' ' . Yii::t('app', 'MS');
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            [
                'attribute' => 'memory',
                'value' => function ($solution, $key, $index, $column) use ($model, $isContestEnd) {
                    // OI 比赛模式未结束时不返回具体结果
                    if ($model->type == \app\models\Contest::TYPE_OI && !$isContestEnd) {
                        return "－";
                    }
                    return $solution->memory . ' KB';
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            [
                'attribute' => 'language',
                'value' => function ($solution, $key, $index, $column) use ($model, $isContestEnd) {
                    if ($solution->canViewSource()) {
                        return Html::a(
                            $solution->getLang(),
                            'javaScript:void(0);',
                            ['onclick' => 'solution_info_click(this)', 'data-url' => '/solution/source?id='.$solution->id]
                        );
                    } else {
                        return $solution->getLang();
                    }
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            [
                'attribute' => 'code_length',
                'format' => 'raw',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'ip',
                'enableSorting' => false,
                'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model, $key, $index, $column) {
                    return Html::tag('span', Yii::$app->formatter->asRelativeTime($model->created_at), ['title' => $model->created_at]);
                },
                'enableSorting' => false,
                'format' => 'raw'
            ]
        ],
    ]); ?>
    <?php
    $url = \yii\helpers\Url::toRoute(['/solution/verdict']);
    $loadingImgUrl = Yii::getAlias('@web/images/loading.gif');
    $js = <<<EOF
function solution_info_click(obj) {
    url = $(obj).attr('data-url');
    if(url.indexOf("source") !== -1){
        html = "<iframe id='modal-iframe' src='"+url+"' frameborder='0' width='100%' onload='this.style.height = this.contentWindow.document.documentElement.scrollHeight + \"px\"' scrolling='no'></iframe>";
        $('#solution-content').html(html);
        $('#solution-info').modal('show');
    }else{
        $.ajax({
            url: url,
            type:'post',
            error: function(){alert('error');},
            success:function(html){
                $('#solution-content').html(html);
                $('#solution-info').modal('show');
            }
        });
    }
    return false;
}
EOF;
    $this->registerJs($js,View::POS_HEAD);
    $js = <<<EOF
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
EOF;
    $this->registerJs($js);
    ?>
    <?php Pjax::end() ?>
</div>
<?php Modal::begin([
    'options' => ['id' => 'solution-info']
]); ?>
<div id="solution-content">
</div>
<?php Modal::end(); ?>