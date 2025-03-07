<?php

namespace app\modules\v1\resource;

use app\models\UserIdentity;

class User extends \app\models\User
{
  public static function getUser($token)
  {
    $accessToken = UserIdentity::findIdentityByAccessToken($token);

    // if (time() > $accessToken->expire_at) {
    //     return [
    //         'status' => 403
    //     ];
    // }
    if ($accessToken) {
      $user = User::findOne(['id' => $accessToken['user_id']]);
      return $user;
    }

    return null;
  }
}