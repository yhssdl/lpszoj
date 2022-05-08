<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Problem;
use justinvoelker\tagging\TaggingWidget;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProblemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $solvedProblem array */

$this->title = Yii::t('app', 'Problems');
$js = <<<EOT
$(".toggle-show-contest-standing input[name='showTags']").change(function () {
    $(".toggle-show-contest-standing").submit();
});
$("#showTags").click(function () {
    var expires = new Date();
    expires.setTime(expires.getTime() + 3650 * 30 * 24 * 60 * 60 * 1000);
    if ($(this).prop("checked")) {
        document.cookie = "showtags=1;expires=" + expires.toGMTString();
    } else {
        document.cookie = "showtags=0;expires=" + expires.toGMTString();
    }
    window.location.reload();
});
EOT;
$this->registerJs($js);

if(isset($_COOKIE['showtags']))
    $showTags = $_COOKIE['showtags'];
else 
    $showTags = 1;
?>

<?= Html::beginForm('', 'post') ?>
<div class="row">
        <div class="col-md-9">
            <?= Html::textInput('q', '', ['class' => 'form-control', 'placeholder' => '题号 / 标题 / 来源']) ?>
        </div>

        <div class="col-md-3">
            <div class="btn-group btn-group-justified search-submit">
                <div class="btn-group">
                    <?= Html::submitButton('<span class="fa fa-search"></span> '.Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                </div>
                <div class="btn-group">
                    <button class="btn btn-default" type="button" data-toggle="modal" data-target="#myModal"><span class="fa fa-tags"></span> <?= Yii::t('app', 'Tags') ?></button>
                </div>
                <?php if(Yii::$app->setting->get('isEnablePolygon')): ?>
                <div class="btn-group">
                    <?= Html::a('<span class="fa fa-plus"></span>&nbsp;'.Yii::t('app', 'Question'), ['/polygon'], ['class' => 'btn btn-default'])?>
                </div>
                <?php endif; ?>
            </div>
        </div>
</div>
<br>
<?= Html::endForm() ?>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px!important">
        <div class="modal-content">
            <div class="modal-header">
                <?= Yii::t('app', 'Tags') ?>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

            </div>
            <div class="modal-body">

                <?= Html::beginForm('', 'post') ?>
                <div class="input-group">
                    <?= Html::textInput('q', '', ['class' => 'form-control', 'placeholder' => '题号 / 标题 / 来源']) ?>
                    <span class="input-group-btn">
                        <?= Html::submitButton('<span class="fa fa-search"></span>', ['class' => 'btn btn-primary']) ?>
                    </span>

                </div>

                <p></p>
                <?= Html::endForm() ?>
                <?= TaggingWidget::widget([
                    'items' => $tags,
                    'url' => ['/problem/index'],
                    'format' => 'ul',
                    'urlParam' => 'tag',
                    'linkOptions' => ['class' => 'label label-normal']
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?php

$title_str = '标题 <span class="float-right">'. Html::checkbox('showTags', $showTags, ['id' => 'showTags','style' => 'vertical-align:middle;']).' 显示标签</span>';
$label_i = 0;
?>

<div class="row">
    <div class="col-md-12">
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
            'options' => ['class' => 'table-responsive problem-index-list'],
            'columns' => [
                [
                    'attribute' => 'id',
                    'value' => function ($model, $key, $index, $column) use ($solvedProblem) {
                        if (isset($solvedProblem[$model->id])) {
                            return Html::a($model->id, ['/problem/view', 'id' => $key],['class'=>'btn btn-success btn-sm' ]);
                        }
                        return Html::a($model->id, ['/problem/view', 'id' => $key],['class'=>'btn btn-default btn-sm' ]);
                    },
                    'format' => 'raw',
                    'options' => ['width' => '80px']
                ],
                [

                    'attribute' => 'title',
                    'header' => $title_str,
                    'value' => function ($model, $key, $index, $column) use ($showTags) {
                        global $label_i;
                        if($model->status==Problem::STATUS_PRIVATE)
                        	$res = Html::a(Html::encode($model->title), ['/problem/view','id' => $key],['class'=>'text-vip']);
                      	else
                        	$res = Html::a(Html::encode($model->title), ['/problem/view', 'id' => $key],['class'=>'text-dark']);   

                        $tags = !empty($model->tags) ? explode(',', $model->tags) : [];
                        $tagsCount = count($tags);
                        if ($showTags && $tagsCount > 0) {
                            $res .= '<span class="problem-list-tags">';

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
                        return $res;
                    },
                    'format' => 'raw',
                    'enableSorting' => false,
                    'options' => ['style' => 'min-width:300px;']
                ],
                [
                    'attribute' => 'solved',
                    'enableSorting' => false,
                    'value' => function ($model, $key, $index, $column) use ($solvedProblem) {
                    if($model->submit==0)
                        $pos = 0;
                    else
                        $pos = round($model->accepted *100 / $model->submit,2);

                    return '<div title="通过率:'.$pos.'%" class="press"><span class="bar" style="width: ' . $pos . '%;">' . Html::a($model->accepted . '/' . $model->submit  , [
                            '/solution/index',
                            'SolutionSearch[problem_id]' => $model->id
                           // 'SolutionSearch[result]' => 0
                        ], ['data-pjax' => 0]) .'</span></div>';
                    },
                    'format' => 'raw',
                    'options' => ['width' => '160px']
                ]
            ],   
        ]); ?>
    </div>
</div>
