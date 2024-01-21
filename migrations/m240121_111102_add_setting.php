<?php

use yii\db\Migration;

/**
 * Class m240115_181132_add_setting
 */
class m240121_111102_add_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', ['key' => 'isAdminShowSolution', 'value' => '1']); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['key' => 'isAdminShowSolution']);                         
    }

}
