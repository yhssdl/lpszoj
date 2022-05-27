<?php

namespace app\models;

use Yii;

/**
 * This is the model class for Homework.
 */
class Homework extends Contest
{
    /**
     * 作业的发布状态：草稿、已发布
     */
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_PRIVATE = 2;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'start_time', 'end_time'], 'required'],
            [['description', 'editorial', 'invite_code', 'ext_link'], 'string'],
            [['created_by'], 'integer'],
            [['start_time', 'end_time', 'lock_board_time'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['id', 'status', 'type', 'scenario', 'created_by', 'group_id','language','enable_clarify', 'enable_clarify', 'enable_board'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'start_time' => Yii::t('app', 'Start Time'),
            'end_time' => Yii::t('app', 'End Time'),
            'status' => Yii::t('app', 'Status'),
            'type' => Yii::t('app', 'Type'),
            'created_by' => Yii::t('app', 'Created By'),
            'description' => Yii::t('app', 'Description'),
            'editorial' => Yii::t('app', 'Editorial'),
            'lock_board_time' => Yii::t('app', 'Lock Board Time'),
            'language' => Yii::t('app', 'Language'),
            'enable_clarify' => Yii::t('app', 'Clarification'),
            'enable_board' => Yii::t('app','Enable Board'),
            'ext_link' => '站外比赛',
            'invite_code' => '邀请码（仅站外比赛有效）',            
        ];
    }

    /**
     * 判断是否有管理、编辑该作业的权限
     */
    public function hasPermission()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        // 不是作业
        if ($this->group_id == 0) {
            return false;
        }
        // 创建人
        if ($this->created_by == Yii::$app->user->id) {
            return true;
        }
        if ($this->group->hasPermission()) {
            return true;
        }
        return false;
    }
}
