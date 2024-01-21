<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Solution;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\modules\polygon\models\Problem */
/* @var $solution \app\modules\polygon\models\PolygonStatus */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->title;
$this->params['model'] = $model;
$solution->language = Yii::$app->user->identity->language;
?>

<div class="animate__animated animate__fadeInUp">
    <div class="alert alert-light"><i class="fa fa-info-circle"></i>
        该页面用于给验题人验证题目数据的准确性，验题前需在
        <?= Html::a(Yii::t('app', 'Tests Data'), ['/polygon/problem/tests', 'id' => $model->id]) ?>
        页面中生成标程的标准输出文件。
    </div>

    <?= GridView::widget([
        'layout' => '{items}{summary}{pager}',
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
        'options' => ['class' => 'table-responsive problem-index-list'],
        'columns' => [
            [
                'attribute' => 'id',
                'value' => function ($solution, $key, $index, $column) use ($model) {
                    return Html::a($solution->id, [
                        '/polygon/problem/solution-detail',
                        'id' => $model->id,
                        'sid' => $solution->id
                    ], ['target' => '_blank']);
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            [
                'attribute' => 'who',
                'label' => Yii::t('app', 'Who'),
                'value' => function ($model, $key, $index, $column) {
                    return Html::a(Html::encode($model->user->nickname), ['/user/view', 'id' => $model->created_by]);
                },

                'format' => 'raw'
            ],
            [
                'attribute' => 'result',
                'value' => function ($model, $key, $index, $column) {
                    if (
                        $model->result == Solution::OJ_CE || $model->result == Solution::OJ_WA
                        || $model->result == Solution::OJ_RE
                    ) {
                        return Html::a(
                            $model->getResult(),
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
                'attribute' => 'time',
                'value' => function ($model, $key, $index, $column) {
                    return $model->time  . ' ' . Yii::t('app', 'MS');
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
                'value' => function ($solution, $key, $index, $column) use ($model) {
                    return Html::a($solution->getLang(), [
                        '/polygon/problem/solution-detail',
                        'id' => $model->id,
                        'sid' => $solution->id
                    ], ['target' => '_blank']);
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

    <?php if (!$model->spj) : ?>
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($solution, 'language')->dropDownList(Solution::getLanguageList())->label(false) ?>

        <?= $form->field($solution, 'source')->widget('app\widgets\codemirror\CodeMirror')->label(false); ?>

        <div class="form-group">
        <div class="row"><div class="col-md-2 col-md-offset-5"><?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?></div></div>
        </div>
        <?php ActiveForm::end(); ?>
    <?php else : ?>
        <div class="alert alert-light"><i class="fa fa-info-circle"></i>
            当前验题功能尚未支持用SPJ来进行验证的题目。
        </div>
    <?php endif; ?>
</div>


<?php
$url = \yii\helpers\Url::toRoute(['/polygon/problem/verdict']);
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
EOF;
$this->registerJs($js);
?>
