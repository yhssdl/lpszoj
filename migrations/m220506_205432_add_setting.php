<?php

use yii\db\Migration;

/**
 * Class m220506_205432_add_setting
 */
class m220506_205432_add_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', ['key' => 'isShowStatus', 'value' => '1']); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['key' => 'isShowStatus']);                         
    }

}
