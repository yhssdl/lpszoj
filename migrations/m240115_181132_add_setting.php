<?php

use yii\db\Migration;

/**
 * Class m240115_181132_add_setting
 */
class m240115_181132_add_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', ['key' => 'isEnableShowSolution', 'value' => '1']); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['key' => 'isEnableShowSolution']);                         
    }

}
