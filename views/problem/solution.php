<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Problem */
/* @var $solution app\models\Solution */
/* @var $submissions array */

$this->title = $model->id . ' - ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Problems'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/problem/view', 'id' => $model->id]];
?>
<div class="row">
    <div class="col-md-12">
        <?php 
            $bShow = $model->show_solution || ( $model->isSolved() && $model->show_solution==0);
        ?>

        <div class="news-content">   
        <?php if($bShow && Yii::$app->setting->get('isEnableShowSolution')): ?>
            <?= Yii::$app->formatter->asMarkdown($model->solution) ?>
        <?php else: ?>
            <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 没有权限查看解题。</div>
        <?php endif;?>

        </div>
    </div>
</div>
