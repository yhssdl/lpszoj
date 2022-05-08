<?php

use yii\db\Migration;

/**
 * Class m220508_083532_add_setting
 */
class m220508_083532_add_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', ['key' => 'isEnablePolygon', 'value' => '0']); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['key' => 'isEnablePolygon']);                         
    }

}
