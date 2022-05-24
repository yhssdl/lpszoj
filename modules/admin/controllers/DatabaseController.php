<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use app\modules\admin\models\MysqlBackup;
use app\models\MysqlCmd;
use app\models\User;
use app\components\AccessRule;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

set_time_limit(900);

class DatabaseController extends Controller
{

	public $layout = 'database';
	public $menu = [];
	public $tables = [];
	public $fp;
	public $file_name;
	public $enableZip = true;


	public function behaviors()
	{
		return [
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


	protected function getPath()
	{
		$sql = new MysqlBackup();
		return $sql->path;
	}



	public function actionCreate($data = 1)
	{
		$sql = new MysqlBackup();

		$tables = $sql->getTables();

		if (!$sql->startBackup()) {

			// render error
			\yii::$app->session->setFlash('success', 'error！');
			return $this->render('index');
		}

		foreach ($tables as $tableName) {
			$sql->getColumns($tableName);
		}
		/* echo "<prE>";
		print_r($sql->getColumns ( $tableName ));
		die(); */
		if ($data) {
			foreach ($tables as $tableName) {
				$sql->getData($tableName);
			}
		}

		$file_path = $sql->endBackup();
		$file_name = explode('/', $file_path);

		\yii::$app->session->setFlash('success', '备份成功！' . end($file_name));
		$this->redirect(array(
			'index'
		));
	}



	public function actionDelete($file)
	{
		$list = $this->getFileList($file);
		$file = $list[0];

		//$this->updateMenuItems ();
		if (isset($file)) {

			$sqlFile = $this->path . basename($file);

			if (file_exists($sqlFile))

				unlink($sqlFile);
		} else
			throw new HttpException(404, Yii::t('app', 'File not found'));
		\yii::$app->session->setFlash('success', '已删除！' . $file);
		return $this->redirect(\yii::$app->request->referrer);
	}



	protected function getFileList($ext = '*.sql')
	{
		$path = $this->path;
		$dataArray = array();
		$list = array();
		$list_files = glob($path . $ext);
		if ($list_files) {
			$list = array_map('basename', $list_files);
			sort($list);
		}
		return $list;
	}



	public function actionIndex()
	{

		//$this->updateMenuItems ();

		$list = $this->getFileList();

		$list = array_merge($list, $this->getFileList('*.zip'));

		$dataArray = [];
		foreach ($list as $id => $filename) {
			$columns = array();
			$columns['id'] = $id;
			$columns['name'] = basename($filename);
			$columns['size'] = filesize($this->path . $filename);

			$columns['create_time'] = date('Y-m-d H:i:s', filectime($this->path . $filename));
			$columns['modified_time'] = date('Y-m-d H:i:s', filemtime($this->path . $filename));
			if (date('M-d-Y' . ' \a\t ' . ' g:i A', filemtime($this->path . $filename)) > date('M-d-Y' . ' \a\t ' . ' g:i A', filectime($this->path . $filename))) {
				$columns['modified_time'] = date('Y-m-d H:i:s',  filemtime($this->path . $filename));
			}

			$dataArray[] = $columns;
		}

		$dataProvider = new ArrayDataProvider([
			'allModels' => array_reverse($dataArray),
			'sort' => [
				'attributes' => [
					'modified_time' => SORT_ASC
				]
			]
		]);

		return $this->render('index', array(
			'dataProvider' => $dataProvider
		));
	}



	public function actionRestore($file = null)
	{
		ini_set('max_execution_time', 0);
		//ini_set('memory_limit', '512M');

		$message = 'OK';
		$this->layout = null;
		//$this->updateMenuItems ();

		$list = $this->getFileList();
		$list = array_merge($list, $this->getFileList('*.zip'));
		foreach ($list as $id => $filename) {

			$columns = array();
			$columns['id'] = $id;
			$columns['name'] = basename($filename);
			$columns['size'] = filesize($this->path . $filename);

			$columns['create_time'] = date('Y-m-d H:i:s', filectime($this->path . $filename));
			$columns['modified_time'] = date('Y-m-d H:i:s', filemtime($this->path . $filename));

			if (date('M-d-Y' . ' \a\t ' . ' g:i A', filemtime($this->path . $filename)) > date('M-d-Y' . ' \a\t ' . ' g:i A', filectime($this->path . $filename))) {
				$columns['modified_time'] = date('M-d-Y' . ' \a\t ' . ' g:i A', filemtime($this->path . $filename));
			}

			$dataArray[] = $columns;
		}
		$dataProvider = new ArrayDataProvider([
			'allModels' => array_reverse($dataArray),
			'sort' => [
				'attributes' => [
					'modified_time' => SORT_ASC
				]
			]
		]);

		if (isset($file)) {
			$sql = new MysqlBackup();
			$sqlZipFile = $this->path . basename($file);
			$sqlFile = $sql->unzip($sqlZipFile);
			$message = $sql->execSqlFile($sqlFile);
			if ($message == 'ok')
				\yii::$app->session->setFlash('success', '恢复成功！' . $file);
			else
				\yii::$app->session->setFlash('success', $message);
		} else {
			\yii::$app->session->setFlash('success', 'Select a file.');
			$message = 'NOK';
		}

		return $this->render('index', array(
			'error' => $message,
			'dataProvider' => $dataProvider
		));
	}


	public function actionDownload($file = null)
	{

		$list = $this->getFileList($file);
		$file = $list[0];

		if (isset($file)) {
			$sqlFile = $this->path . basename($file);
			if (file_exists($sqlFile)) {
				Yii::$app->response->sendFile($sqlFile);
			} else {
				throw new HttpException(404, Yii::t('app', 'File not found'));
			}
		}
	}


	public function actionCommand()
	{

		$dataProvider = new ActiveDataProvider([
			'query' => MysqlCmd::find()
		]);

		return $this->render('command', [
			'dataProvider' => $dataProvider,
		]);
	}


	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'command' page.
	 * @return mixed
	 */
	public function actionCreate_sql()
	{
		$model = new MysqlCmd();
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect('command');
		}

		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing model.
	 * If update is successful, the browser will be redirected to the 'command' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			return $this->redirect('command');
		}

		return $this->render('update', [
			'model' => $model,
		]);
	}

	/**
	 * Run an existing model.
	 * If update is successful, the browser will be redirected to the 'command' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionRun($id)
	{
		$model = $this->findModel($id);

		$sql_array = explode("\n", $model->command);

		foreach ($sql_array as $sql) {
			$sql = trim($sql);
			if (strlen($sql) < 5) continue;
			$command = Yii::$app->db->createCommand($sql);
			$command->execute();
		}
		Yii::$app->session->setFlash('success', 'SQL命令运行成功！');
		return $this->redirect('command');
	}

	/**
	 * Deletes an existing Discuss model.
	 * If deletion is successful, the browser will be redirected to the 'command' page.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete_sql($id)
	{
		$this->findModel($id)->delete();
		return $this->redirect(['command']);
	}


	/**
	 * Finds the Discuss model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return Discuss the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = MysqlCmd::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
	}
}
