<?php

namespace app\models;

use yii\db\ActiveRecord;

class Local extends ActiveRecord
{
  public static function tableName()
  {
    return 'locais';
  }


  public function rules()
  {
    return [
      [['nome'], 'required'],
      [['nome', 'endereco'], 'string', 'max' => 255],
    ];
  }
}