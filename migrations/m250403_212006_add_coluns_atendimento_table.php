<?php

use yii\db\Migration;

/**
 * Class m250403_212006_add_coluns_atendimento_table
 */
class m250403_212006_add_coluns_atendimento_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('atendimento', 'prioridadeAtendimento', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250403_212006_add_coluns_atendimento_table cannot be reverted.\n";

        return false;
    }
    */
}
