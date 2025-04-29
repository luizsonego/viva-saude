<?php
namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;

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

    /**
     * Retorna os locais de atendimento como array
     * @return array
     */
    public function getLocaisAtendimento()
    {
        return $this->local ? Json::decode($this->local) : [];
    }
    
    /**
     * Retorna os procedimentos e valores como array
     * @return array
     */
    public function getProcedimentosValores()
    {
        return $this->procedimento_valor ? Json::decode($this->procedimento_valor) : [];
    }

}

