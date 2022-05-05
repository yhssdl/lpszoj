<?php

use yii\db\Migration;

/**
 * Class m220505_210932_add_setting
 */
class m220505_210932_add_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', ['key' => 'isShowError', 'value' => '1']); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['key' => 'isShowError']);                         
    }

}
