<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Problem */

$this->title = Yii::t('app', 'Import Problem');
$maxFileSize = min(ini_get("upload_max_filesize"), ini_get("post_max_size"));
?>
<p class="lead"><?= Html::encode($this->title) ?></p>
<div class="problem-import animate__animated animate__fadeInUp">

    <?php if (extension_loaded('xml')) : ?>

        <div class="alert alert-light">
        <i class="fa fa-info-circle"></i> 提交文件为 ZIP 或者 XML 格式，根据您的 PHP 设置，上传的文件无法大于 <?= $maxFileSize ?>。<br>&nbsp;&nbsp;&nbsp;&nbsp;<font color=red>温馨提示：</font>如果有多个XML题库文件，可以打包成zip文件一次性导入。
        </div>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <?= $form->field($model, 'problemFile', ['options' => ['class' => 'custom-file'], 'template' => '{label}{input}'])->fileInput(
            ['class' => 'custom-file-input']
        )->label(true, ['class' => 'custom-file-label', 'id' => 'myfile']) ?>
        <p></p>

        <div class="row"><div class="col-md-2 col-md-offset-5"><?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-success btn-block']) ?></div></div>

        <?php ActiveForm::end() ?>
    <?php else : ?>
        <div class="alert alert-light">
        <i class="fa fa-info-circle"></i> 服务器尚未开启 <code>php-xml</code> 扩展，请安装 <code>php-xml</code> 后再使用此功能。
        </div>
    <?php endif; ?>
</div>
<?php
$js = <<<EOF
$("#myfile").html("选择一个文件");

$("#uploadform-problemfile").on("change", function () {
    $("#myfile").html($(this).get(0).files[0].name);
});
EOF;
$this->registerJs($js);
?>