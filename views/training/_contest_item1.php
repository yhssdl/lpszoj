<?php

use yii\helpers\Html;
?>

<div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title" style="cursor:pointer" data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $pos ?>" aria-expanded="true">
                <?= $t_model->title ?>
                <a class="pull-right openswitch"><span class="fa fa-angle-double-down" title="展开"></span></a>
            </h4>
        </div>
        <div id="collapse<?= $pos ?>" class="panel-collapse collapse" aria-expanded="true">
            <div class="panel-body">
                <div class="alert alert-light" style="margin-bottom:0px"><i class=" fa fa-info-circle"></i> 需要完成 <strong>『<?=$pass_title?>』</strong> 小节后，才能显示该小节内容。</div>
            </div>
            <!--panel-collapse -->
        </div>
        <!-- panel -->
    </div>










</div>