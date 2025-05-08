<?php

use yii\db\Migration;

/**
 * Class m250505_143724_update_etiqueta_table
 */
class m250505_143724_update_etiqueta_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('servico', 'etiqueta');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('etiqueta', 'servico', $this->string());
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250505_143724_update_etiqueta_table cannot be reverted.\n";

        return false;
    }
    */
}
