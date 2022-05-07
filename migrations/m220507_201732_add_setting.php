<?php

use yii\db\Migration;

/**
 * Class m220507_201732_add_setting
 */
class m220507_201732_add_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', ['key' => 'showMode', 'value' => '0']); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['key' => 'showMode']);                         
    }

}
