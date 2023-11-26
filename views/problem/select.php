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
$title_str = '标题 <span class="float-right">'.Html::a(Html::encode("显示所有"), ['/problem/select'], ['class' => 'label label-normal']).'</span>';
$label_i = 2;
$cssString = "body{overflow-x:hidden;}";  
$this->registerCss($cssString);
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
