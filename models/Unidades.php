<?php
namespace app\models;

use app\behaviors\GenerateUuid;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Unidades extends ActiveRecord
{
  public static function tableName()
  {
    return '{{%unidades}}';
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
          'nome',
          'slug',
          'descricao',
        ],
        'string'
      ],
    ];
  }
}