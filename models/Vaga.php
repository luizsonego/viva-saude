<?php

namespace app\models;

use yii\db\ActiveRecord;

class Vaga extends ActiveRecord
{
    public static function tableName()
    {
        return 'vagas';
    }

    public function rules()
    {
        return [
            [['medico_id', 'local_id', 'data', 'tipo', 'quantidade'], 'required'],
            [['medico_id', 'local_id', 'quantidade'], 'integer'],
            [['horario'], 'time'],
            [['local'], 'string'],
            [['atendimento'], 'string'],
            [['created_at', 'updated_at'], 'dateTime'],
            [['data'], 'date', 'format' => 'php:Y-m-d'],
            [['tipo'], 'in', 'range' => ['consulta', 'retorno', 'procedimento']],
        ];
    }

    public function getMedico()
    {
        return $this->hasOne(Medicos::class, ['id' => 'medico_id']);
    }

    public function getLocal()
    {
        return $this->hasOne(Local::class, ['id' => 'local_id']);
    }
}