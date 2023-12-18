<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Solution */

$this->title = $model->id;

if (!$model->canViewSource()) {
    return '暂无权限查看源码';
}
?>
<div class="solution-view">
    <div class="row">
        <div class="col-md-5">
            <p><?= Yii::t('app', 'Submit Time') ?>：<?= $model->created_at ?></p>
        </div>
        <div class="col-md-5">
            <p>运行 ID: <?= Html::a($model->id, ['/solution/detail', 'id' => $model->id]) ?></p>
        </div>
        <div class="col-md-2 text-right">
           <div><a type="button" class="btn btn-link" href='javaScript:void(0);' id='code_cpy' data-clipboard-target="#pre_code"><span class="fa fa-copy"></span></a></div>
        </div>
    </div>
    <div class="pre"><p id="pre_code"><?= Html::encode($model->source) ?></p></div>
</div>
<script type="text/javascript">
    (function ($) {
        $(document).ready(function () {
            $('.pre p').each(function(i, block) {  // use <pre><p>
                hljs.highlightBlock(block);
            });

        var clipboard = new ClipboardJS('#code_cpy');
        clipboard.on('success', function(e) {
            $('#code_cpy').text("已复制");
            setTimeout(function() {
                $('#code_cpy').html("<span class=\"fa fa-copy\"></span>");
            }, 500);
            e.clearSelection();
        });
        clipboard.on('error', function(e) {
            $('#code_cpy').text("复制失败");
            setTimeout(function() {
                $('#code_cpy').html("<span class=\"fa fa-copy\"></span>");
            }, 500);
            e.clearSelection();
        });
        })
    })(jQuery);
</script>