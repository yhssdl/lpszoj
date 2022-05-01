<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Problem */

$this->title = Yii::t('app', 'Import Problem From Polygon System');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Problems'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="problem-create">

    <p class="lead"><?= Html::encode($this->title) ?></p>
    <?= Html::a("前往 Polygon 题库", ['/polygon/problem/index'], ['class' => 'btn btn-default btn-block']) ?>
    <p></p>
    <?= Html::beginForm() ?>
    <div class="form-group animate__animated animate__fadeInUp">
        <p>单个添加</p>
        <div class="input-group">
            <span class="input-group-addon" id="polygon_problem_id"><?= Yii::t('app', 'Polygon '.Yii::t('app', 'Problem ID')) ?></span>
            <?= Html::textInput('polygon_problem_id', '', ['class' => 'form-control']) ?>
        </div>
        <p class="help-block">请提供位于 <?= Html::a(Yii::t('app', 'Polygon System'), ['/polygon/problem']) ?> 问题对应 ID</p>
    </div>

    <div class="form-group animate__animated animate__fadeInUp">
        <p>批量添加</p>
        <div class="input-group">
            <span class="input-group-addon"><?= Yii::t('app', 'From') ?></span>
            <?= Html::textInput('polygon_problem_id_from', '', ['class' => 'form-control']) ?>
            <span class="input-group-addon"><?= Yii::t('app', 'to') ?></span>
            <?= Html::textInput('polygon_problem_id_to', '', ['class' => 'form-control']) ?>
        </div>
        <p class="help-block">请提供位于 <?= Html::a(Yii::t('app', 'Polygon System'), ['/polygon/problem']) ?> 问题对应 ID 的范围</p>
    </div>

    <div class="form-group animate__animated animate__fadeInUp">
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>
        <b>
        注意：
        <ol>
            <li>当你重复添加同一个位于 <?= Html::a(Yii::t('app', 'Polygon System'), ['/polygon/problem']) ?> 问题对应的 ID 只会覆盖现有题库的题目内容及测试数据，不会创建一道新题目。</li>
            <li>如果涉及需要修改题面或数据时，可以使用重复添加来实现 Polygon 中的题目与题库中题目的同步。单独修改 Polygon 中题目信息或数据时，题库中的题目不会主动随着 Polygon 更新，需在此页面重复添加一遍。</li>
        </ol>
    </b>
    </div>

    <?= Html::endForm() ?>
</div>
