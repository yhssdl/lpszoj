<?php

use yii\db\Migration;

/**
 * Class m240115_191508_add_contest_option
 */
class m240115_191508_add_contest_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%contest}}', 'show_solution', $this->smallInteger()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%contest}}', 'show_solution');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240115_191508_add_contest_option cannot be reverted.\n";

        return false;
    }
    */
}
