<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

echo GridView::widget ( [ 
	'layout' => '{items}{pager}',
	'pager' => [
		'firstPageLabel' => Yii::t('app', 'First'),
		'prevPageLabel' => '« ',
		'nextPageLabel' => '» ',
		'lastPageLabel' => Yii::t('app', 'Last'),
		'maxButtonCount' => 10
	],
		'dataProvider' => $dataProvider,
		'tableOptions' => ['class' => 'table table-striped table-bordered table-text-center'],
		'rowOptions' => function($model, $key, $index, $grid) {
            return ['class' => 'animate__animated animate__fadeInUp'];
        },  
		'columns' => array (
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'name',
                'label' => '文件名称',
            ],
            [
                'attribute'=>'size',
                'label' => '文件大小',
               'format'=>'shortSize',
            ],
            [
                'attribute'=>'create_time',
                'label' => '创建时间',
            ],
				array (
						'header' => '操作',
						'class' => 'yii\grid\ActionColumn',
						'template' => '{restore} {delete} {download}',
						'contentOptions' => ['style' => 'width:200px;'],
						'buttons' => [ 
								'restore' => function ($url, $model) {
									return Html::a ( '恢复', $url, ['class' => 'btn btn-warning',"title" => "恢复备份", 'data-confirm' => Yii::t('yii', '是否要恢复此备份,当前数据将回滚至此备份?'), 'data-method' => 'post']);
								} ,	

								'delete' => function ($url, $model) {
                                    return Html::a('删除', $url, ['class' => 'btn btn-danger',"title" => "删除", 'data-confirm' => Yii::t('yii', '是否要删除此备份?'), 'data-method' => 'post']);
								},
							
								'download' => function ($url, $model) {
									return Html::a ( '下载', $url, ['class' => 'btn btn-success',"title" => "下载文件",  'data-method' => 'post']);
								} 
						],
						'urlCreator' => function ($action, $model, $key, $index) {
							
								$url = Url::toRoute ( [ 
										$action,
										'file' => $model ['name'] 
								] );
								return $url;
							
						} 
				)
				 
		) 
] );
