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
use app\models\User;
use yii\db\Expression;
use app\models\Contest;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use app\models\ContestAnnouncement;
use app\models\Homework;

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
        $model->join_policy = Group::JOIN_POLICY_INVITE;

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
        $model = $this->findModel($id);
        $role = $model->getRole();

     
        $newContest = new Contest();
        $newContest->type = Contest::TYPE_HOMEWORK;
        $newContest->language = -1;
        $newContest->enable_clarify = 1;
        $newContest->start_time = date('Y-m-d H:i:s');
        $newContest->end_time = '9999-12-31 23:59:59';
        $contestDataProvider = new ActiveDataProvider([
            'query' => Contest::find()->where([
                'group_id' => $model->id
            ])->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
             ]
        ]);


        if ($newContest->load(Yii::$app->request->post())) {
            $newContest->group_id = $model->id;
            $newContest->scenario = Contest::SCENARIO_ONLINE;
            $newContest->status = Contest::STATUS_VISIBLE;
            $newContest->save();
            return $this->refresh();
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
        $model = $this->findModel($id);
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

        $model = $this->findHomeworkModel($id);

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

        return $this->render('section', [
            'model' => $model,
            'announcements' => $announcements,
            'newAnnouncement' => $newAnnouncement
        ]);
    }

    /**
     * Finds the Group model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Group the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Group::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    } 

    /**
     * @param int $id
     * @return Homework|null
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    protected function findHomeworkModel($id)
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
