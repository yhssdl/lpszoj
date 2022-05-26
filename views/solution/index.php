<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SolutionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Status');
?>
<div class="solution-index">
    <?php Pjax::begin() ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'layout' => '{items}{pager}',
        'pager' =>[
            'firstPageLabel' => Yii::t('app', 'First'),
            'prevPageLabel' => '« ',
            'nextPageLabel' => '» ',
            'lastPageLabel' => Yii::t('app', 'Last'),
            'maxButtonCount' => 10
        ],
        'dataProvider' => $dataProvider,
        'rowOptions' => function($model, $key, $index, $grid) {
            return ['class' => 'animate__animated animate__fadeInUp'];
        },
        'options' => ['class' => 'table-responsive'],
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'columns' => [
            [
                'attribute' => 'id',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->id, ['/solution/detail', 'id' => $model->id], ['target' => '_blank', 'data-pjax' => 0,'class'=>'text-dark']);
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
                'attribute' => 'problem_id',
                'value' => function ($model, $key, $index, $column) {
                    if (isset($model->problem)) {
                        return Html::a($model->problem_id . ' - ' . Html::encode($model->problem->title), ['/problem/view', 'id' => $model->problem_id],['class'=>'text-dark']);
                    }
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            [
                'attribute' => 'result',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->canViewResult()) {
                        return Html::a($model->getResult(),
                            ['/solution/result', 'id' => $model->id],
                            ['onclick' => 'return false', 'data-click' => "solution_info"]
                        );
                    } else {
                        return $model->getResult();
                    }
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            [
                'attribute' => 'score',
                'enableSorting' => false,
                'visible' => Yii::$app->setting->get('oiMode')
            ],
            [
                'attribute' => 'time',
                'value' => function ($model, $key, $index, $column) {
                    return $model->time .' '. Yii::t('app', 'MS');
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            [
                'attribute' => 'memory',
                'value' => function ($model, $key, $index, $column) {
                    return $model->memory . ' KB';
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            [
                'attribute' => 'language',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->canViewSource()) {
                        return Html::a($model->getLang(),
                            ['/solution/source', 'id' => $model->id],
                            ['onclick' => 'return false', 'data-click' => "solution_info",'title'=>Yii::t('app', 'Code Length').":".$model->code_length,'class'=>'text-dark']
                        );
                    } else {
                        return $model->getLang();
                    }
                },
                'enableSorting' => false,
                'format' => 'raw'
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

    <?php Pjax::end() ?>
</div>
<?php Modal::begin([
    'options' => ['id' => 'solution-info']
]); ?>
    <div id="solution-content">
    </div>
<?php Modal::end(); ?>
