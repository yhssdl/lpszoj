<?php

namespace app\modules\admin\controllers;
use Yii;
use yii\web\Controller;
use app\models\Group;
use app\models\GroupUser;
use yii\data\ActiveDataProvider;
use app\components\AccessRule;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\db\Expression;
use app\models\Contest;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\db\Query;
use app\models\ContestUser;
use app\models\Problem;
use app\models\Solution;
use app\models\User;
use app\models\ContestProblem;

/**
 * TrainingController implements the CRUD actions for Training model.
 */
class TrainingController extends Controller
{
    public $layout = 'training';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
                        // Allow users, moderators and admins to create
                        'roles' => [
                            User::ROLE_ADMIN
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Training models.
     * @return mixed
     */
    public function actionIndex()
    {

        $query = Group::find();
        $query->where(['is_train' => Group::MODE_TRAIN]);
    

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Training model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Group();
        $model->is_train = 1;
        $model->status = Group::STATUS_VISIBLE;
        $model->join_policy = Group::JOIN_POLICY_FREE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $groupUser = new GroupUser();
            $groupUser->role = GroupUser::ROLE_LEADER;
            $groupUser->created_at = new Expression('NOW()');
            $groupUser->user_id = Yii::$app->user->id;
            $groupUser->group_id = $model->id;
            $groupUser->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }



    /**
     * Displays a single Training model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws ForbiddenHttpException
     */
    public function actionView($id,$sort=0)
    {
        $model = $this->findGroupModel($id);
        
     
        $newContest = new Contest();
        $newContest->type = Contest::TYPE_HOMEWORK;
        $newContest->language = -1;
        $newContest->enable_clarify = 1;
        $newContest->show_solution = 0;
        $newContest->punish_time = -1;
        $newContest->start_time = '2000-01-01 00:00:01';
        $newContest->end_time = '9999-12-31 23:59:59';
        $contestDataProvider = new ActiveDataProvider([
            'query' => Contest::find()->where([
                'group_id' => $model->id
            ])->orderBy(['id' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 20,
             ]
        ]);


        if ($newContest->load(Yii::$app->request->post())) {
            $newContest->group_id = $model->id;
            $newContest->scenario = Contest::SCENARIO_ONLINE;
            $newContest->status = Contest::STATUS_VISIBLE;
            $newContest->save();
            return $this->redirect(['section', 'id' => $newContest->id]);
        }

        return $this->render('view', [
            'model' => $model,
            'contestDataProvider' => $contestDataProvider,
            'newContest' => $newContest
        ]);
    }


    /**
     * Updates an existing Training model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws ForbiddenHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findGroupModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

/**
     * @param $id
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionSection($id)
    {

        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $model->type = Contest::TYPE_HOMEWORK;
            $model->language = -1;
            $model->enable_clarify = 1;
            $model->show_solution = 0;
            $model->punish_time = -1;
            $model->start_time = '2000-01-01 00:00:01';
            $model->end_time = '9999-12-31 23:59:59';
            $model->save();
            return $this->redirect(['view', 'id' => $model->group_id]);
            //return $this->refresh();
        }

        return $this->render('section', [
            'model' => $model,
        ]);
    }


    /**
     * 删除一个问题
     * @param $id
     * @param $pid
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionDeleteproblem($id, $pid)
    {
        $model = $this->findModel($id);
        $model->deleteProblem($pid);
        Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully'));
        return $this->redirect(['section', 'id' => $id]);
    }

    /**
     * 增加一个问题
     * @param $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionAddproblem($id)
    {
        $model = $this->findModel($id);

        if (($post = Yii::$app->request->post())) {

            $problem_ids  = $post['problem_ids'];
            $cnt = count($problem_ids);
            $info_msg = "";
            for ($i = 0; $i < $cnt; ++$i) {
                if (empty($problem_ids[$i]) || $problem_ids[$i]=='1')
                    continue;
                $pid =  $problem_ids[$i];

                $problemStatus = (new Query())->select('status')
                    ->from('{{%problem}}')
                    ->where('id=:id', [':id' => $pid])
                    ->scalar();
                if ($problemStatus == null || ($problemStatus == Problem::STATUS_HIDDEN && Yii::$app->user->identity->role != User::ROLE_ADMIN)) {
                    Yii::$app->session->setFlash('error', $pid);
                } else if ($problemStatus == Problem::STATUS_PRIVATE 
                        && (Yii::$app->user->identity->role < User::ROLE_VIP)) {
                    $info_msg = $info_msg.$pid.":".Yii::t('app', '私有题目，仅 VIP 用户可选用')."<br>";
                }else if ($problemStatus >= Problem::STATUS_TEACHER 
                        && (Yii::$app->user->identity->role < User::ROLE_TEQACHER)) {
                    $info_msg = $info_msg.$pid.":".Yii::t('app', '私有题目，仅教师可选用')."<br>";
                }
                else {
                    $problemInContest = (new Query())->select('problem_id')
                        ->from('{{%contest_problem}}')
                        ->where(['problem_id' => $pid, 'contest_id' => $model->id])
                        ->exists();
                    if ($problemInContest) {
                        $info_msg = $info_msg.$pid.":".Yii::t('app', 'This problem has in the contest.')."<br>";
                        continue;
                    }
                    $count = (new Query())->select('contest_id')
                        ->from('{{%contest_problem}}')
                        ->where(['contest_id' => $model->id])
                        ->count();
    
                    Yii::$app->db->createCommand()->insert('{{%contest_problem}}', [
                        'problem_id' => $pid,
                        'contest_id' => $model->id,
                        'num' => $count
                    ])->execute();
                }
            }
            if($info_msg==""){
                Yii::$app->session->setFlash('success', Yii::t('app', 'Submitted successfully')); 
            }else{
                 Yii::$app->session->setFlash('info', $info_msg);
            } 
        }
        return $this->redirect(['section', 'id' => $id]);
    }

    /**
     * 修改一个问题
     * @param $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionUpdateproblem($id)
    {
        $model = $this->findModel($id);

        if (($post = Yii::$app->request->post())) {
            $pid = intval($post['problem_id']);
            $new_pid = intval($post['new_problem_id']);

            $oldProblemStatus = (new Query())->select('status')
                ->from('{{%problem}}')
                ->where('id=:id', [':id' => $pid])
                ->scalar();
            $newProblemStatus = (new Query())->select('status')
                ->from('{{%problem}}')
                ->where('id=:id', [':id' => $new_pid])
                ->scalar();

            if (!empty($oldProblemStatus) && !empty($newProblemStatus)) {
                $problemInContest = (new Query())->select('problem_id')
                    ->from('{{%contest_problem}}')
                    ->where(['problem_id' => $new_pid, 'contest_id' => $model->id])
                    ->exists();
                if ($problemInContest) {
                    Yii::$app->session->setFlash('info', Yii::t('app', 'This problem has in the contest.'));
                    return $this->redirect(['section', 'id' => $id]);
                }
                if ($newProblemStatus == Problem::STATUS_VISIBLE || Yii::$app->user->identity->role == User::ROLE_ADMIN
                    || ($newProblemStatus == Problem::STATUS_PRIVATE && Yii::$app->user->identity->role >= User::ROLE_VIP)
                    || ($newProblemStatus >= Problem::STATUS_TEACHER && Yii::$app->user->identity->role >= User::TEACHER)) {
                    Yii::$app->db->createCommand()->update('{{%contest_problem}}', [
                        'problem_id' => $new_pid,
                    ], ['problem_id' => $pid, 'contest_id' => $model->id])->execute();
                    Yii::$app->cache->flush();
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Submitted successfully'));
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'No such problem.'));
            }
            return $this->redirect(['section', 'id' => $id]);
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findGroupModel($id);
        $model->delete();
        Yii::$app->session->setFlash('success', '已删除');
        return $this->redirect(['index']);
    }


    public function actionDelete_section($id)
    {
        $model = $this->findModel($id);
        ContestUser::deleteAll(['contest_id' => $model->id]);
        ContestProblem::deleteAll(['contest_id' => $model->id]);
        Solution::deleteAll(['contest_id' => $model->id]);
        $groupId = $model->group_id;
        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('app', '已删除'));
        return $this->redirect(['view', 'id' => $groupId]);
    }    

    /**
     * Finds the Group model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Group the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findGroupModel($id)
    {
        if (($model = Group::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    } 

    /**
     * @param int $id
     * @return Contest|null
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Contest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

}
