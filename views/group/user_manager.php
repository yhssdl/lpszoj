<?php

use yii\helpers\Html;
use app\models\GroupUser;
use app\models\User;
/* @var $this yii\web\View */
/* @var $model app\models\Group */
/* @var $groupUser app\models\GroupUser */
?>

<h3>管理用户：<?= Html::a(Html::encode($groupUser->user->nickname), ['/group/view', 'id' => $groupUser->user->id]) ?></h3>
<hr>
<h4>当前角色：<?= $groupUser->getRole() ?></h4>
<br>
<div class="btn-group btn-group-justified">
    <?php if ($groupUser->role == GroupUser::ROLE_APPLICATION) : ?>
        <div class="btn-group">
            <?= Html::a('同意加入', ['/group/user-update', 'id' => $groupUser->id, 'role' => 1], ['class' => 'btn btn-success']); ?>
        </div>
        <div class="btn-group">
            <?= Html::a('拒绝加入', ['/group/user-update', 'id' => $groupUser->id, 'role' => 2], ['class' => 'btn btn-danger']); ?>
        </div>

    <?php elseif ($groupUser->role == GroupUser::ROLE_REUSE_INVITATION) : ?>
        <div class="btn-group">
            <?= Html::a('重新邀请', ['/group/user-update', 'id' => $groupUser->id, 'role' => 3], ['class' => 'btn btn-default']); ?>
        </div>
    <?php elseif ($groupUser->role == GroupUser::ROLE_MEMBER && $model->getRole() == GroupUser::ROLE_LEADER) : ?>
        <div class="btn-group">
            <?= Html::a('设为助理', ['/group/user-update', 'id' => $groupUser->id, 'role' => 4], ['class' => 'btn btn-default']); ?>
        </div>
    <?php elseif ($groupUser->role == GroupUser::ROLE_MANAGER && $model->getRole() == GroupUser::ROLE_LEADER) : ?>
        <div class="btn-group">
            <?= Html::a('设为普通成员', ['/group/user-update', 'id' => $groupUser->id, 'role' => 5], ['class' => 'btn btn-default']); ?>
        </div>
    <?php endif; ?>

    <?php if ($groupUser->user->role==User::ROLE_USER && ($groupUser->role == GroupUser::ROLE_MEMBER && $model->getRole() == GroupUser::ROLE_LEADER && Yii::$app->setting->get('isGroupReset') != 0)
        || ($groupUser->role == GroupUser::ROLE_MEMBER && $model->getRole() == GroupUser::ROLE_MANAGER && Yii::$app->setting->get('isGroupReset') == 2)
    ) : ?>

        <div class="btn-group">
            <?= Html::a('重置密码', ['/group/user-update', 'id' => $groupUser->id, 'role' => 6], ['class' => 'btn btn-default']); ?>
        </div>
        <div class="btn-group">
            <?= Html::a('重置昵称', ['/group/user-update', 'id' => $groupUser->id, 'role' => 7], ['class' => 'btn btn-default']); ?>
        </div>
    <?php endif; ?>
</div>