<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\components\AccessRule;
use app\modules\admin\models\GenerateUserForm;
use app\models\User;
use app\models\UserSearch;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public $layout = 'user';
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $generatorForm = new GenerateUserForm();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        if ($generatorForm->load(Yii::$app->request->post())) {
            $generatorForm->generateUsers();
            return $this->refresh();
        }

        $generatorForm->language = Yii::$app->setting->get('defaultLanguage');

        if (Yii::$app->request->get('action') && Yii::$app->request->isPost) {
            $keys = Yii::$app->request->post('keylist');
            $action = Yii::$app->request->get('action');
            foreach ($keys as $key) {
                if(Yii::$app->user->id != $key){
                    Yii::$app->db->createCommand()->update('{{%user}}', [
                        'role' => $action
                    ], ['id' => $key])->execute();                   
                }
            }
            return $this->refresh();
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'generatorForm' => $generatorForm
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $newPassword = Yii::$app->request->post('User')['newPassword'];
            $role = Yii::$app->request->post('User')['role'];
            if (!empty($newPassword)) {
                Yii::$app->db->createCommand()->update('{{%user}}', [
                    'password_hash' => Yii::$app->security->generatePasswordHash($newPassword,5)
                ], ['id' => $model->id])->execute();
            } else if (!empty($role)) {
                $model->role = intval($role);
                $model->save();
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Saved successfully'));
            return $this->refresh();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Yii::$app->db->createCommand()->delete('{{%user_profile}}',['user_id' => $id])->execute();
        Yii::$app->db->createCommand()->delete('{{%contest_user}}',['user_id' => $id])->execute();
        Yii::$app->db->createCommand()->delete('{{%group_user}}',['user_id' => $id])->execute();
        Yii::$app->db->createCommand()->delete('{{%discuss}}',['created_by' => $id])->execute(); 
        Yii::$app->db->createCommand('DELETE solution_info, solution FROM solution_info, solution  WHERE solution_info.solution_id=solution.id AND solution.created_by=:uid',[':uid' => $id])->execute(); 
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Delete successfully'));

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
