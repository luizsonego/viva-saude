<?php

use yii\db\Migration;

/**
 * Class m250215_163126_update_atendimento_anexos_table
 */
class m250215_163126_update_atendimento_anexos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('atendimento', 'anexos', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('atendimento', 'anexos', $this->string());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250215_163126_update_atendimento_anexos_table cannot be reverted.\n";

        return false;
    }
    */
}
