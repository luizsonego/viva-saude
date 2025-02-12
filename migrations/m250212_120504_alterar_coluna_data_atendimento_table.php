<?php

use yii\db\Migration;

/**
 * Class m250212_120504_alterar_coluna_data_atendimento_table
 */
class m250212_120504_alterar_coluna_data_atendimento_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('atendimento', 'medico_atendimento_data', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250212_120504_alterar_coluna_data_atendimento_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250212_120504_alterar_coluna_data_atendimento_table cannot be reverted.\n";

        return false;
    }
    */
}
