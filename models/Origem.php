<?php
namespace app\models;

use app\behaviors\GenerateUuid;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Origem extends ActiveRecord
{
  public static function tableName()
  {
    return '{{%origem}}';
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
          'nome',
          'slug',
          'descricao',
        ],
        'string'
      ],
    ];
  }
}