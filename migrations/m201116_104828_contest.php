<?php

use yii\db\Migration;

/**
 * Class m201116_104828_contest
 */
class m201116_104828_contest extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{contest}}', 'language', 'SMALLINT NOT NULL DEFAULT -1');
        $this->addColumn('{{contest}}', 'clarification', 'SMALLINT NOT NULL DEFAULT 0');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{contest}}', 'language');
        $this->dropColumn('{{contest}}', 'clarification');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201116_104828_contest cannot be reverted.\n";

        return false;
    }
    */
}
