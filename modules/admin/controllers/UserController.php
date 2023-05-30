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
use app\models\Group;

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
            $action = Yii::$app->request->get('action');
            $keys = Yii::$app->request->post('keylist');
            if($keys){
                if($action=='setuser') {
                    $newPassword = Yii::$app->request->post('newPassword');
                    $newNickname = Yii::$app->request->post('nickname');
                    $newMemo =  Yii::$app->request->post('memo');
                    $newRole =  Yii::$app->request->post('role');
                    $school =  Yii::$app->request->post('school');
                    if(!empty($newPassword) || !empty($newNickname) || !empty($newMemo)|| $newRole!=null || !empty($school)){
                        if (!empty($newPassword)) {
                            $newPassword = Yii::$app->security->generatePasswordHash($newPassword,5);
                        }
                        $sum = 0;
                        foreach ($keys as $key) {
                            if(Yii::$app->user->id != $key){
                                $sum++;
                                $user_model = User::findOne($key);
                                if(!empty($newNickname)){
                                    $nickname = str_replace("{u}",$user_model->username,$newNickname);
                                    $nickname = str_replace("{n}",$user_model->nickname,$nickname);
                                    $user_model->nickname = $nickname;
                                }
                                if(!empty($newMemo)) {
                                    $user_model->memo = $newMemo;
                                }
                                if($newRole!=null){
                                    $user_model->role = $newRole;
                                }

                                if (!empty($newPassword)) {
                                    $user_model->password_hash = $newPassword;
                                }

                                if (!empty($school)) {
                                    Yii::$app->db->createCommand()->update('{{%user_profile}}', [
                                        'school'=> $school,], ['user_id' => $user_model->id])->execute();
                                }

                                $user_model->save();                
                            }
                        }
                        Yii::$app->session->setFlash('success', '批量设置属性成功：'.$sum.'人。' );
                    }else{
                        Yii::$app->session->setFlash('error', '当前未设置新的属性，修改失败！'.$school );
                    }
                } else if ($action == 'delete') {
                    $sum = 0;
                    foreach ($keys as $key) {
                        if(Yii::$app->user->id != $key){
                            $sum++;
                            $model = $this->findModel($key);
                            try {
                                Yii::$app->db->createCommand()->delete('{{%user_profile}}',['user_id' => $model->id])->execute();
                                Yii::$app->db->createCommand()->delete('{{%contest_user}}',['user_id' => $model->id])->execute();
                                Yii::$app->db->createCommand()->delete('{{%group_user}}',['user_id' => $model->id])->execute();
                                Yii::$app->db->createCommand()->delete('{{%discuss}}',['created_by' => $model->id])->execute(); 
                                Yii::$app->db->createCommand('DELETE solution_info, solution FROM solution_info, solution  WHERE solution_info.solution_id=solution.id AND solution.created_by=:uid',[':uid' => $model->id])->execute(); 
                                $this->deleteGroup($model->id);
                                $this->findModel($model->id)->delete();
                            } catch (\ErrorException $e) {
                                Yii::$app->session->setFlash('error', '删除失败:' . $e->getMessage());
                                return $this->redirect(['index']);
                            }
                            $model->delete();
                            
                        }
                       
                    }
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Delete successfully'.':'.$sum.'人'));
                }else if($action == 'enable') {
                    $sum = 0;
                    foreach ($keys as $key) {
                        if(Yii::$app->user->id != $key){
                            $sum++;
                            Yii::$app->db->createCommand()->update('{{%user}}', [
                                'status' => User::STATUS_ACTIVE
                            ], ['id' => $key])->execute();                   
                        }
                    }
                    Yii::$app->session->setFlash('success', Yii::t('app', '启用账户成功'.':'.$sum.'人'));
                }else if($action == 'disable') {
                    $sum = 0;
                    foreach ($keys as $key) {
                        if(Yii::$app->user->id != $key){
                            $sum++;
                            Yii::$app->db->createCommand()->update('{{%user}}', [
                                'status' => User::STATUS_DISABLE
                            ], ['id' => $key])->execute();                   
                        }
                    }
                    Yii::$app->session->setFlash('success', Yii::t('app', '禁用账户成功'.':'.$sum.'人'));
                }
            }else{
                Yii::$app->session->setFlash('error', '当前未选中用户，操作无效！' );
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
            $newNickname = Yii::$app->request->post('User')['nickname'];
            $newEmail = Yii::$app->request->post('User')['email'];
            $model->memo = Yii::$app->request->post('User')['memo'];
            $role = Yii::$app->request->post('User')['role'];

            if (!empty($role)) {
                $model->role = intval($role);
            }
            if(empty($newNickname)){
                $model->nickname = $model->username;
            }else{
                $model->nickname = $newNickname;
            }
            if(!empty($newEmail)){
                $model->email = $newEmail;
            }
            $model->save();
     
            if (!empty($newPassword)) {
                Yii::$app->db->createCommand()->update('{{%user}}', [
                    'password_hash' => Yii::$app->security->generatePasswordHash($newPassword,5)
                ], ['id' => $model->id])->execute();
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
        $this->deleteGroup($id);
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


    protected function deleteGroup($user_id)
    {
        $ids = Yii::$app->db->createCommand('SELECT id FROM {{%group}} WHERE created_by=:uid',[
            ':uid' => $user_id
        ])->queryColumn();
        foreach($ids as $id){
            $model = Group::findOne($id);
            if($model!=null) {
                $model->delete();
            }
        }
    }
}
