<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use app\models\Contest;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Contest */
/* @var $newAnnouncement app\models\ContestAnnouncement */
/* @var $announcements yii\data\ActiveDataProvider */

$this->title = $model->title;
$problems = $model->problems;
$contest_id = $model->id;
$requestUrl = Url::toRoute('/problem/select');
$addUrl = Url::to(['contest/addproblem', 'id' => $contest_id]);
$js = <<<EOT
$("#select_submit").click(function () {
    $("#select_modal").modal('hide');
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
EOT;
$this->registerJs($js);
$css = <<< EOT
 .modal-dialog {
    width:90%!important;
 }
EOT;
$this->registerCss($css);
?>
<p class="lead"><?= Html::encode($this->title) ?></p>

<div class="contest-view">

    <div class="btn-group btn-group-justified">
        <div class="btn-group"><?= Html::a('选手', ['register', 'id' => $model->id], ['class' => 'btn btn-primary']) ?></div>
        <div class="btn-group"><?= Html::a('题册', ['print', 'id' => $model->id], ['class' => 'btn btn-primary', 'target' => '_blank']) ?></div>
        <div class="btn-group"><?= Html::a(Yii::t('app', 'Editorial'), ['editorial', 'id' => $model->id], ['class' => 'btn btn-primary']) ?></div>
        <div class="btn-group"><?= Html::a(Yii::t('app', 'Calculate rating'), ['rated', 'id' => $model->id], ['class' => 'btn btn-info']) ?></div>
        <div class="btn-group"><?= Html::a(Yii::t('app', 'Print'), ['/print', 'id' => $model->id], ['class' => 'btn btn-info', 'target' => '_blank']) ?></div>
        <div class="btn-group"><?= Html::a(Yii::t('app', 'Clarification'), ['clarify', 'id' => $model->id], ['class' => 'btn btn-info', 'target' => '_blank']) ?></div>
        <div class="btn-group"><?= Html::a(Yii::t('app', 'Submit'), ['status', 'id' => $model->id], ['class' => 'btn btn-info', 'target' => '_blank']) ?></div>
        <div class="btn-group"><?= Html::a('导出成绩', ['export', 'id' => $model->id], ['class' => 'btn btn-success', 'target' => '_blank']) ?></div>
        <div class="btn-group"><?= Html::a('外榜', ['/contest/standing2', 'id' => $model->id], ['class' => 'btn btn-success', 'target' => '_blank']) ?></div>
        <div class="btn-group"><?= Html::a('终榜', ['rank', 'id' => $model->id], ['class' => 'btn btn-success', 'target' => '_blank']) ?></div>

        <div class="btn-group">
            <?php Modal::begin([
                'header' => Yii::t('app', 'Scroll Scoreboard'),
                'toggleButton' => ['label' => Yii::t('app', 'Scroll Scoreboard'), 'class' => 'btn btn-success']
            ]); ?>
            <?= Html::beginForm(['contest/scroll-scoreboard', 'id' => $model->id], 'get', ['target' => '_blank']) ?>
            <div class="alert alert-light"><i class="fa fa-info-circle"></i> 滚榜只支持罚时 20 分钟的比赛，其他值请先修改 web/js/scrollboard.js。</div>
            <div class="form-group">
                <?= Html::label(Yii::t('app', 'Number of gold medals'), 'gold') ?>
                <?= Html::textInput('gold', round($model->getContestUserCount() * 0.1), ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= Html::label(Yii::t('app', 'Number of silver medals'), 'silver') ?>
                <?= Html::textInput('silver', round($model->getContestUserCount() * 0.2), ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= Html::label(Yii::t('app', 'Number of bronze medals'), 'bronze') ?>
                <?= Html::textInput('bronze', round($model->getContestUserCount() * 0.3), ['class' => 'form-control']) ?>
            </div>
            <?php if ($model->getRunStatus() == Contest::STATUS_ENDED) : ?>
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('app', '打开滚榜页面'), ['class' => 'btn btn-success btn-block']) ?>
                </div>
                <p class="hint-block">
                    1. 填写上述奖牌数，在滚榜页面会对获奖队伍有颜色的区分。暂无冠亚季军颜色区分，若有此需求，请将其包含在金牌数中。
                </p>
                <p class="hint-block">
                    2. 打开滚榜页面后，通过不断按<code>回车</code>来进行滚动。
                </p>
                <p class="hint-block">
                    3. 建议把浏览器设为全屏显示（打开页面后，按<code>F11</code>键）体验更佳。
                </p>
            <?php else : ?>
                <div class="alert alert-light"><i class="fa fa-info-circle"></i> 比赛尚未结束，暂时不能滚榜。</div>
            <?php endif; ?>
            <?= Html::endForm(); ?>
            <?php Modal::end(); ?>
        </div>



        <div class="btn-group"><?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?') . ' 该操作不可恢复，会删除所有与该场比赛有关的提交记录及其它信息',
                                        'method' => 'post',
                                    ],
                                ]) ?></div>
    </div>
    <br>


    <span class="lead">
        <?= Yii::t('app', 'Problems') ?>
    </span>
    <?php Modal::begin([
        'header' => Yii::t('app', '设置题目来源'),
        'toggleButton' => ['label' => '设置下列所有题目的来源', 'class' => 'btn btn-success btn-xs'],
    ]); ?>
    <?= Html::beginForm(['contest/set-problem-source', 'id' => $model->id]) ?>
    <div class="alert alert-light"><i class="fa fa-info-circle"></i> 设置来源有利于在题库中根据题目来源来搜索题目。此操作会修改题目的来源信息。</div>
    <div class="form-group">
        <?= Html::label(Yii::t('app', 'Source'), 'problem_id') ?>
        <?= Html::textInput('source', $model->title, ['class' => 'form-control']) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>
    </div>
    <?= Html::endForm(); ?>
    <?php Modal::end(); ?>

    <?php Modal::begin([
        'header' => Yii::t('app', '设置下列所有题目在前台显示状态'),
        'toggleButton' => ['label' => '设置题目在前台显示状态', 'class' => 'btn btn-success btn-xs'],
    ]); ?>
    <?= Html::beginForm(['contest/set-problem-status', 'id' => $model->id]) ?>
    <div class="alert alert-light"><i class="fa fa-info-circle"></i> 该操作用于该场比赛目前添加的所有题目在前台设为隐藏或可见。</div>
    <div class="form-group">
        <?= Html::label(Yii::t('app', 'Status'), 'status') ?>
        <label class="radio-inline">
            <input type="radio" name="status" value="<?= \app\models\Problem::STATUS_VISIBLE ?>">
            <?= Yii::t('app', 'Visible') ?>
        </label>
        <label class="radio-inline">
            <input type="radio" name="status" value="<?= \app\models\Problem::STATUS_HIDDEN ?>">
            <?= Yii::t('app', 'Hidden') ?>
        </label>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>
    </div>
    <?= Html::endForm(); ?>
    <?php Modal::end(); ?>
    <br><br>
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
                        <td class="animate__animated animate__fadeInUp"><?= Html::a('P' . ($key + 1), ['/admin/problem/view', 'id' => $p['problem_id']]) ?></td>
                        <td class="animate__animated animate__fadeInUp"><?= Html::a($p['problem_id'], ['/admin/problem/view', 'id' => $p['problem_id']]) ?></td>
                        <td class="animate__animated animate__fadeInUp"><?= Html::a(Html::encode($p['title']), ['/admin/problem/view', 'id' => $p['problem_id']]) ?></td>
                        <td>
                       
                                <?php Modal::begin([
                                    'header' => Yii::t('app', 'Modify') . ' : ' . ($key + 1),
                                    'toggleButton' => ['label' => Yii::t('app', 'Modify'), 'class' => 'btn btn-warning'],
                                ]); ?>

                                <?= Html::beginForm(['contest/updateproblem', 'id' => $model->id]) ?>

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
                       
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td>
                        <?php Modal::begin([
                            'id' => 'select_modal',
                            'header' => Yii::t('app', 'Add a problem'),
                            'toggleButton' => ['label' => Yii::t('app', 'Add a problem'), 'class' => 'btn btn-success'],
                        ]); ?>

                            <IFRAME  scrolling="auto" frameBorder=0 id=frmchild1 name=frmchild1 height="600px"
                            src="<?= $requestUrl ?>" width="100%" allowTransparency="true" style="overflow-x: hidden;"></IFRAME>
                            
                            <div class="row" style="padding-top:10px"><?= Html::button(Yii::t('app', 'Submit'), ['id'=> 'select_submit','class' => 'col-md-2 col-md-offset-5 btn btn-success']) ?></div>

                        <?php Modal::end(); ?>
                    </td>
                    <td></td>
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
    <div class="animate__animated animate__fadeInUp">
        <?= \yii\grid\GridView::widget([
            'dataProvider' => $announcements,
            'tableOptions' => ['class' => 'table table-striped table-bordered table-text-center'],
            'columns' => [
                'content:ntext',
                'created_at:datetime',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['class'=>'a_just'],
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
                            return Html::a('<span class="fa fa-trash"></span>', Url::toRoute(['contest/delete_announcement', 'contest_id' => $contest_id, 'id' => $model->id]), $options);
                        },
                    ]
                ],
            ],
        ]) ?>
    </div>
    <p class="lead">
        <?= Yii::t('app', 'Information') ?>
        <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-xs btn-success']) ?>
    </p>

    <div class="animate__animated animate__fadeInUp">
        <?= DetailView::widget([
            'model' => $model,
            'template' => '<tr><th class="bg-tablehead" style="width:150px;text-align:center;">{label}</th><td style="min-width:300px;">{value}</td></tr>',
            'options' => ['id' => 'grid', 'class' => 'table table-bordered'],
            'attributes' => [
                'id',
                'title',
                'start_time',
                'end_time',
                'lock_board_time',
                'description:html',
                [
                    'label' => Yii::t('app', 'Scenario'),
                    'value' => $model->scenario == Contest::SCENARIO_ONLINE ? Yii::t('app', 'Online') : Yii::t('app', 'Offline')
                ]
            ],
        ]) ?>
    </div>
</div>
<?php Modal::begin([
    'header' => Yii::t('app', 'Information'),
    'options' => ['id' => 'modal-info'],
    'size' => Modal::SIZE_LARGE
]); ?>
<div id="modal-content">
</div>
<?php Modal::end(); ?>
<?php
$js = "
$('[data-click=modal]').click(function() {
    $.ajax({
        url: $(this).attr('href'),
        type:'post',
        error: function(){alert('error');},
        success:function(html){
            $('#modal-content').html(html);
            $('#modal-info').modal('show');
        }
    });
});
";
$this->registerJs($js);
?>