<?php

use yii\db\Migration;

/**
 * Class m220504_064732_add_setting
 */
class m220504_064732_add_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', ['key' => 'isHomeNotice', 'value' => '1']); 
        $this->insert('{{%setting}}', ['key' => 'homeNotice', 'value' => '<h4>关于</h4><p>Online Judge系统（简称OJ）是一个在线的判题系统。 用户可以在线提交程序多种程序（如C、C++、Java）源代码，系统对源代码进行编译和执行， 并通过预先设计的测试数据来检验程序源代码的正确性。</p>']);           
        $this->insert('{{%setting}}', ['key' => 'defaultLanguage', 'value' => '-1']); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['key' => 'isHomeNotice']);                    
        $this->delete('{{%setting}}', ['key' => 'homeNotice']); 
        $this->delete('{{%setting}}', ['key' => 'defaultLanguage']);         
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220504_064732_add_setting cannot be reverted.\n";

        return false;
    }
    */
}
