<?php

use app\models\User;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Group */

$this->title = Yii::t('app', 'Create Group');

$DefGp = false;
if (!Yii::$app->user->isGuest){
    if (Yii::$app->setting->get('isDefGroup') == 1) {
        $DefGp = true;
    } elseif ((Yii::$app->setting->get('isDefGroup') == 2) && (Yii::$app->user->identity->role === User::ROLE_ADMIN)) {
        $DefGp = true;
    } elseif (Yii::$app->setting->get('isDefGroup') == 3 && Yii::$app->user->identity->role >= User::ROLE_VIP) {
        $DefGp = true;
    } elseif (Yii::$app->setting->get('isDefGroup') == 4 && Yii::$app->user->identity->role >= User::ROLE_TEACHER) {
        $DefGp = true;
    }
}



?>
<div class="group-create">
    <?php if ($DefGp): ?>    
    <p class="lead"><?= Html::encode($this->title) ?></p>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
    <?php else: ?>
        <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 没有创建小组的权限！</div>
    <?php endif; ?>   
</div>
