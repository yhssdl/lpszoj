<?php

use yii\helpers\Html;

$start_time = strtotime($model->start_time);
?>

<div class="section-body">
    <div class="media__left medium">
        <div class="contest__date numbox">
            <div class="numbox__num large"><?= date('d', $start_time) ?></div>
            <div class="numbox__text"><?= date('Y-m', $start_time) ?></div>
        </div>
    </div>
    <div class="media__body medium">
        <div class="contest__title"><?= Html::a(Html::encode($model->title), ['/contest/view', 'id' => $model->id], ['class' => 'text-dark']); ?></div>
        <ul class="supplementary list">
            <li><?= Html::a('<span class="fa fa-address-book"></span> 设置', ['/homework/update', 'id' => $model->id], ['class' => 'contest-tag  status-not-start text-none-decoration']) ?></li>
            <li>
                <span class="contest-tag <?= $model->getRunStatus(2) ?> text-white"><span class="fa fa-flag"></span> <?= $model->getRunStatus(1) ?></span>
            </li>
            <li>
                <span class="contest-tag contest-tag-green"><span class="fa fa-bar-chart"></span> <?= $model->getType() ?></span>
            </li>

            <?php if (!Yii::$app->user->isGuest && $model->isUserInContest()) : ?>
                <li><span class="contest-tag  contest-tag-info"><span class="glyphicon glyphicon-fa fa-check"></span> 已参赛</sapn>
                </li>
            <?php endif; ?>
            <?php if ($model->invite_code) : ?>
                <li><span class="contest-tag  contest-tag-blue"><span class="fa fa-lock"></span>需邀请码</sapn>
                </li>
            <?php endif; ?>
            <li><span class="fa fa-clock-o text-blue"></span> 时长:<?= $model->getContestTimeLen() ?></li>
            <li><span class="fa fa-user"></span> 参与人数:<?= $model->getContestUserCount() ?></li>
            </li>

        </ul>
    </div>

</div>