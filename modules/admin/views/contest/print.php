<?php

use yii\helpers\Html;

/* @var $model app\models\Contest */
/* @var $this \yii\web\View */

$this->title = $model->title;
$this->registerAssetBundle('yii\bootstrap\BootstrapPluginAsset');
?>
<style>
    html,
    body {
        background-color: #fff !important;
        padding: 0 20px;
    }

    pre {
        padding: 0;
        background-color: #fff;
        border: none;
    }

    .limit {
        text-align: center;
    }

    pre {
        white-space: pre-line;
        word-wrap: break-word;
        word-break: break-all;
    }

    p,
    blockquote {
        margin-bottom: 0.5rem;
    }

    table {
        max-width: 100%;
        table-layout: fixed;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col problem-view">
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <h1 class="limit"><?= $model->title ?></h1>
            <br>
            <br>
            <h3 class="limit">试题册</h3>
            <br>
            <br>
            <div class="limit">
                <p class="limit"><?= Yii::$app->setting->get('schoolName') ?></p>
            </div>

            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>

            <div class="limit">
                <h3>题目列表</h3>
                <br>
                <br>
                <?php foreach ($problems as $key => $problem) : ?>
                    <p><b><?= Html::encode($problem['title']) ?></b></p>
                <?php endforeach; ?>
            </div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <p class="limit">
                <b>重要提示</b> Java 和 Python 程序在题目所标时间空间限制基础上有 2 秒的额外运行时间和 64 MB 的额外空间
            </p>
            <br>
            <br>
            <div style="page-break-after: always"></div>
            <?php foreach ($problems as $key => $problem) : ?>

                <?php
                    $cur_id = ($problem['num'] + 1);
                ?>

                <h3 class="limit"><?= Html::encode($cur_id . '. ' . $problem['title']) ?></h3>
                <p class="limit">
                    <?= Yii::t('app', '{t, plural, =1{# second} other{# seconds}}', ['t' => intval($problem['time_limit'])]); ?>, <?= $problem['memory_limit'] ?> MB
                </p>
                <br>
                <div class="content-wrapper">
                    <?= Yii::$app->formatter->asMarkdown($problem['description']) ?>
                </div>
                <h4>输入</h4>
                <p></p>
                <div class="content-wrapper">
                    <?= Yii::$app->formatter->asMarkdown($problem['input']) ?>
                </div>
                <h4>输出</h4>
                <p></p>
                <div class="content-wrapper">
                    <?= Yii::$app->formatter->asMarkdown($problem['output']) ?>
                </div>

                <?php
                try{
                    $sample_input = unserialize($problem['sample_input']);
                    $sample_output = unserialize($problem['sample_output']);
                }catch(\Throwable $e){
                    $sample_input =  array("无","","");
                    $sample_output =  array("无","","");
                }
                ?>

                <?php if ($sample_output[0] != '') : ?>
                    <h4>样例</h4>
                    <p></p>
                <?php endif; ?>

                <?php for ($i = 0; $i < 3; $i++) : ?>
                    <?php if ($sample_input[$i] != '' || $sample_output[$i] != '') : ?>
                        <table class="table table-bordered" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                                <tr>
                                    <th>样例输入 <?= $i + 1 ?></th>
                                    <th>样例输出 <?= $i + 1 ?></th>
                                </tr>
                                <tr>
                                    <td>
                                        <pre style="margin-bottom:0"><?= Html::encode($sample_input[$i]) ?></pre>

                                    </td>
                                    <td>
                                        <pre style="margin-bottom:0"><?= Html::encode($sample_output[$i]) ?></pre>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if (!empty($problem['hint'])) : ?>
                    <h4>说明</h4>
                    <p></p>
                    <div class="content-wrapper">
                        <?= Yii::$app->formatter->asMarkdown($problem['hint']) ?>
                    </div>
                <?php endif; ?>
                <div style="page-break-after: always"></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>