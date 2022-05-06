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
            <?php if ($model->ext_link) : ?>
                <li>
                    <?= Html::a('<span class="fa fa-share"></span> 外部比赛</sapn>', ['/contest/view', 'id' => $model->id], ['class' => 'contest-tag  status-not-start text-none-decoration']); ?>
                </li>
                <li>
                <a href="?ContestSearch%5Btype%5D=<?= $model->type ?>" class="contest-tag contest-tag-green text-none-decoration"><span class="fa fa-bar-chart"></span> <?= $model->getType() ?></a>
                </li>
                <?php if ($model->invite_code) : ?>
                    <li><span class="contest-tag  contest-tag-blue"><span class="fa fa-lock"></span> <?= $model->invite_code ?></sapn>
                    </li>
                <?php endif; ?>
            <?php else : ?>

                <li>
                    <a href="?ContestSearch%5Bstatus%5D=<?= $model->getRunStatus() ?>" class="contest-tag <?= $model->getRunStatus(2) ?> text-white text-none-decoration"><span class="fa fa-flag"></span> <?= $model->getRunStatus(1) ?></a>
                </li>
                <li>
                <a href="?ContestSearch%5Btype%5D=<?= $model->type ?>" class="contest-tag contest-tag-green text-none-decoration"><span class="fa fa-trophy"></span> <?= $model->getType() ?></a>
            </li>
                <?php if (!Yii::$app->user->isGuest && $model->isUserInContest()) : ?>

                    <li><span class="contest-tag  contest-tag-info"><span class="fa fa-check"></span> 已参赛</sapn>
                    </li>
                <?php endif; ?>
                <?php if ($model->invite_code) : ?>
                    <li><span class="contest-tag  contest-tag-blue"><span class="fa fa-lock"></span>需邀请码</sapn>
                    </li>
                <?php endif; ?>
                <li><span class="fa fa-clock-o text-blue"></span> 时长:<?= $model->getContestTimeLen() ?></li>
                <li><span class="fa fa-user"></span> 参与人数:<?= $model->getContestUserCount() ?></li>
                </li>
            <?php endif; ?>
        </ul>
    </div>

</div>