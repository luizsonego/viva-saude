<?php
namespace app\models;

use app\behaviors\GenerateUuid;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Prioridade extends ActiveRecord
{
  public static function tableName()
  {
    return '{{%prioridade}}';
  }

  public function behaviors()
  {
    return [
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