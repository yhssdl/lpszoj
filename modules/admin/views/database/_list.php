<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

echo GridView::widget ( [ 
		'id' => 'install-grid',
		'dataProvider' => $dataProvider,
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
						'template' => '{restore}{delete}{download}',
						'buttons' => [ 
								'delete' => function ($url, $model) {
                                    return Html::a('<button  class="btn btn-danger btn-flat">删除</button> ', $url, ["title" => "删除", 'data-confirm' => Yii::t('yii', '是否要删除此备份?'), 'data-method' => 'post']);
								},
								
								'restore' => function ($url, $model) {
									return Html::a ( '<button  class="btn btn-warning btn-flat">恢复备份</button> ', $url, ["title" => "恢复备份", 'data-confirm' => Yii::t('yii', '是否要恢复此备份,当前数据将回滚至此备份?'), 'data-method' => 'post']);

								} ,								
								'download' => function ($url, $model) {
									return Html::a ( '<button  class="btn btn-success btn-flat">下载</button> ', $url, ["title" => "下载文件",  'data-method' => 'post']);

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
?>
