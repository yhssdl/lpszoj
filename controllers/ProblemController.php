<?php

namespace app\controllers;

use app\components\BaseController;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\data\Pagination;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use app\models\Problem;
use app\models\Solution;
use app\models\User;
use justinvoelker\tagging\TaggingQuery;
use app\models\Discuss;

/**
 * ProblemController implements the CRUD actions for Problem model.
 */
class ProblemController extends BaseController
{
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

        ];
    }

    /**
     * Lists all Problem models.
     * @return mixed
     */
    public function actionIndex($page = 1, $tag = '')
    {

        if (Yii::$app->setting->get('isContestMode') && (Yii::$app->user->isGuest || (!Yii::$app->user->identity->isAdmin()))) {
            throw new ForbiddenHttpException('不允许执行此操作。');
        }

        $query = Problem::find();

        if (Yii::$app->request->get('tag') != '') {
            $query->andWhere('tags LIKE :tag', [':tag' => '%' . Yii::$app->request->get('tag') . '%']);
        }
        if (($post = Yii::$app->request->post())) {
            if(isset($post['q'])) {
                $query->orWhere(['like', 'title', $post['q']])
                    ->orWhere(['like', 'id', $post['q']])
                    ->orWhere(['like', 'source', $post['q']]);
            }
            if(isset($post['tag'])) {
                $query->orWhere(['like', 'tags', $post['tag']]);
            }
        }
        $query->andWhere('status<>' . Problem::STATUS_HIDDEN);

        if (Yii::$app->setting->get('isHideVIP') && (Yii::$app->user->isGuest || Yii::$app->user->identity->role === User::ROLE_USER)){
            $query->andWhere('status<>' . Problem::STATUS_PRIVATE);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50
            ]
        ]);

        $cache = Yii::$app->cache;
        $tags = $cache->get('problem-tags');
        if ($tags === false) {
            $tags = (new TaggingQuery())->select('tags')
                ->from('{{%problem}}')
                ->where('status<>' . Problem::STATUS_HIDDEN)
                ->limit(30)
                ->displaySort(['freq' => SORT_DESC])
                ->getTags();
            $cache->set('problem-tags', $tags, 3600);
        }

        $solvedProblem = [];
        if (!Yii::$app->user->isGuest) {
            $solved = (new Query())->select('problem_id')
                ->from('{{%solution}}')
                ->where(['created_by' => Yii::$app->user->id, 'result' => Solution::OJ_AC])
                ->all();
            foreach ($solved as $k) {
                $solvedProblem[$k['problem_id']] = true;
            }
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'tags' => $tags,
            'solvedProblem' => $solvedProblem,
            'page' => $page,
            'tag' => $tag            
        ]);
    }

