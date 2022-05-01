<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\polygon\models\ProblemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Polygon System');
?>
<h2><?= $this->title ?></h2>
<p><?= Yii::t('app', 'Professional way to prepare programming contest problem') ?></p>
<hr>
<p>受 <a href="https://polygon.codeforces.com/" target="_blank">Codeforces Polygon</a> 的启发，为 OJ 开发了 Polygon系统：</p>
<div class="well">
    <ul>
        <li>出题人填写问题基本信息</li>
        <li>测试数据的准备</li>
        <li>验证题目的正确与否</li>
    </ul>
</div>
<p>注意：任何注册用户都可以用它来准备题目，但普通用户只能查看自己创建的题目，管理员有权查看所有用户的题目。
    并且只有管理员把 Polygon 中的题目同步到题库后才可以用来准备比赛或显示在题库列表中。</p>

<hr>
<div class="problem-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create Problem'), ['/polygon/problem/create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo $this->render('/problem/_search', ['model' => $searchModel]); ?>

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
        'columns' => [
            [
                'attribute' => 'id',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->id, ['problem/view', 'id' => $key]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'title',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a(Html::encode($model->title), ['problem/view', 'id' => $key]);
                },
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
                'format' => 'raw'
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'controller' => 'problem'
            ],
        ],
    ]); ?>
</div>
