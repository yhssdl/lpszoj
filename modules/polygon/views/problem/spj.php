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
<div class="alert alert-light"><i class="fa fa-info-circle"></i> 如果该题目需要特判的，请在下面填写特判程序。参考：<?= Html::a('如何编写特判程序？', ['/wiki/spj']) ?></div>


<?php if ($model->spj): ?>
    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'spj_lang')->textInput([
        'maxlength' => true, 'value' => 'C、C++', 'disabled' => true
    ])->hint('<i class="fa fa-info-circle"></i> 当前仅支持 C\C++ 语言。')->label(false) ?>

    <?= $form->field($model, 'spj_source')->widget('app\widgets\codemirror\CodeMirror')->label(false); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-block']) ?>
    </div>
    <?php ActiveForm::end(); ?>
<?php else: ?>
    <div class="alert alert-light"><i class="fa fa-info-circle"></i> 当前题目不是 SPJ 判题，如需启用 SPJ 判题，请先到题目信息编辑页面将 Special Judge 改为是?></div>
<?php endif; ?>
