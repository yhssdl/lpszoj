<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $model app\models\Contest */
/* @var $rankResult array */

$problems = $model->problems;
$first_blood = $rankResult['first_blood'];
$result = $rankResult['rank_result'];
$submit_count = $rankResult['submit_count'];
?>

<!-- 启用封榜时显示的顶部横幅 -->

<?php if ($model->isScoreboardFrozen()) : ?>
    <div class="alert alert-light" style="text-align: left !important;"><i class=" glyphicon glyphicon-info-sign"></i>
        <?= (($model->isContestEnd())
            ? "比赛已经结束，封榜状态尚未解除，请等候管理员滚榜或解榜。"
            : "现已是封榜状态，榜单将不再实时更新，待赛后再揭晓。")
        ?>
    </div>
    <p></p>
<?php endif; ?>
<table class="table table-bordered table-rank">
    <thead>
        <tr>
            <th width="80px"><?= Yii::t('app', 'Rank') ?></th>
            <th width="200px"><?= Yii::t('app', 'Who') ?></th>
            <th title=<?= Yii::t('app', 'solved/Penalty time') ?> colspan="2"><?= Yii::t('app', 'Score') ?>
                <span data-toggle="tooltip" data-placement="top" title="分别为通过数量以及耗费时间,如果解题数相同，则按时间从少到多排序，提交运行结果被判错误的话ACM/ICPC模式将被加罚20分钟（单位：分钟）">
                    <span class="glyphicon glyphicon-question-sign"></span>
                </span>
            </th>
            <?php foreach ($problems as $key => $p) : ?>
                <th>
                    <?= Html::a('P' . ($key + 1), ['/contest/problem', 'id' => $model->id, 'pid' => $key]) ?>
                    <br>
                    <span style="color:#7a7a7a; font-size:12px">
                        <?php
                        if (isset($submit_count[$p['problem_id']]['solved']))
                            echo $submit_count[$p['problem_id']]['solved'];
                        else
                            echo 0;
                        ?>
                        /
                        <?php
                        if (isset($submit_count[$p['problem_id']]['submit']))
                            echo $submit_count[$p['problem_id']]['submit'];
                        else
                            echo 0;
                        ?>
                    </span>
                </th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <!-- ########## 榜单主体 ########## -->
        <?php for ($i = 0; $i < count($result); $i++) : ?>
            <?php $rank = $result[$i]; ?>
            <!-- 预处理 -->
            <!-- 如果关闭榜单（一些期末考试会需要到），榜单页只会显示自己的提交信息，其他人的会被隐藏。
        这时不告知用户排名，只打印 ? 号。 -->
            <!-- FIXME: 此部分不应该在视图层完成 -->
            <?php if (
                (!$model->enable_board) && (Yii::$app->user->isGuest || (!Yii::$app->user->identity->isAdmin()))
            ) {
                if (Yii::$app->user->id != $rank['user_id']) {
                    continue;
                } else {
                    $rank['finalrank'] = "?";
                }
            }
            //高亮用户自己所在的行 
            if ((!Yii::$app->user->isGuest) && Yii::$app->user->id == $rank['user_id']) {
                $front_color = "bg-isyou";
            } else {
                $front_color = "";
            }
            ?>
            <tr class="animate__animated animate__fadeInUp <?= $front_color ?>">
                <th>
                    <?= $rank['finalrank'] ?>
                </th>
                <th>
                    <?= Html::a(Html::encode($rank['nickname']), ['/user/view', 'id' => $rank['user_id']]) ?>
                </th>
                <th class="score-solved">
                    <?= $rank['solved'] ?>
                </th>
                <th class="score-time">
                    <?= min(intval($rank['time'] / 60), 99999) ?>
                </th>
                <?php
                foreach ($problems as $key => $p) {
                    $css_class = '';
                    $num = 0;
                    $time = '';
                    if (isset($rank['ac_time'][$p['problem_id']]) && $rank['ac_time'][$p['problem_id']] != -1) {
                        if ($first_blood[$p['problem_id']] == $rank['user_id']) {
                            $css_class = 'solved-first';
                        } else {
                            $css_class = 'solved';
                        }
                        $num = $rank['wa_count'][$p['problem_id']] + 1;
                        $time = intval($rank['ac_time'][$p['problem_id']]);
                    } else if (isset($rank['pending'][$p['problem_id']]) && $rank['pending'][$p['problem_id']]) {
                        $num = $rank['wa_count'][$p['problem_id']] + $rank['pending'][$p['problem_id']];
                        $css_class = 'pending';
                        $time = '';
                    } else if (isset($rank['wa_count'][$p['problem_id']])) {
                        $css_class = 'attempted';
                        $num =  $rank['wa_count'][$p['problem_id']];
                        $time = '';
                    }
                    if ($num == 0) {
                        $num = '';
                        $span = '';
                    } else if ($num == 1) {
                        $span = 'try';
                    } else {
                        $span = 'tries';
                    }
                    // 封榜的显示
                    if ($model->isScoreboardFrozen() && isset($rank['pending'][$p['problem_id']]) && $rank['pending'][$p['problem_id']]) {
                        $num = $rank['ce_count'][$p['problem_id']] + $rank['wa_count'][$p['problem_id']] . "+" .  $rank['pending'][$p['problem_id']];
                    }
                    if ((!Yii::$app->user->isGuest && $model->created_by == Yii::$app->user->id) || (!$model->isScoreboardFrozen() && $model->isContestEnd())) {
                        $url = Url::toRoute([
                            '/contest/submission',
                            'pid' => $p['problem_id'],
                            'cid' => $model->id,
                            'uid' => $rank['user_id']
                        ]);
                        echo "<th class=\"table-problem-cell {$css_class}\" style=\"cursor:pointer\" data-click='submission' data-href='{$url}'>{$time}<br><small>{$num} {$span}</small></th>";
                    } else {
                        echo "<th class=\"table-problem-cell {$css_class}\">{$time}<br><small>{$num} {$span}</small></th>";
                    }
                }
                ?>
            </tr>
        <?php endfor; ?>
    </tbody>
</table>
<?= \yii\widgets\LinkPager::widget([
    'pagination' => $pages,
    'linkOptions' => ['class' => 'page-link'],
    'maxButtonCount' => 5,
]); ?>
<?php
$js = "
$('[data-click=submission]').click(function() {
    $.ajax({
        url: $(this).attr('data-href'),
        type:'post',
        error: function(){alert('error');},
        success:function(html){
            $('#submission-content').html(html);
            $('#submission-info').modal('show');
        }
    });
});
";
$this->registerJs($js);
?>
<?php Modal::begin([
    'options' => ['id' => 'submission-info']
]); ?>
<div id="submission-content">
</div>
<?php Modal::end(); ?>