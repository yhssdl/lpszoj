<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\db\Expression;
use app\components\AccessRule;
use app\models\ContestAnnouncement;
use app\models\User;
use app\models\ContestUser;
use app\models\Problem;
use app\models\Discuss;
use app\models\Contest;
use app\models\SolutionSearch;
use app\models\Solution;
use app\modules\admin\models\GenerateUserForm;

/**
 * ContestController implements the CRUD actions for Contest model.
 */
class ContestController extends Controller
{
    public $layout = 'contest';

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
     * Lists all Contest models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Contest::find()->where(['group_id' => 0])->orderBy(['id' => SORT_DESC]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdateproblem($id)
    {
        $model = $this->findModel($id);

        if (($post = Yii::$app->request->post())) {
            $pid = intval($post['problem_id']);
            $new_pid = intval($post['new_problem_id']);
            $has_problem1 = (new Query())->select('id')
                ->from('{{%problem}}')
                ->where('id=:id', [':id' => $pid])
                ->exists();
            $has_problem2 = (new Query())->select('id')
                ->from('{{%problem}}')
                ->where('id=:id', [':id' => $pid])
                ->exists();
            if ($has_problem1 && $has_problem2) {
                $problem_in_contest = (new Query())->select('problem_id')
                    ->from('{{%contest_problem}}')
                    ->where(['problem_id' => $new_pid, 'contest_id' => $model->id])
                    ->exists();
                if ($problem_in_contest) {
                    Yii::$app->session->setFlash('info', Yii::t('app', 'This problem has in the contest.'));
                    return $this->redirect(['contest/view', 'id' => $id]);
                }

                Yii::$app->db->createCommand()->update('{{%contest_problem}}', [
                    'problem_id' => $new_pid,
                ], ['problem_id' => $pid, 'contest_id' => $model->id])->execute();
                Yii::$app->cache->flush();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Submitted successfully'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'No such problem.'));
            }
            return $this->redirect(['contest/view', 'id' => $id]);
        }
    }

    /**
     * 删除指定的公告。
     * @param integer $contest_id; 比赛id
     * @param integer $id 删除的公告id
     * @return mixed
     */
    public function actionDelete_announcement($contest_id,$id)
    {
        Yii::$app->db->createCommand()->delete('{{%contest_announcement}}',['id' => $id])->execute();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Delete successfully'));
        return $this->redirect(['/admin/contest/view', 'id' => $contest_id]);
    }

    /**
     * 比赛积分计算
     * @param $id
     */
    public function actionRated($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->get('cal')) {
            $model->calRating();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Done'));
            return $this->redirect(['rated', 'id' => $model->id]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => ContestUser::find()
                ->where(['contest_id' => $model->id])
                ->with('user')
                ->orderBy(['rating_change' => SORT_DESC]),
        ]);
        return $this->render('rated', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 比赛的题解
     * @param integer $id
     * @return mixed
     */
    public function actionEditorial($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['editorial', 'id' => $model->id]);
        }

        return $this->render('editorial', [
            'model' => $model
        ]);
    }

    /**
     * 打印参加比赛的用户及用户密码
     * @param $id
     * @return string
     */
    public function actionPrintuser($id)
    {
        $model = $this->findModel($id);
        $this->layout = false;
        $users = ContestUser::findAll(['contest_id' => $model->id]);
        return $this->render('printuser', [
            'users' => $users
        ]);
    }

