<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
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
EOT;
$this->registerJs($js);
?>

<?= Html::beginForm('', 'post') ?>
<div class="row">
        <div class="col-md-9" style="margin-bottom: 1rem;">
            <?= Html::textInput('q', '', ['class' => 'form-control', 'placeholder' => '题号 / 标题 / 来源']) ?>
        </div>

        <div class="col-md-3" style="margin-bottom: 1rem;">
            <div class="btn-group btn-group-justified search-submit">
                <div class="btn-group">
                    <?= Html::submitButton('<span class="glyphicon glyphicon-search"></span> '.Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
                </div>
                <div class="btn-group">
                    <button class="btn btn-default" type="button" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-tags"></span> <?= Yii::t('app', 'Tags') ?></button>
                </div>
                <div class="btn-group">
                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span>&nbsp;'.Yii::t('app', 'Question'), ['/polygon'], ['class' => 'btn btn-default'])?>
                </div>
            </div>
        </div>
</div>
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
                        <?= Html::submitButton('<span class="glyphicon glyphicon-search"></span>', ['class' => 'btn btn-primary']) ?>
                    </span>

                </div>

                <p></p>
                <?= Html::endForm() ?>
                <?= TaggingWidget::widget([
                    'items' => $tags,
                    'url' => ['/problem/index'],
                    'format' => 'ul',
                    'urlParam' => 'tag',
                    'listOptions' => ['style' => 'padding-left:0;'],
                    'liOptions' => ['style' => 'list-style-type: none; display: inline-block; margin-bottom:0.35rem,padding-top: 0.2rem;padding-bottom: 0.2rem;'],
                    'linkOptions' => ['class' => 'label label-warning']
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?php
$title_str = Html::beginForm(['/problem/index', 'page' => $page, 'tag' => $tag], 'get', ['class' => 'toggle-show-contest-standing']);
$title_str .= Yii::t('app', 'Problem').' <span class="float-right">';
$title_str .= Html::checkbox('showTags', $showTags, ['style' => 'vertical-align:middle;']);
$title_str .= ' '.Yii::t('app', 'Show tags').'</span>';
$title_str .= Html::endForm();
$title_str .= '';
?>





<div class="row">

    <?php Pjax::begin(); ?>
    <div class="col-md-12">
        <?= GridView::widget([
            'layout' => '{items}{pager}',
            'pager' =>[
                'firstPageLabel' => Yii::t('app', 'First'),
                'prevPageLabel' => '« ',
                'nextPageLabel' => '» ',
                'lastPageLabel' => Yii::t('app', 'Last'),
                'maxButtonCount' => 15
            ],
            'rowOptions' => function($model, $key, $index, $grid) {
                return ['class' => 'animate__animated animate__fadeInUp'];
            },
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'table-responsive problem-index-list'],
            'columns' => [
                [
                    'attribute' => 'id',
                    'value' => function ($model, $key, $index, $column) use ($solvedProblem) {
                        if (isset($solvedProblem[$model->id])) {
                            return Html::a($model->id, ['/problem/view', 'id' => $key],['class'=>'btn btn-success btn-xs' ]);
                        }
                        return Html::a($model->id, ['/problem/view', 'id' => $key],['class'=>'btn btn-default btn-xs' ]);
                    },
                    'format' => 'raw',
                    'options' => ['width' => '80px']
                ],
                [

                    'attribute' => 'title',
                    'header' => $title_str,
                    'value' => function ($model, $key, $index, $column) use ($showTags) {
                        if($model->status==Problem::STATUS_PRIVATE)
                        	$res = Html::a(Html::encode($model->title), ['/problem/view','id' => $key],['class'=>'text-vip']);
                      	else
                        	$res = Html::a(Html::encode($model->title), ['/problem/view', 'id' => $key],['class'=>'text-dark']);   

                        $tags = !empty($model->tags) ? explode(',', $model->tags) : [];
                        $tagsCount = count($tags);
                        if ($showTags && $tagsCount > 0) {
                            $res .= '<span class="problem-list-tags">';
                            foreach ((array)$tags as $tag) {
                                $res .= Html::a(Html::encode($tag), [
                                    '/problem/index', 'tag' => $tag
                                ], ['class' => 'label label-warning']);
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
    <?php Pjax::end(); ?>
</div>
