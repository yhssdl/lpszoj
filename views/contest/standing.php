<?php

use app\models\Contest;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Contest */
/* @var $form yii\widgets\ActiveForm */
/* @var $showStandingBeforeEnd bool */
/* @var $rankResult array */

$this->title = $model->title;
$this->params['model'] = $model;
if(isset($_COOKIE['autoRefresh']))
    $autoRefresh = $_COOKIE['autoRefresh'];
else 
    $autoRefresh = 1;

$js = <<<EOT
$(".toggle-show-contest-standing input[name='showStandingBeforeEnd']").change(function () {
    $(".toggle-show-contest-standing").submit();
});
$("#autoRefresh").click(function () {
    var expires = new Date();
    expires.setTime(expires.getTime() + 3650 * 30 * 24 * 60 * 60 * 1000);
    if ($(this).prop("checked")) {
        document.cookie = "autoRefresh=1;expires=" + expires.toGMTString();
    } else {
        document.cookie = "autoRefresh=0;expires=" + expires.toGMTString();
    }
    window.location.reload();
});
EOT;
$this->registerJs($js);
if ($autoRefresh) {
    $js = <<<EOT
    function refreshPage() {
        window.location.reload();
    }
    setInterval(refreshPage, 3000);
    EOT;
    $this->registerJs($js);
}

?>
<div class="contest-overview">
    <?php if ($model->type != Contest::TYPE_OI || $model->isContestEnd()) : ?>
        <div class="legend-strip">
            <?php if ($model->isContestEnd()) : ?>
                <?= Html::beginForm(
                    ['/contest/standing', 'id' => $model->id],
                    'get',
                    ['class' => 'toggle-show-contest-standing pull-left', 'style' => 'margin-top: 6px;']
                ); ?>
                <div class="checkbox">
                    <label>
                        <?php if ($showStandingBeforeEnd) : ?>
                            <?= Html::hiddenInput('showStandingBeforeEnd', 0) ?>
                        <?php endif; ?>
                        <?= Html::checkbox('showStandingBeforeEnd', $showStandingBeforeEnd) ?>
                        显示比赛期间榜单
                    </label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label>
                        <?= Html::checkbox('autoRefresh', $autoRefresh, ['id' => 'autoRefresh']) ?>
                        自动刷新
                    </label>    
                </div>
                <?= Html::endForm(); ?>
            <?php else: ?>
                <div class="pull-left" style="margin-top: 6px;">
                    <div class="checkbox">
                        <label>
                            <?= Html::checkbox('autoRefresh', $autoRefresh, ['id' => 'autoRefresh']) ?>
                            自动刷新
                        </label>    
                    </div>
                </div>
            <?php endif; ?>   
       
            <div class="pull-right table-legend">
                <?php if ($model->type != Contest::TYPE_OI && $model->type != Contest::TYPE_IOI) : ?>
                    <div>
                        <span class="solved-first legend-status"></span>
                        <p class="legend-label"> <?= Yii::t('app', 'First to solve problem') ?></p>
                    </div>
                    <div>
                        <span class="solved legend-status"></span>
                        <p class="legend-label"> <?= Yii::t('app', 'Solved problem') ?></p>
                    </div>
                <?php else : ?>
                    <div>
                        <span class="solved-first legend-status"></span>
                        <p class="legend-label"> <?= Yii::t('app', 'All correct') ?></p>
                    </div>
                    <div>
                        <span class="solved legend-status"></span>
                        <p class="legend-label"> <?= Yii::t('app', 'Partially correct') ?></p>
                    </div>
                <?php endif; ?>
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
    <?php endif; ?>
    <div class="clearfix"></div>
    <div class="table-responsive">
        <?php
        if ($model->type == $model::TYPE_RANK_SINGLE) {
            echo $this->render('_standing_single', [
                'model' => $model,
                'pages' => $pages,
                'showStandingBeforeEnd' => $showStandingBeforeEnd,
                'autoRefresh' => $autoRefresh,
                'rankResult' => $rankResult
            ]);
        } else if ($model->type == $model::TYPE_OI || $model->type == $model::TYPE_IOI) {
            echo $this->render('_standing_oi', [
                'model' => $model,
                'pages' => $pages,
                'showStandingBeforeEnd' => $showStandingBeforeEnd,
                'autoRefresh' => $autoRefresh,
                'rankResult' => $rankResult
            ]);
        } else {
            echo $this->render('_standing_group', [
                'model' => $model,
                'pages' => $pages,
                'showStandingBeforeEnd' => $showStandingBeforeEnd,
                'autoRefresh' => $autoRefresh,
                'rankResult' => $rankResult
            ]);
        }
        ?>
    </div>
</div>