<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use app\models\User;
use app\models\Contest;

/* @var $model app\models\Contest */
/* @var $rankResult array */

$problems = $model->problems;
$first_blood = $rankResult['first_blood'];
$result = $rankResult['rank_result'];
$submit_count = $rankResult['submit_count'];
?>
<?php if ($model->isScoreboardFrozen()) : ?>
    <div class="alert alert-light"><i class=" fa fa-info-circle"></i> 现已是封榜状态，榜单将不再实时更新，待赛后再揭晓</div>
<?php endif; ?>
<table class="table table-bordered table-rank">
    <thead>
        <tr>
            <th width="60px"><?= Yii::t('app', 'Rank') ?></th>
            <th width="200px"><?= Yii::t('app', 'Who') ?></th>
            <th colspan="2"><?= Yii::t('app', 'Score') ?>
                <span data-toggle="tooltip" data-placement="top" title="分别为通过数量以及得分,第一个解题成功的拿最高分，之后随时间减少得分，答错则扣分。">
                    <span class="fa fa-question-circle"></span>
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
        <?php for ($i = 0; $i < count($result); $i++) : $rank = $result[$i]; ?>
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
                    <?= Html::a(User::getColorNameByRating($rank['nickname'], $rank['rating']), ['/user/view', 'id' => $rank['user_id']]) ?>
                </th>
                <th class="score-solved">
                    <?= $rank['solved'] ?>
                </th>
                <th class="score-time">
                    <?= intval($rank['time']) ?>
                </th>
                <?php
                foreach ($problems as $key => $p) {
                    $css_class = "";
                    $num = 0;
                    $time = "";
                    if (isset($rank['ac_time'][$p['problem_id']]) && $rank['ac_time'][$p['problem_id']] > 0) {
                        if ($first_blood[$p['problem_id']] == $rank['user_id']) {
                            $css_class = 'solved-first';
                        } else {
                            $css_class = 'solved';
                        }
                        $num = $rank['ce_count'][$p['problem_id']] + $rank['wa_count'][$p['problem_id']] + 1;
                        $time = intval($rank['ac_time'][$p['problem_id']]);
                    } else if (isset($rank['pending'][$p['problem_id']]) && $rank['pending'][$p['problem_id']]) {
                        // 封榜的显示
                        $num = $rank['ce_count'][$p['problem_id']] + $rank['wa_count'][$p['problem_id']] + $rank['pending'][$p['problem_id']];
                        $css_class = 'pending';
                        $time = '';
                    } else if (isset($rank['wa_count'][$p['problem_id']])) {
                        $css_class = 'attempted';
                        $num = $rank['ce_count'][$p['problem_id']] + $rank['wa_count'][$p['problem_id']];
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
                    // 处理封榜的显示
                    if ($model->isScoreboardFrozen() && isset($rank['pending'][$p['problem_id']]) && $rank['pending'][$p['problem_id']]) {
                        $num = $rank['ce_count'][$p['problem_id']] + $rank['wa_count'][$p['problem_id']] . "+" .  $rank['pending'][$p['problem_id']];
                    }
                    if ((!Yii::$app->user->isGuest && $model->created_by == Yii::$app->user->id) || $model->isContestEnd()) {
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