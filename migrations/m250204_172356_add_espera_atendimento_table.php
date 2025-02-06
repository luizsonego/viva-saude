<?php

use yii\db\Migration;

/**
 * Class m250204_172356_add_espera_atendimento_table
 */
class m250204_172356_add_espera_atendimento_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('atendimento', 'em_espera', $this->boolean());
        $this->addColumn('atendimento', 'perfil_cliente', $this->string());
        $this->addColumn('atendimento', 'aguardando_vaga', $this->boolean());

        $this->alterColumn('atendimento', 'onde_deseja_ser_atendido', $this->string());
        $this->alterColumn('atendimento', 'medico_atendimento', $this->string());
        $this->alterColumn('atendimento', 'o_que_deseja', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('atendimento', 'em_espera');
        $this->dropColumn('atendimento', 'perfil_cliente');
        // $this->dropColumn('atendimento', 'aguardando_vaga');
    }


}
