<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Problem;
use yii\bootstrap\Nav;
use justinvoelker\tagging\TaggingWidget;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProblemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $solvedProblem array */
\yii\bootstrap\BootstrapPluginAsset::register($this);
$this->title = Yii::t('app', 'Problems');
$title_str = '标题 <span class="float-right">'.Html::a(Html::encode("显示所有"), ['/problem/select'], ['class' => 'label label-normal']).'</span>';
$label_i = 1;
$cssString = "body{background-color: #fff;overflow-x:hidden;}";  
$this->registerCss($cssString);
?>

<?= Html::beginForm('', 'post') ?>
<div sytle="padding: 25px 15px 20px; background-color: #FFF;">
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
                        <?= Html::textInput('tag', '', ['class' => 'form-control', 'placeholder' => '标签']) ?>
                        <span class="input-group-btn">
                            <?= Html::submitButton('<span class="fa fa-search"></span>', ['class' => 'btn btn-primary']) ?>
                        </span>
                    </div>

                    <p></p>
                    <?= Html::endForm() ?>
                    <?= TaggingWidget::widget([
                        'items' => $tags,
                        'url' => ['/problem/select'],
                        'format' => 'ul',
                        'urlParam' => 'tag',
                        'linkOptions' => ['class' => 'label label-normal']
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

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
                'options' => ['id' => 'select_grid'],
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'name' => 'id',
                        'contentOptions' => ['style' => 'text-align:center;'],
                        'headerOptions' => ['style' => 'text-align:center;'],
                    ],
                    [
                        'attribute' => 'id',
                        'value' => function ($model, $key, $index, $column) {              
                            if ($model->status == \app\models\Problem::STATUS_HIDDEN)
                                return Html::a($model->id, ['problem/view', 'id' => $key],['class'=>'text-gray']);
                            else if ($model->status == \app\models\Problem::STATUS_PRIVATE)
                                return Html::a($model->id, ['problem/view', 'id' => $key],['class'=>'text-vip']);
                            else
                                return Html::a($model->id, ['problem/view', 'id' => $key]);
                        },
                        'contentOptions' => ['style' => 'text-align:center;'],
                        'headerOptions' => ['style' => 'text-align:center;'],
                        'format' => 'raw'
                    ],                
                    [

                        'attribute' => 'title',
                        'header' => $title_str,
                        'value' => function ($model, $key, $index, $column) {
                            global $label_i;
                            if($model->status==Problem::STATUS_PRIVATE)
                                $res = Html::a(Html::encode($model->title), ['/problem/view','id' => $key],['class'=>'text-vip','target'=>'_blank']);
                            else
                                $res = Html::a(Html::encode($model->title), ['/problem/view', 'id' => $key],['class'=>'text-dark','target'=>'_blank']);   

                            $tags = !empty($model->tags) ? explode(',', $model->tags) : [];
                            $tagsCount = count($tags);
                            if ($tagsCount > 0) {
                                $res .= '<span class="problem-list-tags">';

                                foreach ((array)$tags as $tag) {
                                    $label = Problem::getColorLabel($label_i);
                                    $label_i = $label_i + 1;
                                    $res .= Html::a(Html::encode($tag), [
                                        '/problem/select', 'tag' => $tag
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
                    ]
                ],   
            ]); ?>
        </div>
    </div>
</div>