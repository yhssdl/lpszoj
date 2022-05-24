<div class="wrapper main-content-spacing">
	<div class="backup-default-index">
		<div class="panel">
			<header class="panel-heading form-spacing clearfix">
				<h4 style="margin:0;" class="clearfix"><?= Yii::t('app', 'Manage database backup files'); ?>
					<span class="pull-right">
						<a href="create" class="btn btn-success"> <i class="fa fa-plus"></i> <?= Yii::t('app', 'Create Backup'); ?> </a>
					</span>
				</h4>
			</header>
			<div class="panel-body">

				<?php
				echo $this->render('_list', array(
					'dataProvider' => $dataProvider
				));
				?>
			</div>
		</div>
	</div>
</div>