<?php

/* @var $this \yii\web\View */

/* @var $content string */
/* @var $model app\models\Contest */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\widgets\Alert;
use app\models\Contest;

AppAsset::register($this);

$this->registerJsFile('/js/jquery.countdown.min.js', ['depends' => 'yii\web\JqueryAsset']);
$model = $this->params['model'];
$status = $model->getRunStatus();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="shortcut icon" href="<?= Yii::getAlias('@web') ?>/favicon.ico">
    <style>
        .progress-bar {
            transition: none !important;
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' =>  '<img src="/images/logo.png" height="54px"/>',
        'options' => [
            'class' => 'navbar-default boxshow5',
        ],
    ]);
    $menuItems = [
        ['label' => '<span class="fa fa-home"></span> ' . Yii::t('app', 'Home'), 'url' => ['/site/index']],
        ['label' => '<span class="fa fa-book"></span> ' . Yii::t('app', 'Problems'), 'url' => ['/problem/index']],
        [   
            'label' => '<span class="fa fa-tasks"></span> ' . Yii::t('app', 'Status'), 
            'url' => ['/solution/index'],
            'visible' => Yii::$app->setting->get('isShowStatus') !=0
        ],
        [
            'label' => '<span class="fa fa-bar-chart"></span> ' . Yii::t('app', 'Rating'),
            'url' => ['/rating/problem'],
            'active' => Yii::$app->controller->id == 'rating'
        ],
        [
            'label' => '<span class="fa fa-group"></span> ' . Yii::t('app', 'Group'),
            'url' => Yii::$app->user->isGuest ? ['/group/index'] : ['/group/my-group'],
            'visible' => Yii::$app->setting->get('isDefGroup') !=0
        ],
        ['label' => '<span class="fa fa-trophy"></span> ' . Yii::t('app', 'Contests'), 'url' => ['/contest/index']],
        [
            'label' => '<i class="fa fa-info-circle"></i> '. Yii::t('app', 'Wiki'),
            'url' => ['/wiki/index'],
            'active' => Yii::$app->controller->id == 'wiki'
        ],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '<span class="fa fa-sign-in"></span> ' . Yii::t('app', 'Signup'), 'url' => ['/site/signup']];
        $menuItems[] = ['label' => '<span class="fa fa-user"></span> ' . Yii::t('app', 'Login'), 'url' => ['/site/login']];
    } else {
        if (Yii::$app->user->identity->isAdmin()) {
            $menuItems[] =  [
                'label' => '<span class="fa fa-user"></span> ' . Yii::$app->user->identity->nickname,
                'items' => [
                    ['label' => '<span class="fa fa-cog"></span> ' . Yii::t('app', 'Backend'), 'url' => ['/admin']],
                    ['label' => '<span class="fa fa-address-book"></span> ' . Yii::t('app', 'Profile'), 'url' => ['/user/view', 'id' => Yii::$app->user->id]],
                    ['label' => '<span class="fa fa-wrench"></span> ' . Yii::t('app', 'Setting'), 'url' => ['/user/setting', 'action' => 'profile']],
                    '<li class="divider"></li>',
                    ['label' => '<span class="fa fa-sign-out"></span> ' . Yii::t('app', 'Logout'), 'url' => ['/site/logout']],
                ]
            ];
        }else{
            $menuItems[] =  [
                'label' => '<span class="fa fa-user"></span> ' . Yii::$app->user->identity->nickname,
                'items' => [
                    ['label' => '<span class="fa fa-book"></span> ' . Yii::t('app', 'Profile'), 'url' => ['/user/view', 'id' => Yii::$app->user->id]],
                    ['label' => '<span class="fa fa-wrench"></span> ' . Yii::t('app', 'Setting'), 'url' => ['/user/setting', 'action' => 'profile']],
                    '<li class="divider"></li>',
                    ['label' => '<span class="fa fa-sign-out"></span> ' . Yii::t('app', 'Logout'), 'url' => ['/site/logout']],
                ]
            ];
        }
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
        'encodeLabels' => false,
        'activateParents' => true
    ]);
    NavBar::end();
    ?>

    <div class="container radius">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <div class="contest-info">
            <div class="row">
                <div class="col-md-3 text-left hidden-print">
                    <strong><?= Yii::t('app', 'Start') ?>: </strong>
                    <?= $model->start_time ?>
                </div>
                <div class="col-md-6 text-center">
                    <h2 class="contest-title">
                        <?= Html::encode($model->title) ?>
                    </h2>
                </div>
                <div class="col-md-3 text-right hidden-print">
                    <strong><?= Yii::t('app', 'End') ?>: </strong>
                    <?php
                        if (strtotime($model->end_time) >= Contest::TIME_INFINIFY)
                            echo "一直开放";
                        else
                            echo $model->end_time
                        ?>
                </div>
            </div>
            <div class="progress hidden-print">
                <div class="progress-bar progress-bar-success" id="contest-progress" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 1%;">

                </div>
            </div>
            <div class="text-center hidden-print">
                <b><?= Yii::t('app', 'Now') ?>　</b>
                <span id="nowdate"><?= date("Y-m-d H:i:s") ?></span>　
               <span>                    
                   <b>类型</b>: <?= $model->getType()?>　 <b>状态</b>: <?= $model->getRunStatus(1) ?>　
                   <?php if ($model->group_id != 0 && $model->isContestAdmin()): ?>
                                <?= Html::a('<span class="fa fa-address-book"></span> ' . Yii::t('app', 'Setting'),
                                    ['/homework/update', 'id' => $model->id]) ?>
                   
                        <?php endif; ?>
                </span>
            </div>
        </div>
        <br>
        <?php if ($status == $model::STATUS_NOT_START): ?>
            <div class="contest-countdown text-center">
                <div id="countdown"></div>
            </div>
            <?php if (!empty($model->description)): ?>
                <div class="contest-desc">
                    <?= Yii::$app->formatter->asMarkdown($model->description) ?>
                </div>
            <?php endif; ?>
        <?php elseif (!$model->canView()): ?>
            <?= $content ?>
        <?php else: ?>
            <div class="contest-view">
                <?php
                $menuItems = [
                    [
                        'label' => '<span class="fa fa-home"></span> ' . Yii::t('app', 'Information'),
                        'url' => ['contest/view', 'id' => $model->id],
                    ],
                    [
                        'label' => '<span class="fa fa-book"></span> ' . Yii::t('app', 'Problem'),
                        'url' => ['contest/problem', 'id' => $model->id],
                        'linkOptions' => ['data-pjax' => 0]
                    ],
                    [
                        'label' => '<span class="fa fa-tasks"></span> ' . Yii::t('app' , 'Status'),
                        'url' => ['contest/status', 'id' => $model->id],
                        'linkOptions' => ['data-pjax' => 0],
                    ],
                    [
                        'label' => '<span class="fa fa-glass"></span> ' . Yii::t('app', 'Standing'),
                        'url' => ['contest/standing', 'id' => $model->id],
                    ],
                ];
                if($model->enable_clarify==1 || ($model->enable_clarify==2 && $model->isContestEnd())){
                    $menuItems[] = [
                        'label' => '<span class="fa fa-comment"></span> ' . Yii::t('app', 'Clarification'),
                        'url' => ['contest/clarify', 'id' => $model->id],
                    ];
                }
                if ($model->enable_print == 1 && $model->getRunStatus() == $model::STATUS_RUNNING) {
                    $menuItems[] = [
                        'label' => '<span class="fa fa-print"></span> 打印服务',
                        'url' => ['/contest/print', 'id' => $model->id]
                    ];
                }
                if ($model->isContestEnd()) {
                    $menuItems[] = [
                        'label' => '<i class="fa fa-info-circle"></i> ' . Yii::t('app', 'Editorial'),
                        'url' => ['contest/editorial', 'id' => $model->id]
                    ];
                }
                echo Nav::widget([
                    'items' => $menuItems,
                    'options' => ['class' => 'nav nav-tabs hidden-print'],
                    'encodeLabels' => false
                ]) ?>
                <?php \yii\widgets\Pjax::begin() ?>
                <?= $content ?>
                <?php \yii\widgets\Pjax::end() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
    <span>&copy; <?= Yii::$app->setting->get('ojName') ?> OJ <?= date('Y') ?></span>
        <span>
            <?= Html::a (' 中文简体 ', '?lang=zh-CN') . '| ' .
            Html::a (' English ', '?lang=en') ;
            ?>
        </span>
    </div>
