<?php

use yii\helpers\Html;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\Problem */
/* @var $solution app\models\Solution */
/* @var $submissions array */

$this->title = $model->id . ' - ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Problems'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/problem/view', 'id' => $model->id]];
?>
<div class="solution-view">
    <div class="row">
        <div class="col-md-12">
            <?php 
                $bShow  = false;
                if (Yii::$app->setting->get('isEnableShowSolution')==1 && ($model->show_solution || $model->isSolved())){
                    $bShow  = true;  
                } else if( !Yii::$app->user->isGuest && ( (Yii::$app->setting->get('isAdminShowSolution')==1 && Yii::$app->user->identity->role >= User::ROLE_TEACHER) || (Yii::$app->setting->get('isAdminShowSolution')==2 && Yii::$app->user->identity->role == User::ROLE_ADMIN)) ) {
                    $bShow  = true;  
                }
            ?>
            <div class="news-content">   
            <?php if($bShow):?>
                <?= Yii::$app->formatter->asMarkdown($model->solution) ?>
            <?php else: ?>
                <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 没有权限查看解题。</div>
            <?php endif;?>

            </div>
        </div>
    </div>
</div>
<?php
$js = <<<EOF
    var obj = $("#modal-iframe",parent.document);

    $(window).resize(function(){
        obj.height(document.body.scrollHeight);
     });
     obj.height(document.body.scrollHeight);
   
EOF;
$this->registerJs($js);
?>