    /**
     * 显示已经参加比赛的用户
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionRegister($id)
    {
        $model = $this->findModel($id);
        $generatorForm = new GenerateUserForm();

        if ($generatorForm->load(Yii::$app->request->post())) {
            $generatorForm->contest_id = $model->id;
            $generatorForm->prefix = 'c' . $model->id . 'user';
            $generatorForm->save();
            return $this->refresh();
        }
        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->get('uid')) {
                // 删除已参赛用户
                $uid = Yii::$app->request->get('uid');
                $inContest = Yii::$app->db->createCommand('SELECT count(1) FROM {{%contest_user}} WHERE user_id=:uid AND contest_id=:cid', [
                    ':uid' => $uid,
                    ':cid' => $model->id
                ])->queryScalar();
                if ($inContest) {
                    ContestUser::deleteAll(['user_id' => $uid, 'contest_id' => $model->id]);
                    Solution::deleteAll(['created_by' => $uid, 'contest_id' => $model->id]);
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully'));
                }
            } else {
                //　添加参赛用户
                $users = Yii::$app->request->post('user');
                $users = explode("\n", trim($users));
                $message = "";
                foreach ($users as $username) {
                    //　查找用户ID 以及查看是否已经加入比赛中
                    $username = trim($username);
                    $query = (new Query())->select('u.id as user_id, count(c.user_id) as exist')
                        ->from('{{%user}} as u')
                        ->leftJoin('{{%contest_user}} as c', 'c.user_id=u.id and c.contest_id=:cid', [':cid' => $model->id])
                        ->where('u.username=:name', [':name' => $username])
                        ->one();
                    if (!isset($query['user_id'])) {
                        $message .= $username . " 不存在该用户<br>";
                    } else if (!$query['exist']) {
                        Yii::$app->db->createCommand()->insert('{{%contest_user}}', [
                            'user_id' => $query['user_id'],
                            'contest_id' => $model->id,
                        ])->execute();
                        $message .= $username . " 添加成功<br>";
                    } else {
                        $message .= $username . " 已参加比赛<br>";
                    }
                }
                Yii::$app->session->setFlash('info', $message);
            }
            return $this->refresh();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => ContestUser::find()->where(['contest_id' => $model->id])->with('user')->with('contest'),
            'pagination' => [
                'pageSize' => 100
            ]
        ]);
        return $this->render('register', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'generatorForm' => $generatorForm
        ]);
    }

    /**
     * 设置题目比赛来源
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionSetProblemSource($id)
    {
        $model = $this->findModel($id);
        if (($post = Yii::$app->request->post())) {
            $source = $post['source'];
            $problemIds = Yii::$app->db->createCommand('SELECT problem_id as id FROM contest_problem WHERE contest_id=' . $model->id)->queryColumn();
            foreach ($problemIds as $pid) {
                Yii::$app->db->createCommand()->update('{{%problem}}', ['source' => $source], [
                    'id' => $pid
                ])->execute();
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Submitted successfully'));
        }
        return $this->redirect(['contest/view', 'id' => $model->id]);
    }

    /**
     * 设置比赛题目的状态
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionSetProblemStatus($id)
    {
        $model = $this->findModel($id);
        if (($post = Yii::$app->request->post())) {
            $status = $post['status'];
            $problemIds = Yii::$app->db->createCommand('SELECT problem_id as id FROM contest_problem WHERE contest_id=' . $model->id)->queryColumn();
            foreach ($problemIds as $pid) {
                Yii::$app->db->createCommand()->update('{{%problem}}', ['status' => $status], [
                    'id' => $pid
                ])->execute();
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Submitted successfully'));
        }
        return $this->redirect(['contest/view', 'id' => $model->id]);
    }

    /**
     * 给比赛添加一个问题
     * @param $id
     * @return \yii\web\Response
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
                $has_problem = (new Query())->select('id')
                    ->from('{{%problem}}')
                    ->where('id=:id', [':id' => $pid])
                    ->exists();
                if ($has_problem) {
                    $problem_in_contest = (new Query())->select('problem_id')
                        ->from('{{%contest_problem}}')
                        ->where(['problem_id' => $pid, 'contest_id' => $model->id])
                        ->exists();
                    if ($problem_in_contest) {
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
                } else {
                    $info_msg = $info_msg.$pid.":".Yii::t('app', 'No such problem.')."<br>";
                }
            }
            if($info_msg==""){
                Yii::$app->session->setFlash('success', Yii::t('app', 'Submitted successfully')); 
            }else{
                 Yii::$app->session->setFlash('info', $info_msg);
            }
        }
        return $this->redirect(['contest/view', 'id' => $id]);
    }

    /**
     * 滚榜
     * @param $id
     * @param bool $json
     * @return string
     */
    public function actionScrollScoreboard($id, $json = false)
    {
        $model = $this->findModel($id);

        $this->layout = 'basic';

        $numberOfGoldMedals = Yii::$app->request->get('gold');
        $numberOfSilverMedals = Yii::$app->request->get('silver');
        $numberOfBronzeMedals = Yii::$app->request->get('bronze');

        if ($json) {
            $query = (new Query())->select('s.id, u.username, u.nickname, s.result, s.created_at, p.num')
                ->from('{{%solution}} as s')
                ->leftJoin('{{%user}} as u', 'u.id=s.created_by')
                ->leftJoin('{{%contest_problem}} as p', 'p.problem_id=s.problem_id AND p.contest_id=s.contest_id')
                ->where(['s.contest_id' => $model->id])
                ->andWhere(['between', 's.created_at', $model->start_time, $model->end_time]);
            if ($model->scenario == Contest::SCENARIO_OFFLINE) {
                $query->andWhere(['u.role' => User::ROLE_PLAYER]);
            }
            $data = $query->all();
            foreach ($data as &$v) {
                $v['submitId'] = $v['id'];
                $v['subTime'] = $v['created_at'];
                $v['alphabetId'] = 1 + $v['num'];
                $v['resultId'] = $v['result'];
                unset($v['id']);
                unset($v['created_at']);
                unset($v['num']);
                unset($v['result']);
            }

            return json_encode(['total' => count($data), 'data' => $data]);
        }

        return $this->render('board', [
            'model' => $model,
            'numberOfGoldMedals' => $numberOfGoldMedals,
            'numberOfSilverMedals' => $numberOfSilverMedals,
            'numberOfBronzeMedals' => $numberOfBronzeMedals
        ]);
    }

