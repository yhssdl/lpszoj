<?php

use app\models\User;
use yii\bootstrap\Nav;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Groups');
?>
<?php

$DefGp = false;
if (!Yii::$app->user->isGuest){
    if (Yii::$app->setting->get('isDefGroup') == 1) {
        $DefGp = true;
    } elseif ((Yii::$app->setting->get('isDefGroup') == 2) && (Yii::$app->user->identity->role === User::ROLE_ADMIN)) {
        $DefGp = true;
    } elseif (Yii::$app->setting->get('isDefGroup') == 3 && Yii::$app->user->identity->role >= User::ROLE_VIP) {
        $DefGp = true;
    } elseif (Yii::$app->setting->get('isDefGroup') == 4 && Yii::$app->user->identity->role >= User::ROLE_TEACHER) {
        $DefGp = true;
    }
}
?>
<?php if (Yii::$app->setting->get('isDefGroup') == 0) : ?>
    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 当前小组功能已经关闭。</div>
<?php else : ?>
    <?= Nav::widget([
        'items' => [
            [
                'label' => Yii::t('app', 'My Groups'),
                'url' => ['group/my-group'],
                'visible' => !Yii::$app->user->isGuest
            ],
            [
                'label' => Yii::t('app', 'Explore'),
                'url' => ['group/index']
            ],
            [
                'label' => Yii::t('app', 'Create'),
                'url' => 'create',
                'visible' => $DefGp,
                'options' => ['class' => 'pull-right']
            ]
        ],
        'options' => ['class' => 'nav-tabs']
    ]) ?>
    <br>

    <?php
    if ($dataProvider->count > 0) {
        if ($searchModel != null) echo $this->render('_search', ['model' => $searchModel]);
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
        if ($searchModel == null) {
            echo '<div class="alert alert-light"><i class=" fa fa-info-circle"></i> 当前未加入任何小组。</div>';
        } else {
            echo '<div class="alert alert-light"><i class=" fa fa-info-circle"></i> 当前没有公开的小组。</div>';
        }
    }

    ?>

<?php endif; ?>