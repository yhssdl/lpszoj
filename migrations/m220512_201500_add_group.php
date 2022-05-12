<?php

use app\migrations\BaseMigration;

/**
 * Class m220104_124300_add_group
 */
class m220104_124300_add_group extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%group}}', 'is_train', $this->tinyInteger()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%group}}', 'is_train');
    }
}
