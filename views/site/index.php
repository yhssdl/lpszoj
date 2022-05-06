<?php

use yii\helpers\Helper;
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $contests array */
/* @var $news app\models\Discuss */

$this->title = Yii::$app->setting->get('ojName');
$count = count($full_news);
?>
<div class="row blog">



    <div class="col-lg-9 col-md-8 list-group-item">

        <h1 class="text-center"><?= Yii::$app->setting->get('ojName') ?></h1>
        <h5 class="text-center"><?= Yii::$app->setting->get('schoolName') ?></h5>


        <?php
        if ($dataProvider->count > 0) {
            echo '<hr>';
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


        <?php if (!empty($full_news)) : ?>
            <hr>
            <p class="lead">全文新闻</p>
            <div class="list-group">
                <?php $i = 1 ?>
                <?php foreach ($full_news as $new) : ?>
                    <div class="blog">
                        <div class="animate__animated animate__fadeInUp">
                            <div>
                                <h3><?= Html::a(Html::encode($new['title']), ['/site/news', 'id' => $new['id']], ['class' => 'text-dark']) ?>
                                </h3>
                                <?= Yii::$app->formatter->asMarkdown($new['content']) ?>
                            </div>
                        </div>
                        <?php if ($i < $count) {
                            echo '<br><hr><br>';
                        }
                        $i = $i + 1; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- </div> -->
            <p></p>
        <?php endif; ?>


    </div>
    <div class="col-lg-3 col-md-4 animate__animated animate__fadeInUp">
        <?php if (Yii::$app->setting->get('isHomeNotice')) : ?>
            <div class="list-group-item">
                <?= Yii::$app->formatter->asMarkdown(Yii::$app->setting->get('homeNotice')) ?>
            </div>
            <br>
        <?php endif; ?>
        <?php if (!empty($list_news)) : ?>
            <ol class="list-group">
                <li class="alert-light list-group-item text-center"><i class="fa fa-newspaper-o"></i> 最近新闻</li>
                <?php foreach ($list_news as $new) : ?>
                    <?= Html::a(Html::encode($new['title']), ['/site/news', 'id' => $new['id']], ['class' => 'text-ellipsis list-group-item-action list-group-item']) ?>
                <?php endforeach; ?>
            </ol>
            <!-- </div> -->
            <p></p>
        <?php endif; ?>
        <?php if ((Yii::$app->setting->get('isDiscuss')) && (!empty($discusses))) : ?>
            <ol class="list-group">
                <li class="list-group-item text-center"><i class="fa fa-comments-o"></i> 最近讨论</li>

                <?php foreach ($discusses as $discuss) : ?>
                    <?= Html::a('<div class="text-ellipsis">' . Html::encode($discuss['title']) . '</div><div class="text-ellipsis"><small>' . Html::encode($discuss['nickname']) . '&nbsp;&nbsp;&nbsp;' . Yii::$app->formatter->asRelativeTime($discuss['created_at']) . '&nbsp;&nbsp;&nbsp;' . Html::encode($discuss['ptitle']) . '</small></div>', ['/discuss/view', 'id' => $discuss['id']], ['class' => 'list-group-item list-group-item-action']) ?>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </div>
</div>