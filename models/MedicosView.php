<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class MedicosView extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%ViewMedicos}}';
    }

    public function behaviors()
    {
        return [
            // 'timestamp' => [
            //   'class' => TimestampBehavior::className(),
            // ],
        ];
    }


    public function rules()
    {
        return [
            [
                [
                    'nome',
                    'especialidade',
                    'telefone',
                    'whatsapp',
                    'email',
                    'local',
                    'avatar_url'
                ],
                'string'
            ],
            [['horarios', 'procedimento_valor', 'etiquetas', 'vagas'], 'safe'],
        ];
    }
}
