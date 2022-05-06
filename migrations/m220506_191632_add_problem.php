<?php

use yii\db\Migration;

/**
 * Class m220506_191632_add_problem
 */
class m220506_191632_add_problem extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%polygon_problem}}', 'show_solution',  $this->smallInteger()->defaultValue(0));
        $this->addColumn('{{%problem}}', 'show_solution',  $this->smallInteger()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%polygon_problem}}', ['key' => 'show_solution']);      
        $this->delete('{{%problem}}', ['key' => 'show_solution']);                       
    }

}
