<?php

use yii\helpers\Html;

/* @var $model app\models\Contest */

$this->title = $model->title;
?>


        <?php echo $this->render('standing', [
            'model' => $model,
            'rankResult' => $rankResult,
            'pages' => $pages,
            'showStandingBeforeEnd' => $showStandingBeforeEnd
        ]); ?>


<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Yii::$app->setting->get('ojName') ?> OJ <?= date('Y') ?></p>
    </div>
</footer>