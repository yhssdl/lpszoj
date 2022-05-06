<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Problem */

$this->title = Yii::t('app', 'Import Problem From Polygon System');
?>
<div class="problem-create">

    <p class="lead"><?= Html::encode($this->title) ?></p>
    <?= Html::a("前往 Polygon 题库", ['/polygon/problem/index'], ['class' => 'btn btn-default btn-block']) ?>
    <br>
    <div class="alert alert-light">
    <i class="fa fa-info-circle"></i> 感谢您参与 信息技术 &amp; 人工智能 公共题库的建设！下面两栏请任选一项填写，重复同步同一题目仅做强制覆盖不会另外新建题目。
    </div>
    <?= Html::beginForm() ?>
    <div class="form-group animate__animated animate__fadeInUp">
        <p>单个添加</p>
        <div class="input-group">
            <span class="input-group-addon" id="polygon_problem_id"><?= Yii::t('app', 'Polygon '.Yii::t('app', 'Problem ID')) ?></span>
            <?= Html::textInput('polygon_problem_id', '', ['class' => 'form-control']) ?>
        </div>
        <p class="help-block"><i class="fa fa-info-circle"></i> 请提供位于 <?= Html::a(Yii::t('app', 'Polygon System'), ['/polygon/problem']) ?> 问题对应 ID</p>
    </div>

    <div class="form-group animate__animated animate__fadeInUp">
        <p>批量添加</p>
        <div class="input-group">
            <span class="input-group-addon"><?= Yii::t('app', 'From') ?></span>
            <?= Html::textInput('polygon_problem_id_from', '', ['class' => 'form-control']) ?>
            <span class="input-group-addon"><?= Yii::t('app', 'to') ?></span>
            <?= Html::textInput('polygon_problem_id_to', '', ['class' => 'form-control']) ?>
        </div>
        <p class="help-block"><i class="fa fa-info-circle"></i> 请提供位于 <?= Html::a(Yii::t('app', 'Polygon System'), ['/polygon/problem']) ?> 问题对应 ID 的范围</p>
    </div>

    <div class="form-group animate__animated animate__fadeInUp">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>
    </div>

    <?= Html::endForm() ?>
</div>
