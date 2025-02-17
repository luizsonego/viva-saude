<?php
namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Medicos extends ActiveRecord
{

  public static function tableName()
  {
    return '{{%medicos}}';
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

