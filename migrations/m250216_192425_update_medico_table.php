<?php

use yii\db\Migration;

/**
 * Class m250216_192425_update_medico_table
 */
class m250216_192425_update_medico_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('medicos', 'etiquetas', $this->text());
        $this->addColumn('medicos', 'vagas', $this->text());
        $this->addColumn('medicos', 'consultas', $this->text());
        $this->addColumn('medicos', 'retornos', $this->text());
        $this->addColumn('medicos', 'procedimentos', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('medicos', 'etiquetas');
        $this->dropColumn('medicos', 'vagas');
        $this->dropColumn('medicos', 'consultas');
        $this->dropColumn('medicos', 'retornos');
        $this->dropColumn('medicos', 'procedimentos');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250216_192425_update_medico_table cannot be reverted.\n";

        return false;
    }
    */
}
