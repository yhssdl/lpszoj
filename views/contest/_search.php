<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ContestSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'options' => [
        'class' => ''
    ],
]); ?>
<div class="row">

    <div class="col-lg-6">
        <?= $form->field($model, 'title')->textInput(['maxlength' => 128, 'autocomplete' => 'off', 'placeholder' => Yii::t('app', 'Title')])->label(false) ?>
     </div>

    <div class="col-lg-2">
        <?= $form->field($model, 'type', [
            'template' => "{label}\n<div class=\"input-group btn-group-justified\"><span style='width:30%' class=\"input-group-addon\">类型</span>{input}</div>",
        ])->dropDownList($model::getTypeList())->label(false) ?>
    </div>
    <div class="col-lg-2">
        <?= $form->field($model, 'status', [
            'template' => "{label}\n<div class=\"input-group btn-group-justified\"><span style='width:30%' class=\"input-group-addon\">状态</span>{input}</div>",
        ])->dropDownList($model::getRunStatusList())->label(false) ?>
    </div>

    <div class="col-lg-2">
        <div class="btn-group btn-block search-submit">
            <?= Html::submitButton('<i class="fa fa-search"></i> ' . Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-block']) ?>
        </div>
    </div>
 
</div>
<?php ActiveForm::end(); ?>