/**
     * select all Problem models.
     * @return mixed
     */
    public function actionSelect($page = 1, $tag = '')
    {
        $this->layout = "basic";

        if (Yii::$app->setting->get('isContestMode') && (Yii::$app->user->isGuest || (!Yii::$app->user->identity->isAdmin()))) {
            throw new ForbiddenHttpException('不允许执行此操作。');
        }

        $query = Problem::find();

        if (Yii::$app->request->get('tag') != '') {
            $query->andWhere('tags LIKE :tag', [':tag' => '%' . Yii::$app->request->get('tag') . '%']);
        }
        if (($post = Yii::$app->request->post())) {
            if(isset($post['q'])) {
                $query->orWhere(['like', 'title', $post['q']])
                    ->orWhere(['like', 'id', $post['q']])
                    ->orWhere(['like', 'source', $post['q']]);
            }
            if(isset($post['tag'])) {
                $query->orWhere(['like', 'tags', $post['tag']]);
            }
        }
        if (!Yii::$app->user->identity->isAdmin()){
            $query->andWhere('status<>' . Problem::STATUS_HIDDEN);
        }
        
        if (Yii::$app->setting->get('isHideVIP') && (Yii::$app->user->isGuest || Yii::$app->user->identity->role === User::ROLE_USER)){
            $query->andWhere('status<>' . Problem::STATUS_PRIVATE);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 30
            ]
        ]);

        $cache = Yii::$app->cache;
        $tags = $cache->get('problem-tags');
        if ($tags === false) {
            $tags = (new TaggingQuery())->select('tags')
                ->from('{{%problem}}')
                ->where('status<>' . Problem::STATUS_HIDDEN)
                ->limit(30)
                ->displaySort(['freq' => SORT_DESC])
                ->getTags();
            $cache->set('problem-tags', $tags, 3600);
        }

        return $this->render('select', [
            'dataProvider' => $dataProvider,
            'tags' => $tags,
            'page' => $page,
            'tag' => $tag            
        ]);
    }


    public function actionStatistics($id)
    {
        $model = $this->findModel($id);

        $dataProvider = new ActiveDataProvider([
            'query' => Solution::find()->with('user')
                ->where(['problem_id' => $model->id, 'result' => Solution::OJ_AC])
                ->orderBy(['time' => SORT_ASC, 'memory' => SORT_ASC, 'code_length' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        return $this->render('stat', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDiscuss($id)
    {
        $model = $this->findModel($id);
        $newDiscuss = new Discuss();
        $newDiscuss->setScenario('problem');

        $query = Discuss::find()->where([
            'entity' => Discuss::ENTITY_PROBLEM,
            'entity_id' => $model->id,
            'parent_id' => 0
        ])->orderBy('updated_at DESC');

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $discusses = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        if ($newDiscuss->load(Yii::$app->request->post())) {
            if (Yii::$app->user->isGuest) {
                Yii::$app->session->setFlash('error', 'Please login.');
                return $this->redirect(['/site/login']);
            }
            $newDiscuss->entity = Discuss::ENTITY_PROBLEM;
            $newDiscuss->entity_id = $id;
            $newDiscuss->save();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Submitted successfully'));
            return $this->refresh();
        }

        return $this->render('discuss', [
            'model' => $model,
            'discusses' => $discusses,
            'pages' => $pages,
            'newDiscuss' => $newDiscuss
        ]);
    }

    /**
     * Displays a single Problem model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $solution = new Solution();
        $submissions = NULL;
        if (!Yii::$app->user->isGuest) {
            $submissions = (new Query())->select('created_at, result, id')
                ->from('{{%solution}}')
                ->where([
                    'problem_id' => $id,
                    'created_by' => Yii::$app->user->id
                ])
                ->orderBy('id DESC')
                ->limit(10)
                ->all();
        }
        if ($solution->load(Yii::$app->request->post())) {
            if (Yii::$app->user->isGuest) {
                Yii::$app->session->setFlash('error', 'Please login.');
                return $this->redirect(['/site/login']);
            }
            $st = time() - Yii::$app->session['Submit_time'];
            $jt = intval(Yii::$app->setting->get('submitTime'));
            if($st > $jt) {
                $solution->problem_id = $model->id;
                $solution->status = Solution::STATUS_VISIBLE;
                $solution->ip = $solution->getClientIp();
                $solution->save();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Submitted successfully'));
                Yii::$app->session['Submit_time']= time();
                return $this->refresh();
            } else {
                $st = $jt - $st;
                $tip = sprintf(Yii::t('app', 'The submission interval is %d seconds, and you can submit again after %d seconds.'),$jt,$st);
                Yii::$app->session->setFlash('error', $tip);
            }
           
        }


        $view = (Yii::$app->setting->get('showMode') ? 'view' : 'classic');

        return $this->render($view, [
            'solution' => $solution,
            'model' => $model,
            'submissions' => $submissions
        ]);
    }


    /**
     * 查看题解
     * @param $id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionSolution($id)
    {
        $model = $this->findModel($id);

        return $this->render('solution', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Problem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Problem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     * @throws ForbiddenHttpException if the model cannot be viewed
     */
    protected function findModel($id)
    {
        if (($model = Problem::findOne($id)) !== null) {
            $isVisible = ($model->status == Problem::STATUS_VISIBLE);
            $isPrivate = ($model->status == Problem::STATUS_PRIVATE);
            if ($isVisible || ($isPrivate && !Yii::$app->user->isGuest &&
                               (Yii::$app->user->identity->role === User::ROLE_VIP || Yii::$app->user->identity->role === User::ROLE_ADMIN))) {
                return $model;
            } else {
                throw new ForbiddenHttpException($isPrivate?'当前题目为VIP题目，普通用户没有权限访问。':'不允许执行此操作。');
            }
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
