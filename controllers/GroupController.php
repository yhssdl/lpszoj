<?php

namespace app\controllers;

use app\components\BaseController;
use Yii;
use app\models\Group;
use app\models\GroupUser;
use app\models\GroupSearch;
use app\models\Contest;
use yii\data\SqlDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use app\models\User;


/**
 * GroupController implements the CRUD actions for Group model.
 */
class GroupController extends BaseController
{
    /**
     * {@inheritdoc}
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
                        'actions' => ['create', 'accept', 'my-group', 'update', 'user-delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 显示我的小组
     * @return string
     * @throws \yii\db\Exception
     */
    public function actionMyGroup()
    {
        $count = Yii::$app->db->createCommand('
            SELECT COUNT(*) FROM {{%group}} AS g LEFT JOIN {{%group_user}} AS u ON u.group_id=g.id WHERE u.user_id=:id and g.is_train=:is_train',
            [':id' => Yii::$app->user->id,':is_train' => Group::MODE_GROUP]
        )->queryScalar();
        $dataProvider = new SqlDataProvider([
            'sql' => 'SELECT g.id,g.name,g.description,g.join_policy,g.logo_url FROM {{%group}} AS g LEFT JOIN {{%group_user}} AS u ON u.group_id=g.id WHERE u.user_id=:id AND g.is_train=:is_train AND u.role <> 0',
            'params' => [':id' => Yii::$app->user->id,':is_train' => Group::MODE_GROUP],
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $searchModel = null;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Group models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 接收邀请页面
     * @param $id
     * @param $accept
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionAccept($id, $accept = -1)
    {
        $model = $this->findModel($id);
        if ($model->isMember()) {
            return $this->redirect(['/group/view', 'id' => $model->id]);
        }
        if ($model->join_policy == Group::JOIN_POLICY_INVITE && $model->getRole() != GroupUser::ROLE_INVITING) {
            throw new ForbiddenHttpException('不允许执行此操作。');
        }
        $userDataProvider = new ActiveDataProvider([
            'query' => GroupUser::find()->where([
                'group_id' => $model->id
            ])->with('user')->orderBy(['role' => SORT_DESC])
        ]);
        if (!Yii::$app->user->isGuest) {
            $role = $model->getRole();
            if ($accept == 0 && $role == GroupUser::ROLE_INVITING) { // 拒绝小组邀请
                Yii::$app->db->createCommand()->update('{{%group_user}}', [
                    'role' => GroupUser::ROLE_REUSE_INVITATION
                ], ['user_id' => Yii::$app->user->id, 'group_id' => $model->id])->execute();
                Yii::$app->session->setFlash('info', '已拒绝');
                return $this->redirect(['/group/index']);
            } else if ($accept == 1 && $role == GroupUser::ROLE_INVITING) { // 接受小组邀请
                Yii::$app->db->createCommand()->update('{{%group_user}}', [
                    'role' => GroupUser::ROLE_MEMBER
                ], ['user_id' => Yii::$app->user->id, 'group_id' => $model->id])->execute();
                Yii::$app->session->setFlash('success', '已加入');
                return $this->redirect(['/group/view', 'id' => $model->id]);
            } else if ($model->join_policy == Group::JOIN_POLICY_FREE && $accept == 2 && !$role) { // 加入小组
                Yii::$app->db->createCommand()->insert('{{%group_user}}', [
                    'user_id' => Yii::$app->user->id,
                    'group_id' => $model->id,
                    'created_at' => new Expression('NOW()'),
                    'role' => GroupUser::ROLE_MEMBER
                ])->execute();
                Yii::$app->session->setFlash('info', '已加入');
            } else if ($model->join_policy == Group::JOIN_POLICY_APPLICATION && $accept == 3) { // 申请加入小组
                if($role){
                    Yii::$app->session->setFlash('error', '请不要重复申请');
                }else{
                    Yii::$app->db->createCommand()->insert('{{%group_user}}', [
                        'user_id' => Yii::$app->user->id,
                        'group_id' => $model->id,
                        'created_at' => new Expression('NOW()'),
                        'role' => GroupUser::ROLE_APPLICATION
                    ])->execute();
                    Yii::$app->session->setFlash('info', '已申请');
                }

            }
        }
        Yii::$app->cache->delete('role' . $model->id . '_' . Yii::$app->user->id);
        return $this->render('accept', [
            'model' => $model,
            'userDataProvider' => $userDataProvider
        ]);
    }


    /**
     * Displays a single Group model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws ForbiddenHttpException
     */
    public function actionView($id,$sort=0)
    {
        $model = $this->findModel($id);
        $role = $model->getRole();
        if (!$model->isMember() && ($role == GroupUser::ROLE_INVITING ||
                                    $role == GroupUser::ROLE_APPLICATION ||
                                    $model->join_policy == Group::JOIN_POLICY_FREE ||
                                    $model->join_policy == Group::JOIN_POLICY_APPLICATION)) {
            return $this->redirect(['/group/accept', 'id' => $model->id]);
        } else if (!$model->isMember() && $model->join_policy == Group::JOIN_POLICY_INVITE) {
            throw new ForbiddenHttpException('当前小组为私有小组，需要小组管理员邀请才能加入。');
        }
     
        $newContest = new Contest();
        $newContest->type = Contest::TYPE_HOMEWORK;
        $newContest->language = -1;
        $newContest->enable_clarify = 1;

        $query = Contest::find();

        $contestDataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
             ]
        ]);
        if(!$model->hasPermission())
        {
           $query->andwhere([
            '<>', 'status', Contest::STATUS_HIDDEN
            ]);         
        }


        $query->andWhere([
            'group_id' => $model->id
        ])->orderBy(['start_time' => SORT_DESC, 'end_time' => SORT_ASC, 'id' => SORT_DESC]);


        if ($newContest->load(Yii::$app->request->post())) {
            if (!$model->hasPermission()) {
                throw new ForbiddenHttpException('不允许执行此操作。');
            }
            $newContest->group_id = $model->id;
            $newContest->scenario = Contest::SCENARIO_ONLINE;
            $newContest->status = Contest::STATUS_VISIBLE;
            $newContest->save();
            return $this->redirect(['/homework/update', 'id' => $newContest->id]);;
        }

        return $this->render('view', [
            'model' => $model,
            'contestDataProvider' => $contestDataProvider,
            'newContest' => $newContest
        ]);
    }


   
    /**
     * Displays a single Group model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws ForbiddenHttpException
     */
    public function actionUser($id,$sort=0)
    {
        $model = $this->findModel($id);
        $role = $model->getRole();
        if (!$model->isMember() && ($role == GroupUser::ROLE_INVITING ||
                                    $role == GroupUser::ROLE_APPLICATION ||
                                    $model->join_policy == Group::JOIN_POLICY_FREE ||
                                    $model->join_policy == Group::JOIN_POLICY_APPLICATION)) {
            return $this->redirect(['/group/accept', 'id' => $model->id]);
        } else if (!$model->isMember() && $model->join_policy == Group::JOIN_POLICY_INVITE) {
            throw new ForbiddenHttpException('不允许执行此操作。');
        }
        $newGroupUser = new GroupUser();

        $count = Yii::$app->db->createCommand('
            SELECT COUNT(*) FROM {{%group_user}} WHERE group_id=:id',
            [':id' => $model->id]
        )->queryScalar();

        if($sort==0){
            $s = 's.solved DESC,u.user_id ASC';
        } else {
            $s = 'u.role DESC,u.user_id ASC,s.solved DESC';
        }

        $userDataProvider = new SqlDataProvider([
            'sql' => "SELECT u.*, s.solved FROM group_user as u LEFT JOIN (SELECT  COUNT(DISTINCT `solution`.problem_id) AS solved, `solution`.created_by FROM solution LEFT JOIN `contest` `c` ON `c`.`id`=`solution`.`contest_id` WHERE (`solution`.`contest_id` IS NULL OR (`solution`.`contest_id` IS NOT NULL AND (`c`.`type`=4 and NOW()>`c`.`end_time`) OR `c`.`type`<>4)) AND result=4 GROUP BY `solution`.created_by) AS s ON u.user_id = s.created_by WHERE u.group_id = :id ORDER BY " . $s,
            'params' => [':id' => $model->id],
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 60,
            ],
        ]);


