<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "{{%problem}}".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $input
 * @property string $output
 * @property string $sample_input
 * @property string $sample_output
 * @property string $spj
 * @property string $hint
 * @property string $source
 * @property int $created_at
 * @property int $updated_at
 * @property int $time_limit
 * @property int $memory_limit
 * @property int $status
 * @property int $accepted
 * @property int $submit
 * @property int $solved
 * @property int $created_by
 * @property string $solution
 * @property string $tags
 * @property int polygon_problem_id
 */
class Problem extends ActiveRecord
{
    const STATUS_HIDDEN = 0;
    const STATUS_VISIBLE = 1;
    const STATUS_PRIVATE = 2;

    public $contest_id;
    public $test_status;

    public $sample_input_2;
    public $sample_output_2;
    public $sample_input_3;
    public $sample_output_3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%problem}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => $this->timeStampBehavior(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description', 'input', 'output', 'sample_input', 'sample_output', 'hint', 'test_status',
                'solution','tags'], 'string'],
            [['sample_input_2', 'sample_output_2', 'sample_input_3', 'sample_output_3', 'created_at',
              'updated_at', ], 'string'],
            [['id', 'time_limit', 'memory_limit', 'accepted', 'submit', 'solved', 'status', 'contest_id', 'created_by',
                'polygon_problem_id','show_solution'], 'integer'],
            [['title'], 'string', 'max' => 200],
            [['spj'], 'integer', 'max' => 1],
            [['source'], 'string', 'max' => 100],
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
            'description' => Yii::t('app', 'Description'),
            'input' => Yii::t('app', 'Input'),
            'output' => Yii::t('app', 'Output'),
            'sample_input' => Yii::t('app', 'Sample Input'),
            'sample_output' => Yii::t('app', 'Sample Output'),
            'sample_input_2' => Yii::t('app', 'Sample Input 2'),
            'sample_output_2' => Yii::t('app', 'Sample Output 2'),
            'sample_input_3' => Yii::t('app', 'Sample Input 3'),
            'sample_output_3' => Yii::t('app', 'Sample Output 3'),
            'spj' => Yii::t('app', 'Special Judge'),
            'hint' => Yii::t('app', 'Hint'),
            'source' => Yii::t('app', 'Source'),
            'created_at' => Yii::t('app', 'Created At'),
            'time_limit' => Yii::t('app', 'Time Limit'),
            'memory_limit' => Yii::t('app', 'Memory Limit'),
            'status' => Yii::t('app', 'Status'),
            'accepted' => Yii::t('app', 'Accepted'),
            'submit' => Yii::t('app', 'Submit'),
            'solved' => Yii::t('app', 'Solved'),
            'problem_data' => Yii::t('app', 'Problem Data'),
            'test_status' => Yii::t('app', 'Test Status'),
            'tags' => Yii::t('app', 'Tags'),
            'created_by' => Yii::t('app', 'Created By'),
            'show_solution' => Yii::t('app','Show Solution')
        ];
    }

    /**
     * @inheritdoc
     * @return ProblemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProblemQuery(get_called_class());
    }

    /**
     * This is invoked before the record is saved.
     * @return boolean whether the record should be saved.
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $hint = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", '', strip_tags($this->hint));
            if (empty($hint)) {
                $this->hint = $hint;
            }
            //标签处理
            $tagArr = explode(',', str_replace('，', ',', $this->tags));
            foreach ($tagArr as &$tag) {
                $tag = trim($tag);
            }
            $explodeTags = array_unique($tagArr);
            $this->tags = implode(',', $explodeTags);
            return true;
        } else {
            return false;
        }
    }

    public function beforeDelete()
    {
        Solution::deleteAll(['problem_id' => $this->id]);
        ContestProblem::deleteAll(['problem_id' => $this->id]);
        return parent::beforeDelete();
    }

    /**
     * 将序列化保存后的数组解出来
     */
    public function setSamples()
    {
        try{
            $input = unserialize($this->sample_input ?? '');
            $output = unserialize($this->sample_output ?? '');
        }catch(\Throwable $e){
            $input =  array("无","","");
            $output =  array("无","","");
        }
        $this->sample_input = $input[0] ?? null;
        $this->sample_output = $output[0] ?? null;
        $this->sample_input_2 = $input[1] ?? null;
        $this->sample_output_2 = $output[1] ?? null;
        $this->sample_input_3 = $input[2] ?? null;
        $this->sample_output_3 = $output[2] ?? null;
    }

    public function getDataFiles()
    {
        $path = Yii::$app->params['judgeProblemDataPath'] . $this->id ;
        $files = [];
        try {
            if ($handler = opendir($path)) {
                while (($file = readdir($handler)) !== false) {
                    $files[$file]['name'] = $file;
                    $files[$file]['size'] = filesize($path . '/' . $file);
                    $files[$file]['time'] = filemtime($path . '/' . $file);
                }
                closedir($handler);
            }
            usort($files, function($a, $b) {
                return $a['name'] >  (int) $b['name'] ? 1 : -1 ;
            });
        } catch(\Exception $e) {
            echo '<div class="alert alert-danger"><i class="fa fa-info-circle"></i> ' .$e->getMessage().'</div>';
        }
        return $files;
    }

    public function getDiscusses()
    {
        return $this->hasMany(Discuss::className(), ['problem_id' => 'id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getStatisticsData()
    {
        $data = Yii::$app->db->createCommand(
            'SELECT created_by, result FROM {{%solution}} WHERE problem_id=:pid AND contest_id is null',
            [':pid' => $this->id])->queryAll();
        $users = [];
        $accepted_submission = 0;
        $tle_submission = 0;
        $ce_submission = 0;
        $wa_submission = 0;
        $user_count = 0;
        $submission_count = count($data);
        foreach ($data as $v) {
            if (!isset($users[$v['created_by']])) {
                $user_count++;
                $users[$v['created_by']] = 1;
            }
            if ($v['result'] == Solution::OJ_WA) {
                $wa_submission++;
            } else if ($v['result'] == Solution::OJ_AC) {
                $accepted_submission++;
            } else if ($v['result'] == Solution::OJ_CE) {
                $ce_submission++;
            } else if ($v['result'] == Solution::OJ_TL) {
                $tle_submission++;
            }
        }
        return [
            'accepted_count' => $accepted_submission,
            'ce_submission' => $ce_submission,
            'wa_submission' => $wa_submission,
            'tle_submission' => $tle_submission,
            'submission_count' => $submission_count,
            'user_count' => $user_count
        ];
    }

    public function isSolved(){
        if (!Yii::$app->user->isGuest) {
            $solved = (new Query())->select('problem_id')
                ->from('{{%solution}}')
                ->where(['created_by' => Yii::$app->user->id, 'result' => Solution::OJ_AC])
                ->exists();
        
            return $solved;
        }
        return false;
    }
    

    /**
     * 获取当前问题的上一个问题的 ID
     * @return false|string|null
     * @throws \yii\db\Exception
     */
    public function getPreviousProblemID() {
        return Yii::$app->db->createCommand('SELECT id FROM {{%problem}} WHERE id < :id AND status = :status ORDER BY id DESC limit 1')
            ->bindValues([':id' => $this->id, ':status' => Problem::STATUS_VISIBLE])
            ->queryScalar();
    }

    /**
     * 获取当前问题的下一个问题的 ID
     * @return false|string|null
     * @throws \yii\db\Exception
     */
    public function getNextProblemID() {
        return Yii::$app->db->createCommand('SELECT id FROM {{%problem}} WHERE id > :id AND status = :status LIMIT 1')
            ->bindValues([':id' => $this->id, ':status' => Problem::STATUS_VISIBLE])
            ->queryScalar();
    }

    public static function getColorLabel($i){
        $i = $i % 5;
        switch ($i)
        {
        case 0:
            return 'label label-success';
        case 1:
            return 'label label-warning';
        case 2:
            return 'label label-info';
        case 3:
            return 'label label-danger';    
        default:
            return 'label label-primary';  
        }
    
    }

}