</footer>
<?php $this->endBody() ?>
<script>
    var client_time = new Date();
    var diff = new Date("<?= date("Y/m/d H:i:s")?>").getTime() - client_time.getTime();
    var start_time = new Date("<?= $model->start_time ?>");
    var end_time = new Date("<?= $model->end_time ?>");
    $("#countdown").countdown(start_time.getTime() - diff, function(event) {
        $(this).html(event.strftime('%D:%H:%M:%S'));
    });
    function clock() {
        var h, m, s, n, y, mon, d;
        var x = new Date(new Date().getTime() + diff);
        y = x.getYear() + 1900;
        if (y > 3000) y -= 1900;
        mon = x.getMonth() + 1;
        d = x.getDate();
        h = x.getHours();
        m = x.getMinutes();
        s = x.getSeconds();

        n = y + "-" + mon + "-" + d + " " + (h >= 10 ? h : "0" + h) + ":" + (m >= 10 ? m : "0" + m) + ":" + (s >= 10 ? s : "0" + s);
        document.getElementById('nowdate').innerHTML = n;
        var now_time = new Date(n);
        if (now_time < end_time) {
            var rate = (now_time - start_time) / (end_time - start_time) * 100;
            document.getElementById('contest-progress').style.width = rate + "%";
        } else {
            document.getElementById('contest-progress').style.width = "100%";
        }
        setTimeout("clock()", 1000);
    }
    clock();

    $(document).ready(function () {
        // 连接服务端
        var socket = io(document.location.protocol + '//' + document.domain + ':2120');
        var uid = '<?= Yii::$app->user->isGuest ? session_id() : Yii::$app->user->id ?>';
        // 连接后登录
        socket.on('connect', function(){
            socket.emit('login', uid);
        });
        // 后端推送来消息时
        socket.on('msg', function(msg){
            alert(msg);
        });

        $('.pre p').each(function(i, block) {  // use <pre><p>
            hljs.highlightBlock(block);
        });
    });
</script>
</body>
</html>
<?php $this->endPage() ?>