        if ($newGroupUser->load(Yii::$app->request->post())) {
            if (!$model->hasPermission()) {
                throw new ForbiddenHttpException('不允许执行此操作。');
            }
            $usernames = str_replace("\r","",$newGroupUser->username);
            $usernames = explode("\n", trim($usernames));
            $count = count($usernames);
            $join = 0;
            $role = $newGroupUser->role;
		for ($i = 0; $i < $count; ++$i) {
			if (empty($usernames[$i]))
            	continue;
     		    $newGroupUser = new GroupUser();            
                $newGroupUser->username = $usernames[$i];
	            //　查找用户ID 以及查看是否已经加入小组中
	            $query = (new Query())->select('u.id as user_id, g.role as user_role, count(g.user_id) as exist')
	                ->from('{{%user}} as u')
	                ->leftJoin('{{%group_user}} as g', 'g.user_id=u.id and g.group_id=:gid', [':gid' => $model->id])
	                ->where('u.username=:name', [':name' => $newGroupUser->username])
	                ->one();
	            if (!isset($query['user_id'])) {
	                Yii::$app->session->setFlash('error', $newGroupUser->username.',不存在该用户');
	            } else if (!$query['exist']) {
	                $newGroupUser->role = $role;
	                $newGroupUser->created_at = new Expression('NOW()');
	                $newGroupUser->user_id = $query['user_id'];
	                $newGroupUser->group_id = $model->id;
	                $newGroupUser->save();
	                ++$join;
	            } else {
                    if (isset($query['user_role']) and $query['user_role']>=4) {
                        //已经在小组中
                        continue;
                    }
	                Yii::$app->db->createCommand()->update('{{%group_user}}', [
	                    'role' => $role
	                ], ['user_id' => $query['user_id'], 'group_id' => $model->id])->execute();
	                ++$join;
	            }
            }
            Yii::$app->session->setFlash('success', $join.'个用户已邀请');
            return $this->refresh();
        }

