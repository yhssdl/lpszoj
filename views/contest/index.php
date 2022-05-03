<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use app\models\Contest;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Contests');
?>
<div class="contest-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_contest_item',
    'itemOptions' => ['tag' => false],
    'layout' => '{items}<p></p>{pager}',
    'options' => ['class' => 'list-group animate__animated animate__fadeInUp'],
    'pager' => [
        'linkOptions' => ['class' => 'page-link'],
        'maxButtonCount' => 10,
    ]
])?>
</div>