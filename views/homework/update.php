<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use app\models\Contest;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\models\Homework */

$this->title = Html::encode($model->title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Group'), 'url' => ['/group/index']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($model->group->name), 'url' => ['/group/view', 'id' => $model->group->id]];
$this->params['breadcrumbs'][] = ['label' => Html::encode($model->title), 'url' => ['/contest/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Setting');
$this->params['model'] = $model;
$problems = $model->problems;
$scoreboardFrozenTime = Yii::$app->setting->get('scoreboardFrozenTime') / 3600;
$contest_id = $model->id;
$requestUrl = Url::toRoute('/problem/select');
$addUrl = Url::to(['/homework/addproblem', 'id' => $contest_id]);
$cloneUrl = Url::to(['/homework/clone']);

$js = <<<EOT
$("#select_submit").click(function () {
    var keys = [];
    $("#frmchild1").contents().find("#select_grid").find('input[type=checkbox]:checked').each(function(){ 
        keys.push(parseInt($(this).val())); 
    });
    qstr = $("#frmchild1").contents().find("#q").val();
    if(qstr!=""){
        ss = qstr.replace("，",",").replace(" ",",").split(",");
        count = ss.length;
        for (i=0;i<count;i++){
            id = parseInt(ss[i]);
            if(Object.is(id, NaN)) continue;
            if(keys.indexOf(id)==-1){
                keys.push(id);
            }
        }
    }
    $.post({
       url: "$addUrl", 
       dataType: 'json',
       data: {problem_ids: keys}
    });
});

function resize_iframe(){
    var iframe = document.getElementById("frmchild1");
    try {
        iframe.height =  document.body.offsetHeight*0.8;
    } catch (ex) { }
}

resize_iframe();
$(window).resize(function(){
    resize_iframe();
 });

 $("#clone_submit").click(function () {
    var id = $("#clone_select").val();
    $.ajax({
        url: "$cloneUrl",
        type:'post',
        data: {contest_id:$contest_id ,group_id: id},
        error: function(){
            $("#msg-content").html("克隆时发生未知错误。")
            $('#msg-modal').modal("show");
        },
        success:function(html){
            $("#msg-content").html(html)
            $('#msg-modal').modal("show");
        }   
    });
});
EOT;
$this->registerJs($js);
$css = <<< EOT
 #select_modal .modal-dialog {
    width:90%!important;
 }
EOT;
$this->registerCss($css);
?>
<div class="homework-update">
    <p class="lead"><?= Yii::t('app', 'Problems') ?></p>
    <div class="table-responsive table-problem-list1">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="70px">#</th>
                    <th width="120px"><?= Yii::t('app', 'Problem ID') ?></th>
                    <th><?= Yii::t('app', 'Problem Name') ?></th>
                    <th width="200px"><?= Yii::t('app', 'Operation') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($problems as $key => $p) : ?>
                    <tr>
                        <th><?= Html::a('P' . ($key + 1),  ['/contest/problem', 'id' => $model->id, 'pid' => $key, '#' => 'problem-anchor']) ?></th>
                        <th><?= Html::a($p['problem_id'], ['/contest/problem', 'id' => $model->id, 'pid' => $key, '#' => 'problem-anchor']) ?></th>
                        <td><?= Html::a(Html::encode($p['title']),  ['/contest/problem', 'id' => $model->id, 'pid' => $key, '#' => 'problem-anchor']) ?></td>
                        <th>

                            <?php Modal::begin([
                                'header' => Yii::t('app', 'Modify') . ' : P' . (1 + $key),
                                'toggleButton' => ['label' => Yii::t('app', 'Modify'), 'class' => 'btn btn-warning'],
                            ]); ?>

                            <?= Html::beginForm(['/homework/updateproblem', 'id' => $model->id]) ?>

                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><?= Html::label(Yii::t('app', 'Current Problem ID'), 'problem_id') ?></span>
                                    <?= Html::textInput('problem_id', $p['problem_id'], ['class' => 'form-control', 'readonly' => 1]) ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><?= Html::label(Yii::t('app', 'New Problem ID'), 'new_problem_id') ?></span>
                                    <?= Html::textInput('new_problem_id', $p['problem_id'], ['class' => 'form-control']) ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>
                            </div>
                            <?= Html::endForm(); ?>

                            <?php Modal::end(); ?>

                            <?= Html::a(Yii::t('app', 'Delete'), [
                                'deleteproblem',
                                'id' => $model->id,
                                'pid' => $p['problem_id']
                            ], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ]) ?>

                        </th>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th></th>
                    <th></th>
                    <th>
                        <?php Modal::begin([
                            'id' => 'select_modal',
                            'class' =>'modal-wide',
                            'header' => Yii::t('app', 'Add a problem'),
                            'toggleButton' => ['label' => Yii::t('app', 'Add a problem'), 'class' => 'btn btn-success'],
                            ]); 
                        ?>
                        <IFRAME  scrolling="auto" frameBorder=0 id="frmchild1" name="frmchild1"
                            src="<?= $requestUrl ?>" width="100%" allowTransparency="true"></IFRAME>
                            <div class="row" style="padding-top:10px"><?= Html::button(Yii::t('app', 'Submit'), ['id'=> 'select_submit','class' => 'col-md-2 col-md-offset-5 btn btn-success','data-dismiss'=>'modal']) ?></div>
                        <?php Modal::end(); ?>
                    </th>
                    <th></th>
                </tr>
            </tbody>
        </table>
    </div>


    <p class="lead">
        <?= Yii::t('app', 'Announcements') ?>
        <?php Modal::begin([
            'header' => Yii::t('app', 'Make an announcement'),
            'toggleButton' => ['label' => Yii::t('app', 'Create'), 'class' => 'btn btn-xs btn-success'],
        ]); ?>

        <?php $form = ActiveForm::begin(); ?>
    <div class="alert alert-light"><i class="fa fa-info-circle"></i> 公告发布后将显示在比赛界面中。也可以使用 <?= Html::a('全局公告', ['/admin/setting']) ?>。</div>
    <?= $form->field($newAnnouncement, 'content')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>
    </div>
    <?php ActiveForm::end(); ?>

    <?php Modal::end(); ?>
    </p>

    <?= \yii\grid\GridView::widget([
        'layout' => '{items}{pager}',
        'pager' => [
            'firstPageLabel' => Yii::t('app', 'First'),
            'prevPageLabel' => '« ',
            'nextPageLabel' => '» ',
            'lastPageLabel' => Yii::t('app', 'Last'),
            'maxButtonCount' => 10
        ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            return ['class' => 'animate__animated animate__fadeInUp'];
        },
        'dataProvider' => $announcements,
        'columns' => [
            'content:ntext',
            'created_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['class' => 'a_just'],
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model, $key) use ($contest_id) {
                        $options = [
                            'title' => Yii::t('yii', 'Delete'),
                            'aria-label' => Yii::t('yii', 'Delete'),
                            'data-confirm' => '删除该项公告，确定删除？',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ];
                        return Html::a('<span class="fa fa-trash"></span>', Url::toRoute(['homework/delete_announcement', 'contest_id' => $contest_id, 'id' => $model->id]), $options);
                    },
                ]
            ],
        ],

    ]) ?>    

    <div class="homework-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'title', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Title') . '</span>{input}</div>'])->textInput() ?>


        <?= $form->field($model, 'start_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Start Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
            'clientOptions' => [
                'istoday' => true,
                'type' => 'datetime'
            ],
            'options' => ['autocomplete' => 'off']
        ]) ?>

        <?= $form->field($model, 'end_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'End Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
            'clientOptions' => [
                'istoday' => true,
                'type' => 'datetime'
            ],
            'options' => ['autocomplete' => 'off']
        ]) ?>

        <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 封榜仅对 ACM/ICPC 或作业有效，请不要在其它赛制启用，否则可能出现未知行为。如果不需要封榜请留空，当前会在比赛结束 <?= $scoreboardFrozenTime ?> 小时后才会自动在前台页面解除封榜限制。如需提前结束封榜也可选择清空该表单项。使用封榜功能，后台管理界面的比赛榜单仍然处于实时榜单。</div>

        <?= $form->field($model, 'lock_board_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Lock Board Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
            'clientOptions' => [
                'istoday' => true,
                'type' => 'datetime'
            ]
        ]) ?>

        <?= $form->field($model, 'description')->widget(Yii::$app->setting->get('ojEditor')); ?>

        <?= $form->field($model, 'editorial')->widget(Yii::$app->setting->get('ojEditor')); ?>


        <?= $form->field($model, 'status')->radioList([
            Contest::STATUS_VISIBLE => Yii::t('app', 'Public'),
            Contest::STATUS_HIDDEN => Yii::t('app', 'Hidden')
        ])->hint('公开：小组成员可以看到该比赛，隐藏：小组成员无法看到该比赛') ?>


        <?= $form->field($model, 'enable_clarify')->radioList([
            0 => '关闭',
            1 => '开启',
        ])->hint('答疑界面在作业中可以根据需要开启或关闭。') ?>

        <?= $form->field($model, 'enable_board')->radioList([
        '1' => '开启榜单',
        '0' => '关闭榜单',
        ])->hint('是否开启榜单功能。关闭榜单后，只能在榜单中看到自己的信息。') ?>


        <?= $form->field($model, 'type')->radioList([
            Contest::TYPE_RANK_SINGLE => Yii::t('app', 'Single Ranked'),
            Contest::TYPE_RANK_GROUP => Yii::t('app', 'ACM/ICPC'),
            Contest::TYPE_HOMEWORK => Yii::t('app', 'Homework'),
            Contest::TYPE_OI => Yii::t('app', 'OI'),
            Contest::TYPE_IOI => Yii::t('app', 'IOI'),
        ])->hint('不同类型的区别只在于榜单的排名方式。如需使用OI比赛，请在后台设置页面启用OI模式。') ?>


        <?= $form->field($model, 'language')->radioList([
            -1 => 'All',
            0 => 'C',
            1 => 'C++',
            2 => 'Java',
            3 => 'Python3',
        ])->hint('为 All 时可以使用任意的语言编程，否则在比赛中只能以指定的语言编程并提交。') ?>

        <div class="form-group">
            <div class="row">
            <div class="text-center">
                <div class="btn-group">
                    <div class="btn-group" ><?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success','style'=> "min-width:150px"]) ?></div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" 
                            data-toggle="dropdown">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><?= Html::a('删除该比赛', ['/homework/delete', 'id' => $model->id], ['class' => 'btn btn-link','data-confirm' => '此操作不可恢复，你确定要删除吗？','data-method' => 'post']) ?></li>
                            <?php if (count($group_datas)>0): ?>
                            <li role="separator" class="divider"></li>
                            <li>
                                <?= Html::a('克隆该比赛', '#', ['id' => 'clone','data-toggle' => 'modal','data-target' => '#clone-modal','class' => 'btn btn-link']); ?>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <div class="row">
        <div class="col-md-2 col-md-offset-5">
            <?php Modal::begin([
                'id' => 'clone-modal',
                'header' => '克隆到指定小组',
                ]); 
            ?>
            <div style="padding:10px 20px">
            <?= Html::dropDownList('clone_select','name[]', ArrayHelper::map($group_datas, 'id', 'name'), ['id'=>'clone_select','class' => 'form-control']);?>
            </div>
            <div class="row" style="padding-top:10px"><?= Html::button(Yii::t('app', 'Submit'), ['id'=> 'clone_submit','class' => 'col-md-2 col-md-offset-5 btn btn-success','data-dismiss'=>'modal']) ?></div>
            <?php Modal::end(); ?>

            <?php Modal::begin([
                'id' => 'msg-modal',
                'header' => '信息',
                ]); 
            ?>
            <div id="msg-content" style="padding:20px 30px">
            </div>
            <div class="row" style="padding-top:10px"><?= Html::button(Yii::t('app', 'Ok'), ['id'=> 'msg_submit','class' => 'col-md-2 col-md-offset-5 btn btn-success','data-dismiss'=>'modal']) ?></div>
            <?php Modal::end(); ?>
        </div>
    </div>
</div>
