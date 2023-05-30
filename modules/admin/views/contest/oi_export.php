<?php

$problems = $model->problems;
$rank_result = $model->getOIRankData(false);
$first_blood = $rank_result['first_blood'];
$result = $rank_result['rank_result'];
$submit_count = $rank_result['submit_count'];
header("Content-Type: text/csv; charset=GB2312");
header('Content-Disposition: attachment; filename="'.$model->title.'.csv"');
?>
<?= $model->title ?>


序号,<?= Yii::t('app', 'School') ?>,<?= Yii::t('app', 'Username') ?>,<?= Yii::t('app', 'Nickname') ?>,首次总分,最终总分,<?= Yii::t('app', 'Rank') ?>,用时(分钟)<?php foreach($problems as $key => $p):?>,P<?=1+$key?><?php endforeach; ?>,备注
<?php for($i = 0, $ranking = 1; $i < count($result); $i++):?><?php $rank = $result[$i]; ?><?php echo $ranking;$ranking++;?>
,<?= $rank['school']?>
,<?= $rank['username']?>
,<?= $rank['nickname']?>
,<?= $rank['total_score']?>
,<?= $rank['correction_score'] ?>
,<?= $rank['finalrank']?>
,<?= intval($rank['total_time']) ?>
<?php foreach ($problems as $key => $p) {
$score = "";
$max_score = "";
if (isset($rank['score'][$p['problem_id']])) {
$score = $rank['score'][$p['problem_id']];
$max_score = $rank['max_score'][$p['problem_id']];
}
echo ",".$max_score;
}
?>,<?= $rank['memo']?>

<?php endfor; ?>