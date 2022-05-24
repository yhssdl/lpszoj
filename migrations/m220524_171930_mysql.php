<?php

use app\migrations\BaseMigration;
use yii\db\Schema;
use yii\base\Security;

/**
 * Class m220524_171930_mysql
 */
class m220524_171930_mysql extends BaseMigration
{
    public function up()
    {
        $this->createTable('{{%mysql}}', [
            'id' => $this->primaryKey(),
            'command' => $this->text(),
            'description' => $this->text(),
            'alt_msg' => $this->string(),

        ], $this->tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%mysql}}');
    }
}
