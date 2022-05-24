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
            'description' => $this->text(),
            'command' => $this->text(),
            'alt_msg' => $this->string(),

        ], $this->tableOptions);


        $this->insert('{{%mysql}}', [
            'id' => 1,
            'description' => '将所有用户语言改为 Python',
            'command' => 'UPDATE `user` SET `language`=3  WHERE 1',
            'alt_msg' => '该操作不可恢复，请谨慎操作！'
        ]);

        $this->insert('{{%mysql}}', [
            'id' => 2,
            'description' => '将所有用户语言改为 C++',
            'command' => 'UPDATE `user` SET `language`=1  WHERE 1',
            'alt_msg' => '该操作不可恢复，请谨慎操作！'
        ]);
     
        $this->insert('{{%mysql}}', [
            'id' => 3,
            'description' => '重置所有普通用户昵称',
            'command' => 'UPDATE `user` SET `nickname` = `username` WHERE role = 10',
            'alt_msg' => '该操作不可恢复，请谨慎操作！'
        ]);

        $this->insert('{{%mysql}}', [
            'id' => 4,
            'description' => '重置所有普通用户密码为123456',
            'command' => 'UPDATE user SET auth_key=\'-dvAdtT72MUMlgcdqJhmkBFuL5OElesv\' , password_hash=\'$2y$05$0NlujMcWVbRbIMdtyyzjieeuUhqKImMvW9.X4R08MAMd4e9KI5oCu\' WHERE role = 10',
            'alt_msg' => '该操作不可恢复，请谨慎操作！'
        ]);

        $this->insert('{{%mysql}}', [
            'id' => 5,
            'description' => '更新问题列表中的解题数量',
            'command' => 'UPDATE problem AS p INNER JOIN ( SELECT problem_id, COUNT(*) AS num FROM solution WHERE result=4 GROUP BY problem_id) AS c ON p.id = c.problem_id SET p.accepted = c.num\nUPDATE problem AS p INNER JOIN (SELECT problem_id, COUNT(*) AS num FROM solution GROUP BY problem_id) AS c ON p.id = c.problem_id SET p.submit = c.num',
            'alt_msg' => ''
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%mysql}}');
    }
}
