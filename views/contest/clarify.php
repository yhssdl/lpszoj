<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $newClarify app\models\Discuss */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $discuss app\models\Discuss */

$this->title = $model->title;
$this->params['model'] = $model;

if ($discuss != null) {
    return $this->render('_clarify_view', [
        'clarify' => $discuss,
        'newClarify' => $newClarify
    ]);
}
?>
<div style="margin-top: 20px">
    <?php
    if ($dataProvider->count > 0) {
        echo GridView::widget([
            'layout' => '{items}{pager}',
            'pager' => [
                'firstPageLabel' => Yii::t('app', 'First'),
                'prevPageLabel' => '« ',
                'nextPageLabel' => '» ',
                'lastPageLabel' => Yii::t('app', 'Last'),
                'maxButtonCount' => 10
            ],
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-striped table-bordered table-text-center'],
            'rowOptions' => function ($model, $key, $index, $grid) {
                return ['class' => 'animate__animated animate__fadeInUp'];
            },
            'options' => ['class' => 'table-responsive', 'style' => 'margin:0 auto;width:50%;min-width:600px'],
            'columns' => [
                [
                    'attribute' => Yii::t('app', 'Announcement'),
                    'value' => function ($model, $key, $index, $column) {
                        return Yii::$app->formatter->asMarkdown($model->content);
                    },
                    'format' => 'html'
                ],
                [
                    'attribute' => 'created_at',
                    'options' => ['width' => '150px'],
                    'format' => 'datetime'
                ]
            ],
        ]);
        echo '<hr>';
    }
    ?>
    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 如果你认为题目存在歧义，可以在这里提出。</div>
</div>

<?= GridView::widget([
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
    'dataProvider' => $clarifies,
    'rowOptions' => function ($model, $key, $index, $grid) {
        return ['class' => 'animate__animated animate__fadeInUp'];
    },
    'columns' => [
        [
            'attribute' => 'Who',
            'label' => Yii::t('app', 'Who'),
            'value' => function ($model, $key, $index, $column) {
                return Html::a($model->user->colorname, ['/user/view', 'id' => $model->user->id]);
            },
            'format' => 'raw'
        ],
        [
            'attribute' => 'title',
            'value' => function ($model, $key, $index, $column) {
                return Html::a(Html::encode($model->title), [
                    '/contest/clarify',
                    'id' => $model->entity_id,
                    'cid' => $model->id
                ], ['data-pjax' => 0]);
            },
            'format' => 'raw'
        ],
        'created_at',
        'updated_at'
    ]
]); ?>


<?php if ($model->getRunStatus() == \app\models\Contest::STATUS_RUNNING) : ?>

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($newClarify, 'title', [
            'template' => "{label}\n<div class=\"input-group\"><span class=\"input-group-addon\">" . Yii::t('app', 'Title') . "</span>{input}</div>{error}",
        ])->textInput(['maxlength' => 128, 'autocomplete' => 'off'])->label(false) ?>

        <?= $form->field($newClarify, 'content')->widget(Yii::$app->setting->get('ojEditor')); ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>
        </div>
        <?php ActiveForm::end(); ?>
  
<?php else : ?>
    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> <?= Yii::t('app', 'The contest has ended.') ?></div>
<?php endif; ?>

</div>