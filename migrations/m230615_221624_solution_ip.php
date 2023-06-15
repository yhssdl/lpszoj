<?php

use yii\db\Migration;

/**
 * Class m230615_221624_solution_ip
 */
class m230615_221624_solution_ip extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%solution}}', 'ip', 'VARCHAR(50) NULL DEFAULT \' \' AFTER judge');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%solution}}', 'ip');
    }
}
