<?php

namespace app\controllers;

use app\components\BaseController;
use app\models\ContestProblem;
use app\models\ContestUser;
use app\models\Problem;
use app\models\Solution;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;
use yii\filters\AccessControl;
use app\models\Homework;
use app\models\ContestAnnouncement;
use yii\web\NotFoundHttpException;

/**
 * HomeworkController implements the CRUD actions for Homework model.
 */
class HomeworkController extends BaseController
{
    public function init()
    {
        Yii::$app->language = 'zh-CN';
        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create'],
                'rules' => [
                    [
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
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
        return $this->redirect(['/homework/update', 'id' => $id]);
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

            $problem_ids  = $post['problem_id'];
            $problem_ids = str_replace(","," ",$problem_ids);
            $problem_ids = str_replace("，"," ",$problem_ids);
            $problem_ids = explode(" ", trim($problem_ids));
            $cnt = count($problem_ids);
            for ($i = 0; $i < $cnt; ++$i) {
                if (empty($problem_ids[$i]))
                continue;
                $ids = explode("-", $problem_ids[$i]);
                if(count($ids)==2){
                    $id1 = intval($ids[0]);
                    $id2 = intval($ids[1]);
                    for($k=0;$id1<=$id2;++$id1,++$k){
                        $pids[$k] = $id1;
                    }
                }else{
                    $pids[0] = intval($problem_ids[$i]);
                }
                for($j=0;$j<count($pids);++$j){
                    $pid = $pids[$j];
                    $problemStatus = (new Query())->select('status')
                        ->from('{{%problem}}')
                        ->where('id=:id', [':id' => $pid])
                        ->scalar();
                    if ($problemStatus == null || ($problemStatus == Problem::STATUS_HIDDEN && Yii::$app->user->identity->role != User::ROLE_ADMIN)) {
                        Yii::$app->session->setFlash('error', Yii::t('app', 'No such problem.'));
                    } else if ($problemStatus == Problem::STATUS_PRIVATE && (Yii::$app->user->identity->role == User::ROLE_USER ||
                                                                             Yii::$app->user->identity->role == User::ROLE_PLAYER)) {
                        Yii::$app->session->setFlash('error', Yii::t('app', '私有题目，仅 VIP 用户可选用'));
                    } else {
                        $problemInContest = (new Query())->select('problem_id')
                            ->from('{{%contest_problem}}')
                            ->where(['problem_id' => $pid, 'contest_id' => $model->id])
                            ->exists();
                        if ($problemInContest) {
                            Yii::$app->session->setFlash('info', Yii::t('app', 'This problem has in the contest.'));
                            return $this->redirect(['/homework/update', 'id' => $id]);
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
                        Yii::$app->session->setFlash('success', Yii::t('app', 'Submitted successfully'));
                    }
                }
            }
            return $this->redirect(['/homework/update', 'id' => $id]);
        }
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
                    return $this->refresh();
                }
                if ($newProblemStatus == Problem::STATUS_VISIBLE || Yii::$app->user->identity->role == User::ROLE_ADMIN
                    || ($newProblemStatus == Problem::STATUS_PRIVATE && Yii::$app->user->identity->role == User::ROLE_VIP)) {
                    Yii::$app->db->createCommand()->update('{{%contest_problem}}', [
                        'problem_id' => $new_pid,
                    ], ['problem_id' => $pid, 'contest_id' => $model->id])->execute();
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Submitted successfully'));
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'No such problem.'));
            }
            return $this->redirect(['/homework/update', 'id' => $id]);
        }
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $this->layout = 'main';
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->refresh();
        }

        $announcements = new ActiveDataProvider([
            'query' => ContestAnnouncement::find()->where(['contest_id' => $model->id])
        ]);

        $newAnnouncement = new ContestAnnouncement();
        if ($newAnnouncement->load(Yii::$app->request->post())) {
            $newAnnouncement->contest_id = $model->id;
            $newAnnouncement->save();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Saved successfully'));
            return $this->refresh();
        }

        return $this->render('update', [
            'model' => $model,
            'announcements' => $announcements,
            'newAnnouncement' => $newAnnouncement
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        ContestUser::deleteAll(['contest_id' => $model->id]);
        ContestProblem::deleteAll(['contest_id' => $model->id]);
        Solution::deleteAll(['contest_id' => $model->id]);
        $groupId = $model->group_id;
        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('app', '已删除'));
        return $this->redirect(['/group/view', 'id' => $groupId]);
    }

    /**
     * @param int $id
     * @return Homework|null
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Homework::findOne($id)) !== null) {
            if ($model->hasPermission()) {
                return $model;
            } else {
                throw new ForbiddenHttpException('You are not allowed to perform this action.');
            }
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
