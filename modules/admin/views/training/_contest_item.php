<?php

use app\models\Group;
use yii\helpers\Html;

$g_model = Group::findOne($model->group_id);

if($g_model->logo_url){
    $pic = $g_model->logo_url;
}else{
    $pic = Yii::getAlias('@web') . '/images/task.png';   
}


$pCount = $model->getProblemCount();

?>

<div class="section-body">
<div class="media__left medium"><img class="group-img" src="<?= $pic ?>"></div>
    <div class="media__body medium">
        <div class="contest__title"><?= Html::a(Html::encode($model->title), ['section', 'id' => $model->id], ['class' => 'text-dark']); ?></div>
        <ul class="supplementary list">
            <li><?= Html::a('<span class="fa fa-pencil-square-o"></span> 编辑', ['section', 'id' => $model->id], ['class' => 'contest-tag  status-not-start text-none-decoration']) ?></li>

            <?php if($model->enable_clarify==0): ?>
                <li><?= Html::a('<span class="fa fa-eye-slash"></span> 未通过前隐藏后续小节', ['section', 'id' => $model->id], ['class' => 'contest-tag  status-ended text-none-decoration']) ?></li>
            <?php endif; ?>
            <li>
                <li><span class="fa fa-file-code-o text-blue"></span> 数量: <?= $pCount ?> 题</li>
            </li>

            <li>
                <li><span class="fa fa-flag text-blue"></span> 过关题数:<?= $model->punish_time<0 ? $pCount:$model->punish_time ?></li>
            </li>
            <li><span class="fa fa-user"></span> 参与人数:<?= $model->getContestUserCount() ?></li>
            </li>

        </ul>
    </div>

</div>