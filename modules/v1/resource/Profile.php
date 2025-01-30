<?php
namespace app\modules\v1\resource;

use app\models\User;

class Profile extends \app\models\Profile
{

  public function fields()
  {
    $fields = parent::fields();
    $fields['unidades'] = 'unidades';

    return $fields;
  }

  public function getUnidades()
  {
    return $this->hasOne(Unidades::className(), ['id' => 'unidade']);
  }
}