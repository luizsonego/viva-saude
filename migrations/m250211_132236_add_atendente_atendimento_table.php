<?php

use yii\db\Migration;

/**
 * Class m250211_132236_add_atendente_atendimento_table
 */
class m250211_132236_add_atendente_atendimento_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('atendimento', 'atendente', $this->integer());
        // $this->addForeignKey('fk_atendimento_atendente', 'atendimento', 'atendente', 'profile', 'id', 'CASCADE', 'CASCADE');

        $this->addColumn('atendimento', 'comprovante', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('atendimento', 'atendente');
        $this->dropColumn('atendimento', 'comprovante');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250211_132236_add_atendente_atendimento_table cannot be reverted.\n";

        return false;
    }
    */
}
