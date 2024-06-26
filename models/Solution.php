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
 * @property string $ip
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
            [['source', 'pass_info', 'ip'], 'string'],
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
            'who' => Yii::t('app', 'Who'),
            'ip' => Yii::t('app', 'IP')
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
        $res =  self::getResultList($this->result);
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
        return "<span class=" . $cssClass[$this->result] . " $innerHtml>{$res}{$loadingImg}</span>";
    }

    public static function getResultCssClass($res = 0)
    {
        $cssClass = [
            Solution::OJ_WT0 => "text-muted", // Pending
            Solution::OJ_WT1 =>"text-muted",
            Solution::OJ_CI =>"text-muted",
            Solution::OJ_RI =>"text-muted",
            Solution::OJ_AC =>"text-success", // AC
            Solution::OJ_PE =>"text-warning", // PE
            Solution::OJ_WA =>"text-danger",  // WA
            Solution::OJ_TL =>"text-warning", // TLE
            Solution::OJ_ML =>"text-warning", // MLE
            Solution::OJ_OL =>"text-warning", // OLE
            Solution::OJ_RE =>"text-warning", // RE
            Solution::OJ_CE =>"text-warning", // CE
            Solution::OJ_SE =>"text-danger",  // SE
            Solution::OJ_NT =>"text-danger", // No Test Data
        ];
        return $cssClass[$res];
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
     * 用户是否有权限查看代码
     */
    public function canViewSource()
    {

        //游客不能查看
        if (Yii::$app->user->isGuest){
            return false;
        }

        //用户可查看其它用户代码
        if(Yii::$app->setting->get('isShareCode')==0){
            return true;
        }

        // 管理员有权限查看
        if (Yii::$app->user->identity->role == User::ROLE_ADMIN) {
            return true;
        }

        // 提交代码的作者,在前2个选项时,可查看.
        if ($this->created_by == Yii::$app->user->id  && Yii::$app->setting->get('isShareCode')<=1) {
            return true;
        }

        // 管理教师,在只有在不为3选项时,可查看.
        if (Yii::$app->user->identity->role == User::ROLE_TEACHER  && Yii::$app->setting->get('isShareCode')!=3) {
            return true;
        } 

        if (!empty($this->contest_id) && Yii::$app->setting->get('isShareCode')!=3) {
            $contest = self::getContestInfo($this->contest_id);

            // 小组
            if ($contest['group_id']) {
                $role = Yii::$app->db->createCommand('SELECT role FROM {{%group_user}} WHERE user_id=:uid AND group_id=:gid', [
                    ':uid' => Yii::$app->user->id,
                    ':gid' => $contest['group_id']
                ])->queryScalar();

                // 小组管理员
                if (($role == GroupUser::ROLE_LEADER || $role == GroupUser::ROLE_MANAGER)) {
                    //只要不是只有管理员可查看选项,小组组长可以看过题情况.
                    return true;
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
     * OI 比赛模式，用户是否有权限查看过题情况
     */
    public function canViewResult()
    {
        //游客不能查看
        if (Yii::$app->user->isGuest){
            return false;
        }

        // 管理员有权限查看
        if (Yii::$app->user->identity->role == User::ROLE_ADMIN) {
            return true;
        }

        // 提交代码的作者,在前2个选项时,可查看过题情况.
        if ($this->created_by == Yii::$app->user->id  && Yii::$app->setting->get('isShowError')<=1) {
            return true;
        }

        // 管理教师,在只有在不为3选项时,可查看.
        if (Yii::$app->user->identity->role == User::ROLE_TEACHER  && Yii::$app->setting->get('isShowError')!=3) {
            return true;
        } 

        if (!empty($this->contest_id) && Yii::$app->setting->get('isShowError')!=3) {
            $contest = self::getContestInfo($this->contest_id);

            // 小组
            if ($contest['group_id']) {
                $role = Yii::$app->db->createCommand('SELECT role FROM {{%group_user}} WHERE user_id=:uid AND group_id=:gid', [
                    ':uid' => Yii::$app->user->id,
                    ':gid' => $contest['group_id']
                ])->queryScalar();

                // 小组管理员
                if (($role == GroupUser::ROLE_LEADER || $role == GroupUser::ROLE_MANAGER)) {
                    //只要不是只有管理员可查看选项,小组组长可以看过题情况.
                    return true;
                }
            }
        }
        return false;
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

        // 管理员有权限查看
        if (Yii::$app->user->identity->role == User::ROLE_ADMIN) {
            return true;
        }
        
        // 管理教师,在只有在不为3选项时,可查看.
        if (Yii::$app->user->identity->role == User::ROLE_TEACHER  && Yii::$app->setting->get('isShowError')!=3) {
            return true;
        } 

        if ($this->created_by == Yii::$app->user->id){
            // 提交代码的作者,在前１个选项时,可查看错误数据情况.
            if(Yii::$app->setting->get('isShowError')<1){
                  return true;
            }

           // 对于比赛中的提交，普通用户能查看自己的 Compile Error 所记录的信息
            if ($this->result == self::OJ_CE) {
                return true;
            }    
        }

        if (!empty($this->contest_id) && Yii::$app->setting->get('isShowError')!=3) {
            $contest = self::getContestInfo($this->contest_id);

            // 小组
            if ($contest['group_id']) {
                $role = Yii::$app->db->createCommand('SELECT role FROM {{%group_user}} WHERE user_id=:uid AND group_id=:gid', [
                    ':uid' => Yii::$app->user->id,
                    ':gid' => $contest['group_id']
                ])->queryScalar();

                // 小组管理员
                if (($role == GroupUser::ROLE_LEADER || $role == GroupUser::ROLE_MANAGER)) {
                    //只要不是只有管理员可查看选项,小组组长可以看过题情况.
                    return true;
                }
            }
        }
        return false;
    }


    /**获取客户端ip 
     * @return string 
     */  
    public function getClientIp ()  
    {  
        if (getenv('HTTP_CLIENT_IP')) {  
            $ip = getenv('HTTP_CLIENT_IP');  
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {  
            $ip = getenv('HTTP_X_FORWARDED_FOR');  
        } else if (getenv('REMOTE_ADDR')) {  
            $ip = getenv('REMOTE_ADDR');  
        } else {  
            $ip = $_SERVER['REMOTE_ADDR'];  
        }  
        return $ip;  
    }


    public static function testHtml($model,$id, $caseJsonObject)
    {
        $isAdmin = false;
        //管理员有权限查看所有情况
        if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == User::ROLE_ADMIN){
            $isAdmin = true;
        }
        
        $html_str = "";
        if ($caseJsonObject->verdict == Solution::OJ_AC) {
            $html_str = $html_str . '<div class="panel panel-default test-for-popup"> 
            <div class="panel-heading" style="background-color:#0000" role="tab" id="heading' . $id . '"> 
            <span class="text-success">  
                        测试点' . $id . ' 
                        : ' . Solution::getResultList($caseJsonObject->verdict) . ', 
                        时间: ' . $caseJsonObject->time . ' 毫秒, 
                        内存: ' . $caseJsonObject->memory . ' KB 
                    </span> 
            </div></div>';
        } else {
            $html_str = $html_str .  '<div class="panel panel-default test-for-popup"><div class="panel-heading" style="background-color:#0000" role="tab" id="heading' . $id . '">';
            if ($model->canViewErrorInfo()){
                $html_str = $html_str .  '<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                      href="#test-' . $id . '" aria-expanded="false" aria-controls="test-' . $id . '"><span class=" text-danger">';
            }
            else{
                $html_str = $html_str .  '<span class="text-danger">';
            }
    
            $html_str = $html_str .  '测试点' . $id . ': ' . Solution::getResultList($caseJsonObject->verdict) . ', 
                    时间: ' . $caseJsonObject->time . ' 毫秒,内存: ' . $caseJsonObject->memory . ' KB </span>';
            if ($model->canViewErrorInfo()) $html_str = $html_str .  '</a>';
            $html_str = $html_str .  '</div>';
    
            if ($model->canViewErrorInfo()){
    
                $html_str = $html_str .  '<div id="test-' . $id . '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading' . $id . '">
                    <div class="panel-body">
                        <div class="sample-test">
                            <div class="input">
                                <b>&nbsp;输入数据</b>
                                <pre>' . $caseJsonObject->input . '</pre>
                            </div>
                            <div class="output">
                                <b>&nbsp;标准答案</b>
                                <pre>' . $caseJsonObject->output . '</pre>
                            </div>
                            <div class="output">
                                <b>&nbsp;你的答案</b>
                                <pre>' . $caseJsonObject->user_output . '</pre>
                            </div>
                            <div class="output">
                                <b>&nbsp;检查日志</b>
                                <pre>' . $caseJsonObject->checker_log . '</pre>
                            </div>
                            <div class="output">
                                <b>&nbsp;系统信息</b>
                                <pre>exit code: ' . $caseJsonObject->exit_code . ', checker exit code: ' . $caseJsonObject->checker_exit_code . '</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
            }
        }
        return $html_str;
    }

    public static function  subtaskHtml($id, $score, $verdict,$subtask_body)
    {
        $scoregot = $score;
        $csscolor = 'panel-success';
        if ($verdict != 4) {
          $scoregot = 0;
          $csscolor = 'panel-warning';
        }
        return '<div class="panel ' . $csscolor . ' test-for-popup">
              <div class="panel-heading" role="tab" id="subtask-heading-' . $id . '">
                  <h4 class="panel-title">
                      <a role="button" data-toggle="collapse"
                          href="#subtask-' . $id . '" aria-expanded="false" aria-controls="subtask-' . $id . '">
                          子任务 #' . $id . ', 分数: ' . $score . ', 得分: ' . $scoregot . '
                      </a> 
                  </h4> 
              </div> 
              <div id="subtask-' . $id . '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="subtask-heading-' . $id . '"> 
                  <div id="subtask-body-' . $id . '" class="panel-body">' . $subtask_body . 
                  '</div>
              </div>
          </div>';
    }    
    
}
