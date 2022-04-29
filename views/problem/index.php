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
?>
<div class="row">

    <?php Pjax::begin(); ?>
    <div class="col-md-9">
        <?= GridView::widget([
            'layout' => '{pager}{items}{pager}',
            'pager' =>[
                'firstPageLabel' => '首页',
                'prevPageLabel' => '« ',
                'nextPageLabel' => '» ',
                'lastPageLabel' => '尾页',
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
                        $solve = '';
                        if (isset($solvedProblem[$model->id])) {
                            $solve = '<span class="glyphicon glyphicon-ok text-success" style="float:left"></span>';
                        }
                        return $solve . Html::a($model->id, ['/problem/view', 'id' => $key]);
                    },
                    'format' => 'raw',
                    'options' => ['width' => '100px']
                ],
                [
                    'attribute' => 'title',
                    'value' => function ($model, $key, $index, $column) {
                        if($model->status==Problem::STATUS_PRIVATE)
                        	$res = Html::a(Html::encode($model->title), ['/problem/view','id' => $key],['class'=>'vip' ]);
                      	else
                        	$res = Html::a(Html::encode($model->title), ['/problem/view', 'id' => $key]);                 
                        $tags = !empty($model->tags) ? explode(',', $model->tags) : [];
                        $tagsCount = count($tags);
                        if ($tagsCount > 0) {
                            $res .= '<span class="problem-list-tags">';
                            foreach((array)$tags as $tag) {
                                $res .= Html::a(Html::encode($tag) , ['/problem/index', 'tag' => $tag
                            ],$options = ['class' => 'label label-default']);
                            }
                            $res .= '</span>';
                        }
                        return $res;
                    },
                    'format' => 'raw',
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
                    'options' => ['width' => '100px']
                ]
            ],   
        ]); ?>
    </div>
    <div class="col-md-3">
        <div class="panel panel-default animate__animated animate__fadeInUp">
            <div class="panel-body">
                <?= Html::beginForm('', 'post', ['class' => 'form-inline']) ?>
                <div class="input-group">
                    <?= Html::label(Yii::t('app', 'Search'), 'q', ['class' => 'sr-only']) ?>
                    <?= Html::textInput('q', '', ['class' => 'form-control', 'placeholder' => '输入 ID 或标题或来源']) ?>
                    <span class="input-group-btn">
                    <?= Html::submitButton('<span class="glyphicon glyphicon-search"></span>', ['class' => 'btn btn-default']) ?>
                    </span>
                </div>
                <?= Html::endForm() ?>
            </div>
        </div>

        <div class="panel panel-default animate__animated animate__fadeInUp">
            <div class="panel-heading"><?= Yii::t('app', 'Tags') ?></div>
            <div class="panel-body">
                <?= TaggingWidget::widget([
                    'items' => $tags,
                    'url' => ['/problem/index'],
                    'format' => 'ul',
                    'urlParam' => 'tag',
                    'listOptions' => ['class' => 'tag-group'],
                    'liOptions' => ['class' => 'tag-group-item']
                ]) ?>
            </div>
        </div>
    </div>
    <?php Pjax::end(); ?>
</div>
