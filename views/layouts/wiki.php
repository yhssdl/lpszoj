<?php

use yii\bootstrap\Nav;

/* @var $this \yii\web\View */
/* @var $content string */

$this->title = Yii::t('app', 'Wiki');;
?>

<?php $this->beginContent('@app/views/layouts/main.php'); ?>

<?= Nav::widget([
    'items' => [
        [
            'label' => Yii::t('app', '判题说明'), 'url' => ['wiki/index']
        ],
        [
            'label' => Yii::t('app', '常见问题'), 'url' => ['wiki/faq']
        ],
        [
            'label' => Yii::t('app', '出题要求'), 'url' => ['wiki/problem'],
            'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()
        ],
        [
            'label' => Yii::t('app', 'Special Judge'), 'url' => ['wiki/spj'],
            'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()
        ],
        [
            'label' => Yii::t('app', 'OI 模式'), 'url' => ['wiki/oi'],
            'visible' => !Yii::$app->user->isGuest && Yii::$app->user->identity->isAdmin()
        ],
        ['label' => Yii::t('app', 'About'), 'url' => ['wiki/about']]
    ],
    'options' => ['class' => 'nav-tabs'],
]) ?>
<br>
<div class="row">
    <div class="col animate__animated animate__fadeInUp" style="padding:0px 32px;">
        <?= $content ?>
    </div>
</div>
<?php $this->endContent(); ?>