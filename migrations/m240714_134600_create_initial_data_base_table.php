<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%initial_data_base}}`.
 */
class m240714_134600_create_initial_data_base_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%profile%}}', [
            'id' => $this->string(36)->notNull(),
            'name' => $this->string(90),
            'phone' => $this->string(),
            'whatsapp' => $this->string(),
            'email' => $this->string(),
            'user_id' => $this->integer()->comment('id referencia com tabela de login'),
            'observation' => $this->string(),
            'unidade' => $this->string(),
            'cargo' => $this->string(),
            'avatar_url' => $this->string(),
            'created_at' => $this->string(),
            'updated_at' => $this->string(),
            'deleted_at' => $this->string(),
        ], $tableOptions);

        //grupo de especialidade
        $this->createTable(
            '{{%grupo%}}',
            [
                'id' => $this->primaryKey(),
                'servico' => $this->string(),
                'slug' => $this->string(),
                'descricao' => $this->string(),
                'cor' => $this->string(),
                'created_at' => $this->string(),
                'updated_at' => $this->string(),
                'deleted_at' => $this->string(),
            ],
            $tableOptions
        );
        $this->createTable(
            '{{%etiqueta%}}',
            [
                'id' => $this->primaryKey(),
                'servico' => $this->string(),
                'grupo' => $this->integer(), //seleciona a qual grupo pertence
                'slug' => $this->string(),
                'descricao' => $this->string(),
                'cor' => $this->string(),
                'created_at' => $this->string(),
                'updated_at' => $this->string(),
                'deleted_at' => $this->string(),
            ],
            $tableOptions
        );


        $this->createTable('{{%status%}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(),
            'slug' => $this->string(),
        ]);

        $this->createTable('{{%atendimento%}}', [
            'id' => $this->primaryKey(),
            'titular_plano' => $this->string(),
            'cpf_titular' => $this->string(),
            'whatsapp_titular' => $this->string(),
            'para_quem' => $this->string(),
            'nome_outro' => $this->string(),
            'cpf_outro' => $this->string(),
            'o_que_deseja' => $this->integer(),
            'onde_deseja_ser_atendido' => $this->integer(),
            'medico_atendimento' => $this->integer(),
            'observacoes' => $this->string(),
            'titulo' => $this->string(),
            'slug' => $this->string(),
            'descricao' => $this->string(),
            'status' => $this->string(),
            'prioridade' => $this->integer(),
            'grupo' => $this->string(),
            'etiqueta' => $this->string(),
            'cliente' => $this->string(),
            'cliente_telefone' => $this->string(),
            'atendido_por' => $this->string(),
            'atendimento_iniciado' => $this->string(),
            'atendimento_finalizado' => $this->string(),
            'atendimento_observacao' => $this->string(),
            'atendimento_valor' => $this->decimal(10, 2),
            'etapas' => $this->json(),
            'medico' => $this->integer(),
            'medico_telefone' => $this->string(),
            'medico_atendimento_data' => $this->dateTime(),
            'medico_atendimento_observacao' => $this->string(),
            'medico_atendimento_status' => $this->string(),
            'medico_atendimento_local' => $this->string(),
            'status_cliente' => $this->string(),
            'anexos' => $this->json(),
            'created_at' => $this->string(),
            'updated_at' => $this->string(),
            'deleted_at' => $this->string(),
        ], $tableOptions);

        $this->createTable('{{%historico%}}', [
            'id' => $this->primaryKey(),
            'atendimento_id' => $this->integer(),
            'status' => $this->integer(),
            'prioridade' => $this->integer(),
            'date' => $this->string(),
            'inicio_atendimento' => $this->dateTime(),
            'conclusao_atendimento' => $this->dateTime(),
            'created_at' => $this->string(),
            'updated_at' => $this->string(),
        ], $tableOptions);

        $this->createTable('{{%medicos%}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(),
            'local' => $this->string(),
            'especialidade' => $this->string(),
            'telefone' => $this->string(),
            'whatsapp' => $this->string(),
            'email' => $this->string(),
            'horarios' => $this->json(),
            'procedimento_valor' => $this->json(),
            'avatar_url' => $this->string()
        ], $tableOptions);

        $this->createTable('{{%acoes%}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(),
            'slug' => $this->string(),
            'descricao' => $this->string(),
            'tempo' => $this->string(),
            'obrigatorio' => $this->integer()->defaultValue(0),
            'created_at' => $this->string(),
            'updated_at' => $this->string(),
            'deleted_at' => $this->string(),
        ], $tableOptions);

        $this->createTable('{{%unidades%}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(),
            'slug' => $this->string(),
            'descricao' => $this->string(),
            'created_at' => $this->string(),
            'updated_at' => $this->string(),
            'deleted_at' => $this->string(),
        ], $tableOptions);
        $this->createTable('{{%origem%}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(),
            'slug' => $this->string(),
            'descricao' => $this->string(),
            'created_at' => $this->string(),
            'updated_at' => $this->string(),
            'deleted_at' => $this->string(),
        ], $tableOptions);
        $this->createTable('{{%prioridade%}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(),
            'slug' => $this->string(),
            'descricao' => $this->string(),
            'cor' => $this->string(),
            'created_at' => $this->string(),
            'updated_at' => $this->string(),
            'deleted_at' => $this->string(),
        ], $tableOptions);


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%profile}}');
        $this->dropTable('{{%grupo}}');
        $this->dropTable('{{%etiqueta}}');
        $this->dropTable('{{%prioridade}}');
        $this->dropTable('{{%status}}');
        $this->dropTable('{{%atendimento}}');
        $this->dropTable('{{%historico}}');
        $this->dropTable('{{%medicos}}');
        $this->dropTable('{{%acoes}}');
        $this->dropTable('{{%unidades}}');
        $this->dropTable('{{%origem}}');
    }
}
