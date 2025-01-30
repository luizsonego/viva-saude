<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%especialidade}}`.
 */
class m250129_193805_create_especialidade_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%especialidade}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string()->unique(),
            'slug' => $this->string(),
            'cor' => $this->string(),
            'observacao' => $this->string(),
            'created_at' => $this->string(),
            'updated_at' => $this->string(),
            'deleted_at' => $this->string(),
        ]);

        $this->alterColumn('unidades', 'nome', $this->string()->unique());
        $this->alterColumn('grupo', 'servico', $this->string()->unique());
        $this->alterColumn('grupo', 'cor', $this->string()->unique());
        $this->alterColumn('etiqueta', 'servico', $this->string()->unique());
        $this->alterColumn('medicos', 'nome', $this->string()->unique());
        $this->alterColumn('acoes', 'nome', $this->string()->unique());
        $this->alterColumn('unidades', 'nome', $this->string()->unique());
        $this->alterColumn('origem', 'nome', $this->string()->unique());
        $this->alterColumn('prioridade', 'nome', $this->string()->unique());
        $this->alterColumn('prioridade', 'cor', $this->string()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%especialidade}}');

        $this->alterColumn('unidades', 'nome', $this->string());
        $this->alterColumn('grupo', 'servico', $this->string());
        $this->alterColumn('grupo', 'cor', $this->string());
        $this->alterColumn('etiqueta', 'servico', $this->string());
        $this->alterColumn('medicos', 'nome', $this->string());
        $this->alterColumn('acoes', 'nome', $this->string());
        $this->alterColumn('unidades', 'nome', $this->string());
        $this->alterColumn('origem', 'nome', $this->string());
        $this->alterColumn('prioridade', 'nome', $this->string());
        $this->alterColumn('prioridade', 'cor', $this->string());
    }
}
