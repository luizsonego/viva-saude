<?php

use yii\db\Migration;

/**
 * Class m250609_132549_update_vagas_table
 */
class m250609_132549_update_vagas_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('vagas', 'horario', $this->time()->notNull());
        $this->addColumn('vagas', 'local', $this->string());
        $this->addColumn('vagas', 'atendimento', $this->string());
        $this->addColumn('vagas', 'created_at', $this->dateTime()->notNull());
        $this->addColumn('vagas', 'updated_at', $this->dateTime()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('vagas', 'horario');
        $this->dropColumn('vagas', 'local');
        $this->dropColumn('vagas', 'atendimento');
        $this->dropColumn('vagas', 'created_at');
        $this->dropColumn('vagas', 'updated_at');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250609_132549_update_vagas_table cannot be reverted.\n";

        return false;
    }
    */
}
