<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\UserSearch */

$this->title = Yii::t('app', 'Users');
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <hr>
    <p>
        <?php Modal::begin([
            'header' => '<h2>' . Yii::t('app', '批量创建用户') . '</h2>',
            'toggleButton' => ['label' => Yii::t('app', '批量创建用户'), 'class' => 'btn btn-success'],
        ]);?>
        <?php $form = ActiveForm::begin(['options' => ['target' => '_blank']]); ?>

        <p class="hint-block">1.格式一:每个用户一行，格式为<code>用户名 密码</code>，中间用空格或Tab键分开。</p>
        <p class="hint-block">2.格式二:每个用户一行，格式为<code>用户名 昵称 密码</code>，中间用空格或Tab键分开。</p>
        <p class="hint-block">3.用户名只能以数字、字母、下划线，且非纯数字，长度在 4 - 32 位之间</p>
        <p class="hint-block">4.密码至少六位</p>

        <?= $form->field($generatorForm, 'names')->textarea(['rows' => 10])  ?>

        <p class="hint-block">请选择默认语言类型</p>
        <?= $form->field($generatorForm, 'language')->radioList( [
                0 => 'C',
                1 => 'C++',
                2 => 'Java',	            
                3 => 'Python3'	            
            ])->label(false)  ?>



        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Generate'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <?php Modal::end(); ?>

        选中项：
        <a id="general-user" class="btn btn-success" href="javascript:void(0);">
            设为普通用户
        </a>
        <a id="vip-user" class="btn btn-success" href="javascript:void(0);">
            设为VIP用户
        </a>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['id' => 'grid'],
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'id',
            ],
            'id',
            [
                'attribute' => 'username',
                'format' => 'raw',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'nickname',
                'format' => 'raw',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'email',
                'format' => 'raw',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'role',
                'value' => function ($model, $key, $index, $column) {
                    if ($model->role == \app\models\User::ROLE_PLAYER) {
                        return '参赛用户';
                    } else if ($model->role == \app\models\User::ROLE_USER) {
                        return '普通用户';
                    } else if ($model->role == \app\models\User::ROLE_VIP) {
                        return 'VIP 用户';
                    } else if ($model->role == \app\models\User::ROLE_ADMIN) {
                        return '管理员';
                    }
                    return 'not set';
                },
                'enableSorting' => false,
                'format' => 'raw'
            ],
            // 'status',
            // 'created_at',
            // 'updated_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    $this->registerJs('
    $("#general-user").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
           url: "'.\yii\helpers\Url::to(['/admin/user/index', 'action' => \app\models\User::ROLE_USER]).'", 
           dataType: \'json\',
           data: {keylist: keys}
        });
    });
    $("#vip-user").on("click", function () {
        var keys = $("#grid").yiiGridView("getSelectedRows");
        $.post({
           url: "'.\yii\helpers\Url::to(['/admin/user/index', 'action' => \app\models\User::ROLE_VIP]).'", 
           dataType: \'json\',
           data: {keylist: keys}
        });
    });
    ');
    ?>
</div>
