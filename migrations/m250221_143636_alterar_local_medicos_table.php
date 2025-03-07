<?php

use yii\db\Migration;

/**
 * Class m250221_143636_alterar_local_medicos_table
 */
class m250221_143636_alterar_local_medicos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('medicos', 'local', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250221_143636_alterar_local_medicos_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250221_143636_alterar_local_medicos_table cannot be reverted.\n";

        return false;
    }
    */
}
