<?php
namespace app\models;

use app\behaviors\GenerateUuid;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Etiqueta extends ActiveRecord
{
  public static function tableName()
  {
    return '{{%etiqueta}}';
  }

  public function behaviors()
  {
    return [
      'timestamp' => [
        'class' => TimestampBehavior::className(),
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
      [
        [
          'grupo'
        ],
        'integer'
      ],
    ];
  }
}