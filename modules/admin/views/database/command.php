<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Command');
?>

<div class="wrapper main-content-spacing">
	<div class="backup-default-index">
		<div class="panel">
			<header class="panel-heading form-spacing clearfix">
				<h4 style="margin:0;" class="clearfix"><?= Yii::t('app', 'Manage database SQL'); ?>
					<span class="pull-right">
						<a href="create_sql" class="btn btn-success"> <i class="fa fa-plus"></i> <?= Yii::t('app', 'Create SQL'); ?> </a>
					</span>
				</h4>
			</header>
			<div class="panel-body">

				<?= GridView::widget([
					'layout' => '{items}{pager}',
					'pager' => [
						'firstPageLabel' => Yii::t('app', 'First'),
						'prevPageLabel' => '« ',
						'nextPageLabel' => '» ',
						'lastPageLabel' => Yii::t('app', 'Last'),
						'maxButtonCount' => 10
					],
					'dataProvider' => $dataProvider,
					'tableOptions' => ['class' => 'table table-striped table-bordered'],
					'rowOptions' => function ($model, $key, $index, $grid) {
						return ['class' => 'animate__animated animate__fadeInUp'];
					},
					'columns' => [
						[
							'class' => 'yii\grid\SerialColumn',
							'contentOptions' => ['class' => 'text-center'],
							'headerOptions' =>  ['class' => 'text-center']

						],
						[
							'attribute' => 'description',
							'enableSorting' => false,
							'contentOptions' => ['class' => 'text-center'],
							'headerOptions' =>  ['class' => 'text-center']
						],						
						[
							'attribute' => 'command',
							'value' => function ($model, $key, $index, $column) {

								return str_replace("\n", "<br>", $model->command);
							},
							'format' => 'raw',
							'enableSorting' => false,
							'contentOptions' => ['style' => 'max-width:600px;'],
							'headerOptions' =>  ['class' => 'text-center']
						],


						array (
							'header' => '操作',
							'class' => 'yii\grid\ActionColumn',
							'template' => '{update} {delete_sql} {run}',
							'contentOptions' => ['class' => 'text-center','style' => 'width:200px;'],
							'headerOptions' =>  ['class' => 'text-center'],
							'buttons' => [ 
									'update' => function ($url, $model) {
										return Html::a ( '编辑', $url, ['class' => 'btn btn-primary','data-method' => 'post']);
									} ,	
	
									'delete_sql' => function ($url, $model) {
										return Html::a('删除', $url, ['class' => 'btn btn-danger',  'data-confirm' => Yii::t('yii', '是否要删除此备份?'), 'data-method' => 'post']);
									},
								
									'run' => function ($url, $model) {
										if(empty($model->alt_msg)){
											return Html::a ( '运行', $url, ['class' => 'btn btn-success', 'data-method' => 'post']);
										}else {
											return Html::a ( '运行', $url, ['class' => 'btn btn-warning', 'data-confirm' => Yii::t('yii', $model->alt_msg), 'data-method' => 'post']);
										}
										
									} 
							],
							'urlCreator' => function ($action, $model, $key, $index) {
									$url = Url::toRoute ( [ 
											$action,
											'id' => $model->id
									] );
									return $url;
								
							} 
						)

					],
				]); ?>

			</div>
		</div>
	</div>

</div>