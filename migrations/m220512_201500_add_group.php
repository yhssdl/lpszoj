<?php

use app\migrations\BaseMigration;

/**
 * Class m220512_201500_add_group
 */
class m220512_201500_add_group extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%group}}', 'is_train', $this->tinyInteger()->defaultValue(0)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%group}}', 'is_train');
    }
}
