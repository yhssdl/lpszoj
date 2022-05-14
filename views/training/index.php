<?php

use app\models\User;
use yii\bootstrap\Nav;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Groups');
?>
    <?php
    if ($dataProvider->count > 0) {
        echo ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_group_item',
            'itemOptions' => ['tag' => false],
            'layout' => '{items}<p></p>{pager}',
            'options' => ['class' => 'list-group animate__animated animate__fadeInUp'],
            'pager' => [
                'linkOptions' => ['class' => 'page-link'],
                'maxButtonCount' => 10,
            ]
        ]);
    } else {
        echo '<div class="alert alert-light"><i class=" fa fa-info-circle"></i> 当前没有可以训练的内容。</div>';
    }

    ?>
