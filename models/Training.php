<?php

namespace app\models;

use Yii;

/**
 * This is the model class for Homework.
 */
class Training extends Contest
{

  
    /**
     * 是否有权限访问。用于限制比赛信息、问题、提交队列、榜单、答疑内容的访问，仅供管理员、参赛用户或比赛结束才能访问
     */
    public function canView()
    {

        $isAdmin = !Yii::$app->user->isGuest && Yii::$app->user->identity->role == User::ROLE_ADMIN;
        $isAuthor = !Yii::$app->user->isGuest && $this->created_by == Yii::$app->user->id;
        // 管理员或者创建人
        if ($isAdmin || $isAuthor) {
            return true;
        }

        // 参赛用户
        if ($this->isUserInContest()) {
            return true;
        }
        // 小组成员
        if ($this->group_id != 0) {
            $role = Yii::$app->db->createCommand('SELECT role FROM {{%group_user}} WHERE user_id=:uid AND group_id=:gid', [
                ':uid' => Yii::$app->user->id,
                ':gid' => $this->group_id
            ])->queryScalar();
            if ($role == GroupUser::ROLE_MEMBER || $role == GroupUser::ROLE_MANAGER || $role == GroupUser::ROLE_LEADER) {
                return true;
            }
        }
        return false;
    }
}
