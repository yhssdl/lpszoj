<?php

$problems = $model->problems;
$rank_result = $model->getRankData(false);
$first_blood = $rank_result['first_blood'];
$result = $rank_result['rank_result'];
$submit_count = $rank_result['submit_count'];
header("Content-Type: text/csv; charset=GB2312");
header('Content-Disposition: attachment; filename="'.$model->title.'.csv"');
?>
<?= $model->title?>


序号,<?= Yii::t('app', 'School') ?>,<?= Yii::t('app', 'Username') ?>,<?= Yii::t('app', 'Nickname') ?>,解题数量,罚时(分钟),<?= Yii::t('app', 'Rank') ?><?php foreach($problems as $key => $p):?>,P<?=1+$key?>用时<?php endforeach; ?>,备注
<?php for ($i = 0, $ranking = 1; $i < count($result); $i++): ?><?php $rank = $result[$i]; ?><?php echo $ranking;$ranking++;?>
,<?= $rank['school']?>
,<?= $rank['username']?>
,<?= $rank['nickname']?>
,<?= $rank['solved']?>
,<?= intval($rank['time'] / 60) ?>
,<?= $rank['finalrank']?>
<?php foreach($problems as $key => $p) {
$num = 0;
$time = "";
if (isset($rank['ac_time'][$p['problem_id']]) && $rank['ac_time'][$p['problem_id']] > 0) {
    $num = $rank['ce_count'][$p['problem_id']] + $rank['wa_count'][$p['problem_id']] + 1;
    $time = intval($rank['ac_time'][$p['problem_id']]);
} else if (isset($rank['pending'][$p['problem_id']]) && $rank['pending'][$p['problem_id']]) {
    $num = $rank['ce_count'][$p['problem_id']] + $rank['wa_count'][$p['problem_id']] + $rank['pending'][$p['problem_id']];
    $time = '';
} else if (isset($rank['wa_count'][$p['problem_id']])) {
    $num = $rank['ce_count'][$p['problem_id']] + $rank['wa_count'][$p['problem_id']];
    $time = '';
}
if ($num == 0) {
    $num = '';
}
echo ",{$time}";
}
?>,<?= $rank['memo']?>

<?php endfor; ?>

