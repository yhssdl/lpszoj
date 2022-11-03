<?php

use app\migrations\BaseMigration;

/**
 * Class m220104_124300_add_group
 */
class m221103_100200_add_user extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'memo', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'memo');
    }
}
