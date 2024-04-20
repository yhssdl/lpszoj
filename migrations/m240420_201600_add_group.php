<?php

use app\migrations\BaseMigration;

/**
 * Class m220512_201500_add_group
 */
class m240420_201600_add_group extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%group}}', 'sort_id', $this->integer()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%group}}', 'sort_id');
    }
}
