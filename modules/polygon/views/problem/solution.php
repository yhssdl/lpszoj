<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Solution;

/* @var $this yii\web\View */
/* @var $model app\modules\polygon\models\Problem */

$this->title = $model->title;
$this->params['model'] = $model;

$model->setSamples();
?>
<div class="animate__animated animate__fadeInUp">
<div class="alert alert-light"><i class="fa fa-info-circle"></i> 请在此页面提供一个“标程”（即解答该问题的正确代码程序）。它将被用来生成测试数据的标准输出</div>
<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'solution_lang')->dropDownList(Solution::getLanguageList())->label(false) ?>

    <?= $form->field($model, 'solution_source')->widget('app\widgets\codemirror\CodeMirror')->label(false); ?>

    <div class="form-group">
    <div class="row"><div class="col-md-2 col-md-offset-5"><?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?></div></div>
    </div>
<?php ActiveForm::end(); ?>
</div>
