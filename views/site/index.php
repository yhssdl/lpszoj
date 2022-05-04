<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $contests array */
/* @var $news app\models\Discuss */

$this->title = Yii::$app->setting->get('ojName');
$newsSize = count($news);
?>
<div class="row blog">



    <div class="col-lg-9 col-md-8 list-group-item">

        <h1 class="text-center"><?= Yii::$app->setting->get('ojName') ?></h1>
        <h5 class="text-center"><?= Yii::$app->setting->get('schoolName') ?></h5>
        <hr>

        <?php
        if ($dataProvider->count > 0) {
            echo '<span class="lead">最近比赛</span>';
            echo  ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '_contest_item',
                'itemOptions' => ['tag' => false],
                'layout' => '{items}',
                'options' => ['class' => 'list-group animate__animated animate__fadeInUp'],
            ]);
        }
        ?>
        <?php if (!empty($news)) : ?>
            <hr>
            <p class="lead">最近新闻</p>
            <div class="blog">
                <?php $v = $news[0]; ?>
                <div class="animate__animated animate__fadeInUp">
                    <div>
                        <h3><?= Html::a(Html::encode($v['title']), ['/site/news', 'id' => $v['id']], ['class' => 'text-dark']) ?>
                        </h3>
                        <?= Yii::$app->formatter->asMarkdown($v['content']) ?>
                    </div>
                </div>
                <p></p>
            </div>
        <?php endif; ?>

        <p></p>
    </div>
    <div class="col-lg-3 col-md-4 animate__animated animate__fadeInUp">
        <?php if (Yii::$app->setting->get('isHomeNotice')) : ?>
            <div class="list-group-item">
                <?= Yii::$app->formatter->asMarkdown(Yii::$app->setting->get('homeNotice')) ?>
            </div>
            <br>
        <?php endif; ?>
        <?php if (!empty($news)) : ?>
            <ol class="list-group">
                <li class="list-group-item text-center"><i class="glyphicon glyphicon-bullhorn"></i> 最近新闻</li>
                <?php foreach ($news as $new) : ?>
                    <?= Html::a(Html::encode($new['title']), ['/site/news', 'id' => $new['id']], ['class' => 'list-group-item-action list-group-item']) ?>
                <?php endforeach; ?>
            </ol>
            <!-- </div> -->
            <p></p>
        <?php endif; ?>
        <?php if ((Yii::$app->setting->get('isDiscuss')) && (!empty($discusses))) : ?>
            <ol class="list-group">
                <li class="list-group-item text-center"><i class="glyphicon glyphicon-bell"></i> 最近讨论</li>

                <?php foreach ($discusses as $discuss) : ?>
                    <?= Html::a(Html::encode($discuss['title']) . '<br /><small>' . Html::encode($discuss['nickname']) . '&nbsp;&nbsp;&nbsp;' . Yii::$app->formatter->asRelativeTime($discuss['created_at']) . '&nbsp;&nbsp;&nbsp;' . Html::encode($discuss['ptitle']) . '</small>', ['/discuss/view', 'id' => $discuss['id']], ['class' => 'list-group-item list-group-item-action']) ?>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </div>
</div>