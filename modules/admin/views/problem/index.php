<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Problem;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProblemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Problems');
$label_i = 0;

$js = <<<EOT
    function set_cookie(cookie_name,val) {
        var expires = new Date();
        expires.setTime(expires.getTime() + 3650 * 30 * 24 * 60 * 60 * 1000);
        document.cookie = cookie_name + "=" + val + ";expires=" + expires.toGMTString();
    }

    $("#showTags").click(function () {
        set_cookie('showTags',0);
        window.location.reload();
    });
    $("#showSource").click(function () {
        set_cookie('showSource',0);
        window.location.reload();
    });
    $("#showPolygon_id").click(function () {
        set_cookie('showPolygon_id',0);
        window.location.reload();
    });
    $("#showCreated_by").click(function () {
        set_cookie('showCreated_by',0);
        window.location.reload();
    }); 
    $("#showStatus").click(function () {
        set_cookie('showStatus',0);
        window.location.reload();
    });       
    $("#showAll").click(function () {
        set_cookie('showTags',1);
        set_cookie('showSource',1);
        set_cookie('showPolygon_id',1);
        set_cookie('showCreated_by',1);
        set_cookie('showStatus',1);        
        window.location.reload();
    });
EOT;
$this->registerJs($js);   


if(isset($_COOKIE['showTags']))
    $showTags = $_COOKIE['showTags'];
else 
    $showTags = 1;

if(isset($_COOKIE['showSource']))
    $showSource = $_COOKIE['showSource'];
else 
    $showSource = 1;    

if(isset($_COOKIE['showPolygon_id']))
    $showPolygon_id = $_COOKIE['showPolygon_id'];
else 
    $showPolygon_id = 1;    

if(isset($_COOKIE['showCreated_by']))
    $showCreated_by = $_COOKIE['showCreated_by'];
else 
    $showCreated_by = 1;

if(isset($_COOKIE['showStatus']))
    $showStatus = $_COOKIE['showStatus'];
