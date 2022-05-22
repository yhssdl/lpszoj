<?php

use yii\db\Migration;

/**
 * Class m220522_102032_add_setting
 */
class m220522_102032_add_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', ['key' => 'isShowTraining', 'value' => '1']); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['key' => 'isShowTraining']);                         
    }

}
