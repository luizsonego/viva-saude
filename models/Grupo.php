<?php
namespace app\models;

use app\behaviors\GenerateUuid;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Grupo extends ActiveRecord
{
  public static function tableName()
  {
    return '{{%grupo}}';
  }

  public function behaviors()
  {
    return [
      'timestamp' => [
        'class' => TimestampBehavior::className(),
      ],
      'generateUUid' => [
        'class' => GenerateUuid::className(),
      ],
    ];
  }


  public function rules()
  {
    return [
      [
        [
          'servico',
          'slug',
          'descricao',
          'cor'
        ],
        'string'
      ],
    ];
  }
}