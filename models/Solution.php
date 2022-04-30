<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%solution}}".
 *
 * @property int $id
 * @property int $problem_id
 * @property int $created_by
 * @property int $time
 * @property int $memory
 * @property string $created_at
 * @property string $source
 * @property int $result
 * @property int $language
 * @property int $contest_id
 * @property int $status
 * @property int $code_length
 * @property string $judgetime
 * @property string $pass_info
 * @property int $score
 * @property string $judge
 */
class Solution extends ActiveRecord
{
    const STATUS_HIDDEN = 0;
    const STATUS_VISIBLE = 1;
    const STATUS_TEST = 2;

    /**
     * 是这个值或小于这个值表示处于等待测评状态
     */
    const OJ_WAITING_STATUS = 3;

    /**
     * OJ 测评状态
     * @see Solution::getResultList()
     */
    const OJ_WT0 = 0;
    const OJ_WT1 = 1;
    const OJ_CI  = 2;
    const OJ_RI  = 3;
    const OJ_AC  = 4;
    const OJ_PE  = 5;
    const OJ_WA  = 6;
    const OJ_TL  = 7;
    const OJ_ML  = 8;
    const OJ_OL  = 9;
    const OJ_RE  = 10;
    const OJ_CE  = 11;
    const OJ_SE  = 12;
    const OJ_NT  = 13;