else 
    $showStatus = 1;

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
            <?= Html::a('<span class="fa fa-arrow-circle-o-down"></span> '.Yii::t('app', 'Import Problem'), ['import'], ['class' => 'btn btn-default','title' => '导入HUSTOJ的xml题目', 'data-toggle'=>"tooltip",'data-placement'=>"top" ]) ?>
        </div>

        <div class="btn-group">
            <a id="export" class="btn btn-default" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="将选中题目导出为xml文件"><span class="fa fa-arrow-circle-o-up"></span><?= Yii::t('app', 'Export Problem') ?></a>
        </div>     

        <div class="btn-group">
            <a id="available" class="btn btn-success" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="选中项设为可见，任何用户均能在前台看见题目"><span class="fa fa-eye"></span> 设为普通题</a>
        </div>

        <div class="btn-group">
            <a id="private" class="btn btn-success" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="选中项设为VIP，前台题目列表会出现题目标题，但只有VIP用户才能查看题目信息"><span class="fa fa-key"></span> 设为VIP题</a>
        </div>

        <div class="btn-group">
            <a id="teacher" class="btn btn-success" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="选中项设为教师专用题，前台题目列表会出现题目标题，但只有教师组才能查看题目信息"><span class="fa fa-user"></span> 设为教师题</a>
        </div> 

        <div class="btn-group">
            <a id="train" class="btn btn-success" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="选中项设为训练专用题，前台题目列表会出现题目标题，但只有教师组才能查看题目信息"><span class="fa fa-user"></span> 设为训练题</a>
        </div>         
        
        <div class="btn-group">
            <a id="reserved" class="btn btn-success" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="选中项设为隐藏，题目只能在后台查看"><span class="fa fa-eye-slash"></span> 设为隐藏</a>
        </div>        

        <div class="btn-group">
            <a id="delete" class="btn btn-danger" href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="删除选中题目，不可恢复"><span class="fa fa-trash"></span> 删除</a>
        </div>
    </div>
    <br>

   

    <?= GridView::widget([
        'layout' => '{items}{summary}{pager}',
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
                    if ($model->status == \app\models\Problem::STATUS_HIDDEN)
                        return Html::a($model->id, ['problem/view', 'id' => $key],['class'=>'text-gray','target'=>"_blank"]);
                    else if ($model->status == \app\models\Problem::STATUS_PRIVATE)
                        return Html::a($model->id, ['problem/view', 'id' => $key],['class'=>'text-vip','target'=>"_blank"]);
                    else if ($model->status == \app\models\Problem::STATUS_TEACHER)
                        return Html::a($model->id, ['problem/view', 'id' => $key],['class'=>'text-teacher','target'=>"_blank"]);    
                    else if ($model->status == \app\models\Problem::STATUS_TRAIN)
                        return Html::a($model->id, ['problem/view', 'id' => $key],['class'=>'text-train','target'=>"_blank"]);                                        
                    else
                        return Html::a($model->id, ['problem/view', 'id' => $key],['target'=>"_blank"]);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'title',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->status == \app\models\Problem::STATUS_HIDDEN)
                        return Html::a($model->title, ['problem/update', 'id' => $key],['class'=>'text-gray','target'=>"_blank"]);
                    else if ($model->status == \app\models\Problem::STATUS_PRIVATE)
                        return Html::a($model->title, ['problem/update', 'id' => $key],['class'=>'text-vip','target'=>"_blank"]);
                    else if ($model->status == \app\models\Problem::STATUS_TEACHER)
                        return Html::a($model->title, ['problem/update', 'id' => $key],['class'=>'text-teacher','target'=>"_blank"]);     
                    else if ($model->status == \app\models\Problem::STATUS_TRAIN)
                        return Html::a($model->title, ['problem/update', 'id' => $key],['class'=>'text-train','target'=>"_blank"]);                                      
                    else
                        return Html::a($model->title, ['problem/update', 'id' => $key],['target'=>"_blank"]);
                },
                'enableSorting' => false,
                'contentOptions' => ['style' => 'text-align:left;'],
                'headerOptions' => ['style' => 'text-align:left;'],
                'format' => 'raw'
            ],
            [
                'attribute' => 'tags',
                'header' => "<label  style='cursor: pointer;'>".Html::checkbox('showTags', $showTags, ['id' => 'showTags','style' => 'vertical-align:text-bottom;'])." ".Yii::t('app', 'Tags')."</label>",
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
                'format' => 'raw',
                'visible' =>  $showTags==1,
            ],
            [
                'attribute' => 'source',
                'header' => "<label  style='cursor: pointer;'>".Html::checkbox('showSource', $showSource, ['id' => 'showSource','style' => 'vertical-align:text-bottom;'])." ".Yii::t('app', 'Source')."</label>",
                'visible' =>  $showSource==1,
            ],

            [
                'attribute' => 'status',
                'header' => "<label  style='cursor: pointer;'>".Html::checkbox('showStatus', $showStatus, ['id' => 'showStatus','style' => 'vertical-align:text-bottom;'])." ".Yii::t('app', 'Status')."</label>",
                'value' => function ($model, $key, $index, $column) {
                    if ($model->status == \app\models\Problem::STATUS_VISIBLE) {
                        return "<a>".Yii::t('app', '普通题目')."</a>";
                    } else if ($model->status == \app\models\Problem::STATUS_PRIVATE) {
                       return "<a class='text-vip'>".Yii::t('app', 'VIP题目')."</a>";
                    } else if ($model->status == \app\models\Problem::STATUS_TEACHER) {
                        return "<a class='text-teacher'>".Yii::t('app', '教师题目')."</a>";
                     }else if ($model->status == \app\models\Problem::STATUS_TRAIN) {
                        return "<a class='text-train'>".Yii::t('app', '训练题目')."</a>";
                     } else {
                        return "<a class='text-gray'>".Yii::t('app', 'Hidden')."</a>";
                        
                    }
                },
                'enableSorting' => false,
                'format' => 'raw',
                'visible' =>  $showStatus==1,
            ],
            [
                'attribute' => 'created_by',
                'header' => "<label  style='cursor: pointer;'>".Html::checkbox('showCreated_by', $showCreated_by, ['id' => 'showCreated_by','style' => 'vertical-align:text-bottom;'])." ".Yii::t('app', 'Created By')."</label>",
                'value' => function ($model, $key, $index, $column) {
                    if ($model->user) {
                        return Html::a(Html::encode($model->user->nickname), ['/user/view', 'id' => $model->user->id]);
                    }
                    return '';
                },
                'enableSorting' => false,
                'format' => 'raw',
                'visible' =>  $showCreated_by==1,
            ],
            [
                'attribute' => 'polygon_id',
                'header' => "<label  style='cursor: pointer;'>".Html::checkbox('showPolygon_id', $showPolygon_id, ['id' => 'showPolygon_id','style' => 'vertical-align:text-bottom;'])." ".Yii::t('app', 'Polygon Id')."</label>",
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->polygon_problem_id, ['/polygon/problem/view', 'id' => $model->polygon_problem_id]);
                },
                'enableSorting' => false,
                'format' => 'raw',
                'visible' => Yii::$app->setting->get('isEnablePolygon') &&  $showPolygon_id==1,
            ],
            ['class' => 'yii\grid\ActionColumn',
            'header' => "<label  style='cursor: pointer;'>".Html::checkbox('showAll', 0, ['id' => 'showAll','style' => 'vertical-align:text-bottom;'])." ".Yii::t('app', 'Show all')."</label>",
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
    $("#teacher").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
           url: "' . \yii\helpers\Url::to(['/admin/problem/index', 'action' => \app\models\Problem::STATUS_TEACHER]) . '", 
           dataType: \'json\',
           data: {keylist: keys}
        });
    }); 
    $("#train").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
           url: "' . \yii\helpers\Url::to(['/admin/problem/index', 'action' => \app\models\Problem::STATUS_TRAIN]) . '", 
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
    $("#export").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        if(keys=="") {
            alert("请选中题目后再进行导出。");
            return;
        }
        if (confirm("确定要将选中的题目导出到xml格式的文件吗？")) {
            var url = "/admin/problem/downxml?keylist=" + keys;
            window.open(url);
        }
    });    
    ');
    ?>
</div>