<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $newContestPrint app\models\ContestPrint */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->title;

?>
<div class="print-source-index" style="margin-top: 20px">

    <div class="well">
        如需打印代码以供队友查看，可以在此提交代码内容，工作人员打印好后会送至队伍前。
    </div>

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
        'columns' => [
            [
                'attribute' => 'id',
                'value' => function ($model, $key, $index, $column) {
                    return Html::a($model->id, ['/print/view', 'id' => $model->id], ['target' => '_blank']);
                },
                'format' => 'raw'
            ],
            'created_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'controller' => 'print'
            ],
        ],
    ]); ?>

    <hr>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($newContestPrint, 'source')->widget('app\widgets\codemirror\CodeMirror'); ?>

    <div class="form-group">
        <?= Html::submitButton('提交', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>