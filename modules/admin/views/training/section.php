<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use app\models\Contest;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\Homework */

$this->title = Html::encode($model->title);
$this->params['model'] = $model;
$problems = $model->problems;
$contest_id = $model->id;
$requestUrl = Url::toRoute('/problem/select');
$addUrl = Url::to(['addproblem', 'id' => $contest_id]);
$js = <<<EOT
$("#select_submit").click(function () {
    var keys = [];
    $("#frmchild1").contents().find("#select_grid").find('input[type=checkbox]:checked').each(function(){ 
        keys.push($(this).val()); 
    }); 

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
                        <th><?= Html::a('P' . ($key + 1), ['view', 'id' => $model->id, 'action' => 'problem', 'problem_id' => $key]) ?></th>
                        <th><?= Html::a($p['problem_id'], '') ?></th>
                        <td><?= Html::a(Html::encode($p['title']), ['view', 'id' => $model->id, 'action' => 'problem', 'problem_id' => $key]) ?></td>
                        <th>

                            <?php Modal::begin([
                                'header' => Yii::t('app', 'Modify') . ' : P' . (1 + $key),
                                'toggleButton' => ['label' => Yii::t('app', 'Modify'), 'class' => 'btn btn-warning'],
                            ]); ?>

                            <?= Html::beginForm(['updateproblem', 'id' => $model->id]) ?>

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
                            'header' => Yii::t('app', 'Add a problem'),
                            'toggleButton' => ['label' => Yii::t('app', 'Add a problem'), 'class' => 'btn btn-success'],
                        ]); ?>

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

    <div class="homework-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'title', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', 'Title') . '</span>{input}</div>'])->textInput() ?>


        <?= $form->field($model, 'description')->widget(Yii::$app->setting->get('ojEditor')); ?>

        <?= $form->field($model, 'punish_time', ['template' => '<div class="input-group"><span class="input-group-addon">' . Yii::t('app', '过关题数') . '</span>{input}</div>{hint}'])->textInput()->hint('完成并通过指定的题目数量即可过关，如果为<code>-1</code>时需要通过所有题目。') ?>

        <?= $form->field($model, 'enable_clarify')->radioList([
            0 => '未通过前隐藏后续小节',
            1 => '显示后续小节',
        ])->label(false) ?>



        <?= $form->field($model, 'language')->radioList([
            -1 => 'All',
            0 => 'C',
            1 => 'C++',
            2 => 'Java',
            3 => 'Python3',
        ])->hint('为 All 时可以使用任意的语言编程，否则在比赛中只能以指定的语言编程并提交。') ?>

        <div class="form-group">
            <div class="row">
                <?php if ($model->id == 0): ?>
                    <div class="text-center"><?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success','style'=> "min-width:150px"]) ?></div>
                <?php else:?>

                <div class="text-center">
                    <div class="btn-group">
                        <div class="btn-group" ><?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success','style'=> "min-width:150px"]) ?></div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" 
                                data-toggle="dropdown">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><?= Html::a('删除该小节', ['delete_section', 'id' => $model->id], ['data-confirm' => '此操作不可恢复，你确定要删除吗？','data-method' => 'post']) ?></li>
                            </ul>
                        </div>
                    </div>
                </div>          
                <?php endif;?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

</div>