    const CLANG = 0;
    const CPPLANG = 1;
    const JAVALANG = 2;
    const PYLANG = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%solution}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => $this->timeStampBehavior(false),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['problem_id', 'created_by', 'time', 'memory', 'result', 'language', 'contest_id', 'status',
              'code_length', 'score'], 'integer'],
            [['created_at', 'judgetime'], 'safe'],
            [['language', 'source'], 'required'],
            [['language'], 'in', 'range' => [0, 1, 2, 3], 'message' => 'Please select a language'],
            [['source', 'pass_info'], 'string'],
            [['judge'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Run ID'),
            'problem_id' => Yii::t('app', 'Problem ID'),
            'created_by' => Yii::t('app', 'Created By'),
            'time' => Yii::t('app', 'Time'),
            'memory' => Yii::t('app', 'Memory'),
            'created_at' => Yii::t('app', 'Submit Time'),
            'source' => Yii::t('app', 'Code'),
            'result' => Yii::t('app', 'Result'),
            'language' => Yii::t('app', 'Language'),
            'contest_id' => Yii::t('app', 'Contest ID'),
            'status' => Yii::t('app', 'Status'),
            'code_length' => Yii::t('app', 'Code Length'),
            'judgetime' => Yii::t('app', 'Judgetime'),
            'pass_info' => Yii::t('app', 'Pass Info'),
            'judge' => Yii::t('app', 'Judge'),
            'score' => Yii::t('app', 'Score'),
            'who' => Yii::t('app', 'Who')
        ];
    }

    /**
     * @inheritdoc
     * @return SolutionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SolutionQuery(get_called_class());
    }

    /**
     * This is invoked before the record is saved.
     * @return boolean whether the record should be saved.
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_by = Yii::$app->user->id;
                $this->code_length = strlen($this->source);
            }
            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($this->language != Yii::$app->user->identity->language) {
            User::setLanguage($this->language);
        }
    }

    public function getTestCount()
    {
        return intval(substr(strstr($this->pass_info,'/'), 1));
    }

    public function getPassedTestCount()
    {
        return intval(strstr($this->pass_info,'/', true));
    }

    public function getLang()
    {
        switch ($this->language) {
            case Solution::CLANG:
                $res = 'C';
                break;
            case Solution::CPPLANG:
                $res = 'C++';
                break;
            case Solution::JAVALANG:
                $res = 'Java';
                break;
            case Solution::PYLANG:
                $res = 'Python3';
                break;
            default:
                $res = 'not set';
                break;
        }
        return $res;
    }

    /**
     * 获取语言对应的文件后缀
     *
     * @return string
     */
    public static function getLangFileExtension($lang)
    {
        switch ($lang) {
            case Solution::CLANG:
                $res = 'c';
                break;
            case Solution::CPPLANG:
                $res = 'cpp';
                break;
            case Solution::JAVALANG:
                $res = 'java';
                break;
            case Solution::PYLANG:
                $res = 'py';
                break;
            default:
                $res = 'txt';
                break;
        }
        return $res;
    }

    public function getResult()
    {
        $res = self::getResultList($this->result);
        $loadingImgUrl = Yii::getAlias('@web/images/loading.gif');

        if ($this->result <= Solution::OJ_WAITING_STATUS) {
            $waitingHtmlDom = 'waiting="true"';
            $loadingImg = "<img src=\"{$loadingImgUrl}\">";
        } else {
            $waitingHtmlDom = 'waiting="false"';
            $loadingImg = "";
        }
        $innerHtml =  'data-verdict="' . $this->result . '" data-submissionid="' . $this->id . '" ' . $waitingHtmlDom;

        // 定义各个测评状态的颜色
        // https://v3.bootcss.com/css/#helper-classes
        $cssClass = [
            "text-muted", // Pending
            "text-muted",
            "text-muted",
            "text-muted",
            "text-success", // AC
            "text-warning", // PE
            "text-danger",  // WA
            "text-warning", // TLE
            "text-warning", // MLE
            "text-warning", // OLE
            "text-warning", // RE
            "text-warning", // CE
            "text-danger",  // SE
            "text-danger", // No Test Data
        ];
        return "<strong class=" . $cssClass[$this->result] . " $innerHtml>{$res}{$loadingImg}</strong>";
    }

    public static function getResultList($res = '')
    {
        $results = [
            '' => Yii::t('app', 'Please select'),
            Solution::OJ_WT0 => Yii::t('app', 'Pending'),
            Solution::OJ_WT1 => Yii::t('app', 'Pending Rejudge'),
            Solution::OJ_CI => Yii::t('app', 'Compiling'),
            Solution::OJ_RI => Yii::t('app', 'Running & Judging'),
            Solution::OJ_AC => Yii::t('app', 'Accepted'),
            Solution::OJ_PE => Yii::t('app', 'Presentation Error'),
            Solution::OJ_WA => Yii::t('app', 'Wrong Answer'),
            Solution::OJ_TL => Yii::t('app', 'Time Limit Exceeded'),
            Solution::OJ_ML => Yii::t('app', 'Memory Limit Exceeded'),
            Solution::OJ_OL => Yii::t('app', 'Output Limit Exceeded'),
            Solution::OJ_RE => Yii::t('app', 'Runtime Error'),
            Solution::OJ_CE => Yii::t('app', 'Compile Error'),
            Solution::OJ_SE => Yii::t('app', 'System Error'),
            Solution::OJ_NT => Yii::t('app', 'No Test Data')
        ];
        return $res === '' ? $results : $results[$res];
    }

    public static function getLanguageList($status = '')
    {
        $arr = [
            '' => Yii::t('app', 'Please select'),
            '0' => 'C',
            '1' => 'C++',
            '2' => 'Java',
            '3' => 'Python3'
        ];
        return $status === '' ? $arr : $arr[$status];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getProblem()
    {
        return $this->hasOne(Problem::className(), ['id' => 'problem_id']);
    }

    public function getUsername()
    {
        return $this->user->username;
    }

    public function getSolutionInfo()
    {
        return $this->hasOne(SolutionInfo::className(), ['solution_id' => 'id']);
    }

    public function getContestProblem()
    {
        return $this->hasOne(ContestProblem::className(), ['problem_id' => 'problem_id']);
    }

    public function getProblemInContest()
    {
        return $this->contestProblem;
    }

    /**
     * OI 比赛模式，用户是否有权限查看过题情况
     */
    public function canViewResult()
    {
        //游客不能查看
        if (Yii::$app->user->isGuest){
            return false;
        }
        // 状态可见且设置了分享状态可以查看。以下代码中 isShareCode 的说明参见后台设置页面。
        // 对于比赛中的提交， status 的值默认为 STATUS_HIDDEN，比赛结束时可以在后台设为 STATUS_VISIBLE 以供普通用户查看
        // 对于后台验题时的提交，status 的值为 STATUS_HIDDEN
        if ($this->status == Solution::STATUS_VISIBLE && Yii::$app->setting->get('isShareCode')) {
            return true;
        }
        // 管理员有权限查看
        if (Yii::$app->user->identity->role == User::ROLE_ADMIN) {
            return true;
        }

        // contest_id 为空，说明不是比赛模式
        if (empty($this->contest_id)) {
            return true;
        }

        $contest = self::getContestInfo($this->contest_id);

        // 作业模式无限制
        if ($contest['type'] == Contest::TYPE_HOMEWORK) {
            return true;
        }

        // 小组
        if ($contest['group_id'] && !Yii::$app->user->isGuest) {
            $role = Yii::$app->db->createCommand('SELECT role FROM {{%group_user}} WHERE user_id=:uid AND group_id=:gid', [
                ':uid' => Yii::$app->user->id,
                ':gid' => $contest['group_id']
            ])->queryScalar();
            // 小组管理员
            if ($role == GroupUser::ROLE_LEADER || $role == GroupUser::ROLE_MANAGER) {
                return true;
            }
            if ($role != GroupUser::ROLE_MEMBER) {
                return false;
            }
        }

        // OI 模式比赛结束时才可以看
        if ($contest['type'] != Contest::TYPE_OI || time() >= strtotime($contest['end_time'])) {
            return true;
        }
        return false;
    }

    /**
     * 用户是否有权限查看代码
     */
    public function canViewSource()
    {
        //游客不能查看
        if (Yii::$app->user->isGuest){
            return false;
        }
        // 提交代码的作者有权限查看
        if ($this->created_by == Yii::$app->user->id) {
            return true;
        }
        // 状态可见且设置了分享状态可以查看。以下代码中 isShareCode 的说明参见后台设置页面。
        // 对于比赛中的提交， status 的值默认为 STATUS_HIDDEN，比赛结束时可以在后台设为 STATUS_VISIBLE 以供普通用户查看
        // 对于后台验题时的提交，status 的值为 STATUS_HIDDEN
        if ($this->status == Solution::STATUS_VISIBLE && Yii::$app->setting->get('isShareCode')) {
            return true;
        }
        // 管理员有权限查看
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == User::ROLE_ADMIN) {
            return true;
        }
        if (!empty($this->contest_id)) {
            $contest = self::getContestInfo($this->contest_id);
            // 比赛结束都可以看
            if (time() >= strtotime($contest['end_time'])) {
                return true;
            }
            // 小组
            if ($contest['group_id'] && !Yii::$app->user->isGuest) {
                $role = Yii::$app->db->createCommand('SELECT role FROM {{%group_user}} WHERE user_id=:uid AND group_id=:gid', [
                    ':uid' => Yii::$app->user->id,
                    ':gid' => $contest['group_id']
                ])->queryScalar();
                // 小组管理员
                if ($role == GroupUser::ROLE_LEADER || $role == GroupUser::ROLE_MANAGER) {
                    return true;
                }
                if ($role != GroupUser::ROLE_MEMBER) {
                    return false;
                }
            }
        }
        return false;
    }

    public static function getContestInfo($contestID)
    {
        $key = 'status_' . $contestID;
        $cache = Yii::$app->cache;
        $contest = $cache->get($key);
        if ($contest === false) {
            $contest = Yii::$app->db->createCommand('SELECT `id`, `start_time`, `end_time`, `type`, `group_id` FROM  {{%contest}} WHERE id = :id', [
                ':id' => $contestID
            ])->queryOne();
            $cache->set($key, $contest, 60);
        }
        return $contest;
    }

    /**
     * 用户是否有权限可以查看错误信息
     */
    public function canViewErrorInfo()
    {

        //游客不能查看
        if (Yii::$app->user->isGuest){
            return false;
        }
        // 状态可见且设置了分享状态可以查看。以下代码中 isShareCode 的说明参见后台设置页面。
        // 对于比赛中的提交， status 的值默认为 STATUS_HIDDEN，比赛结束时可以在后台设为 STATUS_VISIBLE 以供普通用户查看
        // 对于后台验题时的提交，status 的值为 STATUS_HIDDEN
        if ($this->status == Solution::STATUS_VISIBLE && Yii::$app->setting->get('isShareCode')) {
            return true;
        }
        // 管理员有权限查看所有情况
        if (Yii::$app->user->identity->role == User::ROLE_ADMIN) {
            return true;
        }
        if (!empty($this->contest_id)) {
            $contest = self::getContestInfo($this->contest_id);
            // 比赛结束都可以看
            if (time() >= strtotime($contest['end_time'])) {
                return true;
            }

            // 作业模式无限制
            if ($contest['type'] == Contest::TYPE_HOMEWORK) {
                return true;
            }

            // 对于比赛中的提交，普通用户能查看自己的 Compile Error 所记录的信息
            if ($this->created_by == Yii::$app->user->id && $this->result == self::OJ_CE) {
                return true;
            }
        }
        //　非比赛中的提交，普通用户也能查看自己的出错信息
        if ($this->status == Solution::STATUS_VISIBLE && $this->created_by == Yii::$app->user->id) {
            return true;
        }
        return false;
    }
}