    /**
     * 显示榜单
     * @param integer $id 比赛 ID
     * @return string
     */
    public function actionRank($id)
    {
        $model = $this->findModel($id);

        $this->layout = 'basic';

        if ($model->type == Contest::TYPE_OI || $model->type == Contest::TYPE_IOI) {
            return $this->render('oi_rank', [
                'model' => $model,
            ]);
        } else {
            return $this->render('rank', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 导出成绩
     * @param integer $id 比赛 ID
     * @return string
     */
    public function actionExport($id)
    {
        $model = $this->findModel($id);
        if ($model->type == Contest::TYPE_OI || $model->type == Contest::TYPE_IOI) {
            return $this->renderPartial('oi_export', [
                'model' => $model,
            ]);
        } else {
            return $this->renderPartial('export', [
                'model' => $model,
            ]);
        }
    }    

    public function actionPrint($id)
    {
        $model = $this->findModel($id);

        $this->layout = 'basic';

        $problems = (new Query())->select('p.title, p.description, p.input, p.output, p.sample_input,
                                          p.sample_output, p.hint, c.num, p.time_limit, p.memory_limit')
            ->from('{{%problem}} as p')
            ->leftJoin('{{%contest_problem}} as c', ['c.contest_id' => $model->id])
            ->where('p.id=c.problem_id')
            ->orderBy('c.num ASC')
            ->all();

        return $this->render('print', [
            'model' => $model,
            'problems' => $problems
        ]);
    }

    
    /**
     * 显示打星用户
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionStar($id)
    {
        $model = $this->findModel($id);
        $generatorForm = new GenerateUserForm();

        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->get('uid')) {
                // 删除打星用户
                $uid = Yii::$app->request->get('uid');
                $inContest = Yii::$app->db->createCommand('SELECT count(1) FROM {{%contest_user}} WHERE user_id=:uid AND contest_id=:cid', [
                    ':uid' => $uid,
                    ':cid' => $model->id
                ])->queryScalar();
                if ($inContest) {
                    ContestUser::updateAll(['is_out_of_competition' => '0'], ['user_id' => $uid, 'contest_id' => $model->id]);
                    Yii::$app->session->setFlash('success', Yii::t('app', '设置成功'));
                }
            } else {
                //　添加打星用户
                $users = Yii::$app->request->post('user');
                $users = explode("\n", trim($users));
                $message = "";
                foreach ($users as $username) {
                    //　查找用户ID 以及查看是否已经加入比赛中
                    $username = trim($username);
                    $query = (new Query())->select('u.id as user_id, count(c.user_id) as exist')
                        ->from('{{%user}} as u')
                        ->leftJoin('{{%contest_user}} as c', 'c.user_id=u.id')
                        ->where('u.username=:name and c.contest_id=:cid', [':name' => $username, ':cid' => $model->id])
                        ->one();
                    if (!isset($query['user_id'])) {
                        $message .= $username . " 不存在该用户<br>";
                    } else if (!$query['exist']) {
                        // Yii::$app->db->createCommand()->insert('{{%contest_user}}', [
                        //     'user_id' => $query['user_id'],
                        //     'contest_id' => $model->id,
                        // ])->execute();
                        $message .= $username . " 未参加比赛<br>";
                    } else {
                        Yii::$app->db->createCommand()->update('{{%contest_user}}', [
                            'is_out_of_competition' => '1',
                        ], [
                            'user_id' => $query['user_id'],
                            'contest_id' => $model->id,
                        ])->execute();
                        $message .= $username . " 设置打星成功<br>";
                    }
                }
                Yii::$app->session->setFlash('info', $message);
            }
            return $this->refresh();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => ContestUser::find()->where(['contest_id' => $model->id, 'is_out_of_competition' => '1'])->with('user')->with('contest'),
            'pagination' => [
                'pageSize' => 100
            ]
        ]);
        return $this->render('star', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'generatorForm' => $generatorForm
        ]);
    }


    /**
     * 显示该比赛的所有提交记录
     * @param integer $id
     * @param integer $active 该值等于 0 就什么也不做，等于 1 就将所有提交记录显示在前台的提交记录列表，等于 2 就隐藏提交记录
     * @return mixed
     */
    public function actionStatus($id, $active = 0, $autoRefresh = 0)
    {

        $model = $this->findModel($id);
        $searchModel = new SolutionSearch();

        if ($active == 1) {
            Solution::updateAll(['status' => Solution::STATUS_VISIBLE], ['contest_id' => $model->id]);
            $this->redirect(['status', 'id' => $id]);
        } else if ($active == 2) {
            Solution::updateAll(['status' => Solution::STATUS_HIDDEN], ['contest_id' => $model->id]);
            $this->redirect(['status', 'id' => $id]);
        }

        return $this->render('status', [
            'autoRefresh' => $autoRefresh,
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(Yii::$app->request->queryParams, $model)
        ]);
    }

    /**
     * 下载比赛期间提交记录
     */
    public function actionDownloadSolution($id)
    {
        $model = $this->findModel($id);
        $zipFile = $model->saveContestSolutionToFile();
        if (!$zipFile) {
            Yii::$app->session->setFlash('error', '保存失败。可能是系统未安装 zip 命令。');
            return $this->redirect(['status', 'id' => $id]);
        }
        Yii::$app->response->on(\yii\web\Response::EVENT_AFTER_SEND, function($event) { unlink($event->data); }, $zipFile);
        return Yii::$app->response->sendFile($zipFile, $model->id . '-' . $model->title . '.zip');
    }

    /**
     * Displays a single Contest model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $announcements = new ActiveDataProvider([
            'query' => ContestAnnouncement::find()->where(['contest_id' => $model->id])
        ]);

        $newAnnouncement = new ContestAnnouncement();
        if ($newAnnouncement->load(Yii::$app->request->post())) {
            $newAnnouncement->contest_id = $model->id;
            $newAnnouncement->save();
            try {
                // 给前台所有用户提醒
                $client = stream_socket_client('tcp://0.0.0.0:2121', $errno, $errmsg, 1);
                fwrite($client, json_encode(['content' => Yii::t('app', 'New announcement: ') . $newAnnouncement->content])."\n");
            } catch (\Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Submitted successfully'));
            return $this->refresh();
        }

        return $this->render('view', [
            'model' => $model,
            'announcements' => $announcements,
            'newAnnouncement' => $newAnnouncement
        ]);
    }

    /**
     * Creates a new Contest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Contest();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Contest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionClarify($id, $cid = -1)
    {
        $model = $this->findModel($id);
        $new_clarify = new Discuss();
        $discuss = null;

        if ($cid != -1) {
            if (($discuss = Discuss::findOne(['id' => $cid, 'entity_id' => $model->id, 'entity' => Discuss::ENTITY_CONTEST])) === null) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
        if ($new_clarify->load(Yii::$app->request->post())) {
            if (empty($new_clarify->content)) {
                $discuss->load(Yii::$app->request->post());
                $discuss->update();
                return $this->refresh();
            }
            $new_clarify->entity = Discuss::ENTITY_CONTEST;
            $new_clarify->entity_id = $model->id;
            if ($discuss !== null) {
                Yii::$app->db->createCommand()->update('{{%discuss}}', ['updated_at' => new Expression('NOW()')], ['id' => $cid])->execute();
                $new_clarify->parent_id = $discuss->id;
            }
            $new_clarify->save();

            try {
                // 给前台用户提醒
                $client = stream_socket_client('tcp://0.0.0.0:2121', $errno, $errmsg, 1);
                fwrite($client, json_encode([
                    'uid' => $discuss->created_by,
                    'content' => Yii::t('app', 'New reply') . ': '. $new_clarify->content
                ])."\n");
            } catch (\Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }

            Yii::$app->session->setFlash('success', Yii::t('app', 'Submitted successfully'));
            return $this->refresh();
        }

        $clarifies = new ActiveDataProvider([
            'query' => Discuss::find()
                ->where(['parent_id' => 0, 'entity_id' => $model->id, 'entity' => Discuss::ENTITY_CONTEST])
                ->with('user')
                ->orderBy('created_at DESC'),
        ]);

        return $this->render('clarify', [
            'model' => $model,
            'clarifies' => $clarifies,
            'new_clarify' => $new_clarify,
            'discuss' => $discuss
        ]);
    }

    public function actionNewProblem()
    {
        $this->layout = false;
        $model = new Problem();
        return $this->renderAjax('/problem/create', [
            'model' => $model
        ]);
    }

    public function actionDeleteproblem($id, $pid)
    {
        $model = $this->findModel($id);
        $model->deleteProblem($pid);
        Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted successfully'));
        return $this->redirect(['contest/view', 'id' => $id]);
    }

    /**
     * Deletes an existing Contest model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', Yii::t('app', '已删除'));
        return $this->redirect(['index']);
    }

    /**
     * Finds the Contest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Contest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contest::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
     
}
