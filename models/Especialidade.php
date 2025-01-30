<?php
namespace app\models;

use app\behaviors\GenerateUuid;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Especialidade extends ActiveRecord
{
  public static function tableName()
  {
    return '{{%especialidade}}';
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
          'observacao',
          'cor',
        ],
        'string'
      ],
    ];
  }
}