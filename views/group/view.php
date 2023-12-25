<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use app\models\GroupUser;
use yii\widgets\ListView;
use app\models\Contest;
use yii\bootstrap\Nav;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
/* @var $this yii\web\View */
/* @var $model app\models\Group */
/* @var $contestDataProvider yii\data\ActiveDataProvider */
/* @var $userDataProvider yii\data\ActiveDataProvider */
/* @var $newContest app\models\Contest */
/* @var $newGroupUser app\models\GroupUser */

$this->title = $model->name;
$scoreboardFrozenTime = Yii::$app->setting->get('scoreboardFrozenTime') / 3600;
?>
<?= Nav::widget([
    'items' => [
        [
            'label' => $this->title,
            'url' => ['group/view', 'id' => $model->id]
        ],
        [
            'label' => Yii::t('app', 'Member'),
            'url' => ['group/user', 'id' => $model->id]
        ],
    ],
    'options' => ['class' => 'nav-tabs']
]) ?>

<br>

<div class="group-view">
    <div class="row">

        <div class="col-md-9">

            <?php if ($contestDataProvider->count > 0) {
                echo ListView::widget([
                    'dataProvider' => $contestDataProvider,
                    'itemView' => ($model->hasPermission() && $model->role != GroupUser::ROLE_ASSISTANT) ? '_contest_item1' : '_contest_item',
                    'itemOptions' => ['tag' => false],
                    'layout' => '{items}<p></p>{pager}',
                    'options' => ['class' => 'list-group animate__animated animate__fadeInUp'],
                    'pager' => [
                        'firstPageLabel' => Yii::t('app', 'First'),
                        'prevPageLabel' => '« ',
                        'nextPageLabel' => '» ',
                        'lastPageLabel' => Yii::t('app', 'Last'),
                        'linkOptions' => ['class' => 'page-link'],
                        'maxButtonCount' => 10,
                    ],
                    'viewParams' => [
                        'group_datas' => $group_datas
                    ],
                ]);
            } else {

                echo '<div class="alert alert-light"><i class=" fa fa-info-circle"></i> 组长还未创建题目。</div>';
            }
            ?>
        </div>
        <div class="col-md-3">
            <div>
                <?php if ($model->kanban) : ?>
                    <div class="list-group-item list-group-item-action"><?= Yii::$app->formatter->asMarkdown($model->kanban) ?></div><br>
                <?php endif; ?>
            </div>
            <div class="list-group">
                <div class="list-group-item"><?= Yii::t('app', 'Join Policy') ?><span class="float-right"> <?= $model->getJoinPolicy() ?></span></div>
                <div class="list-group-item"><?= Yii::t('app', 'Status') ?><span class="float-right"> <?= $model->getStatus() ?></span></div>
            </div>

            <?php if ($model->hasPermission()) : ?>
                <?php Modal::begin([
                    'header' => Yii::t('app', 'Create Contest'),
                    'toggleButton' => [
                        'label' => Yii::t('app', 'Create Contest'),
                        'tag' => 'a',
                        'style' => 'cursor:pointer;',
                        'class' => 'btn btn-success btn-block'
                    ]
                ]); ?>
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($newContest, 'title', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Title') . '</span>{input}</div>'])->textInput()->label(false) ?>


                <?= $form->field($newContest, 'start_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Start Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
                    'clientOptions' => [
                        'istoday' => true,
                        'type' => 'datetime'
                    ],
                    'options' => ['autocomplete' => 'off']
                ]) ?>
                <?= $form->field($newContest, 'end_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'End Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
                    'clientOptions' => [
                        'istoday' => true,
                        'type' => 'datetime'
                    ],
                    'options' => ['autocomplete' => 'off']
                ]) ?>

                <?= $form->field($newContest, 'lock_board_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Lock Board Time') . '</span>{input}</div>'])->widget('app\widgets\laydate\LayDate', [
                    'clientOptions' => [
                        'istoday' => true,
                        'type' => 'datetime'
                    ]
                ])->hint("如果不需要封榜请留空，当前会在比赛结束{$scoreboardFrozenTime}小时后才会自动在前台页面解除封榜限制。
                        如需提前结束封榜也可选择清空该表单项。
                    ") ?>

                <?= $form->field($newContest, 'type')->radioList([
                    Contest::TYPE_RANK_SINGLE => Yii::t('app', 'Single Ranked'),
                    Contest::TYPE_RANK_GROUP => Yii::t('app', 'ACM/ICPC'),
                    Contest::TYPE_HOMEWORK => Yii::t('app', 'Homework'),
                    Contest::TYPE_OI => Yii::t('app', 'OI'),
                    Contest::TYPE_IOI => Yii::t('app', 'IOI'),
                ])->hint('不同类型的区别只在于榜单的排名方式。<a href="/wiki/index#sz" target="_blank">点击查看赛制类型</a>') ?>

                <?= $form->field($newContest, 'enable_clarify')->radioList([
                    0 => '关闭',
                    1 => '开启',
                ])->hint('答疑界面在作业中可以根据需要开启或关闭。') ?>


                <?= $form->field($newContest, 'language')->radioList([
                    -1 => 'All',
                    0 => 'C',
                    1 => 'C++',
                    2 => 'Java',
                    3 => 'Python3',
                ])->hint('为 All 时可以使用任意的语言编程，否则在比赛中只能以指定的语言编程并提交。') ?>


                <div class="form-group">
                <div class="row"><div class="col-md-4 col-md-offset-4"><?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?></div></div>
                </div>
                <?php ActiveForm::end(); ?>
                <?php Modal::end(); ?>
            <?php endif; ?>
            <br>
            <?php if (!Yii::$app->user->isGuest && ($model->role == GroupUser::ROLE_LEADER || Yii::$app->user->identity->isAdmin())) : ?>
                <?= Html::a(Yii::t('app', 'Setting'), ['/group/update', 'id' => $model->id], ['class' => 'btn btn-success btn-block']) ?>
            <?php endif; ?>
        </div>
    </div>
    <?php if($group_datas!=null && count($group_datas)>0): ?>
    <div class="row">
        <div class="col-md-2 col-md-offset-5">
            <?php Modal::begin([
                'id' => 'clone-modal',
                'header' => '克隆到指定小组',
                ]); 
            ?>
            <div id='clone_cid' style='display:none'>0</div>
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
    <?php endif; ?>
</div>
<?php
if($group_datas!=null && count($group_datas)>0){
    $js = <<<EOF
    function clone_click(obj) {
        $("#clone_cid").html($(obj).attr('data-cid'));
        $('#clone-modal').modal("show");
    }
    EOF;
    $this->registerJs($js,View::POS_HEAD);
    $cloneUrl = Url::to(['/homework/clone']);
    $js = <<<EOT
     $("#clone_submit").click(function () {
        var cid = $("#clone_cid").text();
        var id = $("#clone_select").val();
        $.ajax({
            url: "$cloneUrl",
            type:'post',
            data: {contest_id:cid ,group_id: id},
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
}
?>