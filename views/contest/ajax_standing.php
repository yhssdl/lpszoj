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
?>

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
