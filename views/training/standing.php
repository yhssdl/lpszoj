<?php

use app\models\Contest;
use yii\helpers\Html;
use yii\web\view;


/* @var $this yii\web\View */
/* @var $model app\models\Contest */
/* @var $form yii\widgets\ActiveForm */
/* @var $showStandingBeforeEnd bool */
/* @var $rankResult array */

$this->title = $model->title;
$this->params['model'] = $model;
$js = <<<EOT
function submission_click(obj) {
    $.ajax({
        url: $(obj).attr('data-href'),
        type:'post',
        error: function(){alert('error');},
        success:function(html){
            $('#submission-content').html(html);
            $('#submission-info').modal('show');
        }
    });   
}
EOT;
$this->registerJs($js,View::POS_HEAD);

?>
<div class="contest-overview">
    <div class="legend-strip">
        <div class="pull-right table-legend">
            <div>
                <span class="solved-first legend-status"></span>
                <p class="legend-label"> <?= Yii::t('app', 'First to solve problem') ?></p>
            </div>
            <div>
                <span class="solved legend-status"></span>
                <p class="legend-label"> <?= Yii::t('app', 'Solved problem') ?></p>
            </div>
            <div>
                <span class="attempted legend-status"></span>
                <p class="legend-label"> <?= Yii::t('app', 'Attempted problem') ?></p>
            </div>
            <div>
                <span class="pending legend-status"></span>
                <p class="legend-label"> <?= Yii::t('app', 'Pending judgement') ?></p>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
    <div id="main_body" class="table-responsive">
        <?php
            echo $this->render('_standing', [
                'model' => $model,
                'pages' => $pages,
                'autoRefresh' => false,
                'rankResult' => $rankResult
            ]);
        ?>
    </div>
</div>