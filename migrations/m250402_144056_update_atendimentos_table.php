<?php

use yii\db\Migration;

/**
 * Class m250402_144056_update_atendimentos_table
 */
class m250402_144056_update_atendimentos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('atendimento', 'em_atraso', $this->boolean()->defaultValue(false));
        $this->addColumn('atendimento', 'tempo_atraso', $this->string());
        $this->addColumn('atendimento', 'temporizador', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('atendimento','em_atraso');
        $this->dropColumn('atendimento','tempo_atraso');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250402_144056_update_atendimentos_table cannot be reverted.\n";

        return false;
    }
    */
}