        return $this->render('user', [
            'model' => $model,
            'userDataProvider' => $userDataProvider,
            'newGroupUser' => $newGroupUser
        ]);
    }    

    /**
     * Creates a new Group model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Group();
        $model->is_train = 0;
        $model->id = 0;
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
     * Updates an existing Group model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws ForbiddenHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!Yii::$app->user->isGuest && ($model->getRole() == GroupUser::ROLE_LEADER || Yii::$app->user->identity->isAdmin())) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }

            return $this->render('update', [
                'model' => $model,
            ]);
        }

        throw new ForbiddenHttpException('不允许执行此操作。');
    }

    /**
     * 从小组中删除用户
     * @param $id
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionUserDelete($id)
    {
        $groupUser = GroupUser::findOne($id);
        $group = $this->findModel($groupUser->group_id);
        if ($group->hasPermission() && $groupUser->role != GroupUser::ROLE_LEADER) {
            Yii::$app->cache->delete('role' . $group->id . '_' . $groupUser->user_id);
            $groupUser->delete();
            return $this->redirect(['/group/user', 'id' => $group->id]);
        }

        throw new ForbiddenHttpException('不允许执行此操作。');
    }

    /**
     * @param $id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUserUpdate($id, $role = 0)
    {
        $groupUser = GroupUser::findOne($id);
        $user = User::findOne($groupUser->user_id);
        $group = $this->findModel($groupUser->group_id);
        if (!$group->hasPermission()) {
            throw new ForbiddenHttpException('不允许执行此操作。');
        }
        if ($role == 1) { // 同意加入
            $groupUser->role = GroupUser::ROLE_MEMBER;
        } else if ($role == 2) { // 拒绝申请
            $groupUser->role = GroupUser::ROLE_REUSE_APPLICATION;
        } else if ($role == 3) { // 重新邀请
            $groupUser->role = GroupUser::ROLE_INVITING;
        } else if ($role == 4 && $group->getRole() == GroupUser::ROLE_LEADER) { // 设为管理员
            $groupUser->role = GroupUser::ROLE_MANAGER;
        } else if ($role == 5 && $group->getRole() == GroupUser::ROLE_LEADER) { // 设为普通成员
            $groupUser->role = GroupUser::ROLE_MEMBER;
        } else if ($role == 6 && $group->getRole() >= GroupUser::ROLE_MANAGER && $groupUser->role==GroupUser::ROLE_MEMBER) { // 重置密码
            if($user->role==User::ROLE_USER){
                Yii::$app->db->createCommand()->update('{{%user}}', [
                    'password_hash' => Yii::$app->security->generatePasswordHash('123456',5)
                ], ['id' => $groupUser->user_id])->execute();
                Yii::$app->session->setFlash('success', $groupUser->user->username.'的密码已经重置为：123456');
            }else{
                Yii::$app->session->setFlash('error', $groupUser->user->username.'不是普通用户，不能对其进行密码重置。');
            }
            

        } else if ($role == 7 && $group->getRole() >= GroupUser::ROLE_MANAGER && $groupUser->role==GroupUser::ROLE_MEMBER) { // 重置昵称
            if($user->role==User::ROLE_USER){
                Yii::$app->db->createCommand()->update('{{%user}}', [
                        'nickname' =>  $groupUser->user->username
                    ], ['id' => $groupUser->user_id])->execute();
                Yii::$app->session->setFlash('success', $groupUser->user->username.'的昵称已经重置！');
            }else{
                Yii::$app->session->setFlash('error', $groupUser->user->username.'不是普通用户，不能对其进行昵称重置。'); 
            }
        }
        
        if ($role != 0) {
            $groupUser->update();
            Yii::$app->cache->delete('role' . $group->id . '_' . $groupUser->user_id);
            return $this->redirect(['/group/user', 'id' => $group->id]);
        }

        return $this->renderAjax('user_manager', [
            'model' => $group,
            'groupUser' => $groupUser
        ]);
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
        $model = $this->findModel($id);
        if (!Yii::$app->user->isGuest && ($model->created_by === Yii::$app->user->id || Yii::$app->user->identity->isAdmin())) {
            $model->delete();
            Yii::$app->session->setFlash('success', '已删除');
            return $this->redirect(['index']);
        }

        throw new ForbiddenHttpException('不允许执行此操作。');
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
}
