<?php

use yii\db\Migration;

/**
 * Class m250202_001324_update_medicos_table
 */
class m250202_001324_update_medicos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->alterColumn('medicos', 'local', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // $this->alterColumn('medicos', 'local', $this->string());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250202_001324_update_medicos_table cannot be reverted.\n";

        return false;
    }
    */
}
