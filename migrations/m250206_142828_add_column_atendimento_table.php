<?php

use yii\db\Migration;

/**
 * Class m250206_142828_add_column_atendimento_table
 */
class m250206_142828_add_column_atendimento_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('atendimento', 'comentario', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('atendimento', 'comentario');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250206_142828_add_column_atendimento_table cannot be reverted.\n";

        return false;
    }
    */
}
