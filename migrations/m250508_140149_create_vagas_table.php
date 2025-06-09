<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%vagas}}`.
 */
class m250508_140149_create_vagas_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%locais}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string()->notNull(),
            'endereco' => $this->string()
        ]);

        $this->createTable('{{%vagas}}', [
            'id' => $this->primaryKey(),
            'medico_id' => $this->integer()->notNull(),
            'local_id' => $this->integer()->notNull(),
            'data' => $this->date()->notNull(),
            'tipo' => "ENUM('consulta', 'retorno', 'procedimento')",
            'quantidade' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_vagas_medico', 'vagas', 'medico_id', 'medicos', 'id', 'CASCADE');
        $this->addForeignKey('fk_vagas_local', 'vagas', 'local_id', 'locais', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%locais}}');
        $this->dropForeignKey('fk_vagas_medico', 'vagas');
        $this->dropForeignKey('fk_vagas_local', 'vagas');
        $this->dropTable('{{%vagas}}');
    }
}
