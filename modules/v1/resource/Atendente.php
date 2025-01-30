<?php
namespace app\modules\v1\resource;

use app\models\User;
use app\modules\v1\resource\Profile;

class Atendente extends User
{

  public function fields()
  {
    $fields = parent::fields();
    $fields['profile'] = 'profile';

    return $fields;
  }

  public function getProfile()
  {
    return $this->hasOne(Profile::className(), ['user_id' => 'id']);
  }
}