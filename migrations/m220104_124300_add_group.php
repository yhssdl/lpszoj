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
        $this->addColumn('{{%group}}', 'logo_url', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%group}}', 'logo_url');
    }
}
