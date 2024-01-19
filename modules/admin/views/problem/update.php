<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Problem */

$this->title = Yii::t('app', $model->title);
$this->params['model'] = $model;
$previousProblemID = $model->getPreviousProblemID();
$nextProblemID = $model->getNextProblemID();
?>

<div class="problem-update">
    <div class="row">
        <div class="col-md-9 text-left">
            <div class="content-title"><?= $model->id . "：".Html::encode($this->title) ?></div>
        </div>
        <div class="col-md-3 text-right">
            <div class="btn btn-link">
                <?= Html::a(
                    '<i class="fa fa-arrow-left"></i>',
                    $previousProblemID ? ['update', 'id' => $previousProblemID] : 'javascript:void(0);',
                    ['title'=>'上一题','disabled' => !$previousProblemID]
                ) ?>
            </div>
            <div class="btn btn-link">
                <?= Html::a(
                '<i class="fa fa-arrow-right"></i>',
                $nextProblemID ? ['update', 'id' => $nextProblemID] : 'javascript:void(0);',
                ['title' => '下一题',  'disabled' => !$nextProblemID]
                ) ?>
            </div>      
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
