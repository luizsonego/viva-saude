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
      [['nome', 'crm', 'especialidade', 'telefone', 'whatsapp', 'email'], 'required'],
      [['nome', 'crm', 'especialidade', 'telefone', 'whatsapp', 'email', 'avatar_url'], 'string'],
    ];
  }

}

