<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Problem;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProblemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Problems');
$label_i = 0;
?>
<div class="problem-index">

    <p class="lead">创建、导入和管理题目数据</p>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <br>
    <div class="btn-group btn-group-justified">
        <div class="btn-group">
            <?= Html::a('<span class="fa fa-plus"></span> '.Yii::t('app', 'Create Problem'), ['create'], ['class' => 'btn btn-default','title' => '创建一个新的题目', 'data-toggle'=>"tooltip",'data-placement'=>"top" ]) ?>
        </div>

        <?php  if(Yii::$app->setting->get('isEnablePolygon')): ?>
        <div class="btn-group">
            <?= Html::a('<span class="fa fa-star"></span> '.Yii::t('app', 'Polygon Problem'), ['create-from-polygon'], ['class' => 'btn btn-default','title' => '从Polygon中导入题目', 'data-toggle'=>"tooltip",'data-placement'=>"top" ]) ?>
        </div>
        <?php endif; ?>

        <div class="btn-group">
            <?= Html::a('<span class="fa fa-arrow-circle-o-down"></span> '.Yii::t('app', 'Import Problem'), ['import'], ['class' => 'btn btn-default','title' => '从HUSTOJ导入题目', 'data-toggle'=>"tooltip",'data-placement'=>"top" ]) ?>
        </div>

        <div class="btn-group">
            <a id="available" class="btn btn-success" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="选中项设为可见，任何用户均能在前台看见题目"><span class="fa fa-eye"></span> 设为可见</a>
        </div>

        <div class="btn-group">
            <a id="reserved" class="btn btn-success" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="选中项设为隐藏，题目只能在后台查看"><span class="fa fa-eye-slash"></span> 设为隐藏</a>
        </div>

        <div class="btn-group">
            <a id="private" class="btn btn-success" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="选中项设为VIP，前台题目列表会出现题目标题，但只有VIP用户才能查看题目信息"><span class="fa fa-key"></span> 设为私有</a>
        </div>

        <div class="btn-group">
            <a id="delete" class="btn btn-danger" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="删除选中题目，不可恢复"><span class="fa fa-trash"></span> 删除</a>
        </div>
    </div>
    <br>

   

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
        'tableOptions' => ['class' => 'table table-striped table-bordered table-text-center'],
        'rowOptions' => function($model, $key, $index, $grid) {
            return ['class' => 'animate__animated animate__fadeInUp'];
        },       
        'options' => ['id' => 'grid'],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'id',
            ],
            [
                'attribute' => 'id',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->id, ['problem/view', 'id' => $key]);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'title',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a(Html::encode($model->title), ['problem/view', 'id' => $key]);
                },
                'enableSorting' => false,
                'contentOptions' => ['style' => 'text-align:left;'],
                'headerOptions' => ['style' => 'text-align:left;'],
                'format' => 'raw'
            ],
            [
                'attribute' => 'tags',
                'value' => function ($model, $key, $index, $column) {
                    global $label_i;
                    $tags = !empty($model->tags) ? explode(',', $model->tags) : [];
                    $tagsCount = count($tags);
                    if ($tagsCount > 0) {
                        $res = '<span>';
                        foreach ((array)$tags as $tag) {
                            $label = Problem::getColorLabel($label_i);
                            $label_i = $label_i + 1;
                            $res .= Html::a(Html::encode($tag), [
                                '/problem/index', 'tag' => $tag
                            ], ['class' => $label,'target' => '_blank']);
                            $res .= ' ';
                        }
                        $res .= '</span>';
                        return $res;
                    }
                    return '';  
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'source',
            ],

            [
                'attribute' => 'status',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->status == \app\models\Problem::STATUS_VISIBLE) {
                        return Yii::t('app', 'Visible');
                    } else if ($model->status == \app\models\Problem::STATUS_HIDDEN) {
                        return Yii::t('app', 'Hidden');
                    } else {
                        return Yii::t('app', 'Private');
                    }
                },
                'enableSorting' => false,
                'format' => 'raw',
            ],
            [
                'attribute' => 'created_by',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->user) {
                        return Html::a(Html::encode($model->user->nickname), ['/user/view', 'id' => $model->user->id]);
                    }
                    return '';
                },
                'enableSorting' => false,
                'format' => 'raw',
            ],
            [
                'attribute' => 'polygon_id',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->polygon_problem_id, ['/polygon/problem/view', 'id' => $model->polygon_problem_id]);
                },
                'enableSorting' => false,
                'format' => 'raw',
                'visible' => Yii::$app->setting->get('isEnablePolygon'),
            ],
            ['class' => 'yii\grid\ActionColumn',
            'contentOptions' => ['class'=>'a_just']
            ],
        ],
    ]);
    $this->registerJs('
    $(function () {
      $(\'[data-toggle="tooltip"]\').tooltip()
    })
    $("#available").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
           url: "' . \yii\helpers\Url::to(['/admin/problem/index', 'action' => \app\models\Problem::STATUS_VISIBLE]) . '", 
           dataType: \'json\',
           data: {keylist: keys}
        });
    });
    $("#reserved").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
           url: "' . \yii\helpers\Url::to(['/admin/problem/index', 'action' => \app\models\Problem::STATUS_HIDDEN]) . '", 
           dataType: \'json\',
           data: {keylist: keys}
        });
    });
    $("#private").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
           url: "' . \yii\helpers\Url::to(['/admin/problem/index', 'action' => \app\models\Problem::STATUS_PRIVATE]) . '", 
           dataType: \'json\',
           data: {keylist: keys}
        });
    });
    $("#delete").on("click", function () {
        if (confirm("确定要删除？此操作不可恢复！")) {
            var keys = $("#grid").yiiGridView("getSelectedRows");
            $.post({
               url: "' . \yii\helpers\Url::to(['/admin/problem/index', 'action' => 'delete']) . '", 
               dataType: \'json\',
               data: {keylist: keys}
            });
        }
    });
    ');
    ?>
</div>