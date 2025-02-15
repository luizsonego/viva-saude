<?php

namespace app\helpers;

use app\models\Status;
use app\models\StatusCode;
use app\models\User;
use app\models\UserIdentity;
use Exception;
use Yii;


class TokenAuthenticationHelper
{
  public static function token()
  {
    // Obtenha o header de autorização
    $authHeader = Yii::$app->request->headers->get('Authorization') ?? '';
    // Verifique se o header segue o formato "Bearer <token>"
    if (!preg_match('/^Bearer\s+(.+)$/', $authHeader, $matches)) {
      return 0;
    }

    // Encontre o usuário pelo token
    $user = UserIdentity::findIdentityByAccessToken($matches[1]);

    // Verifique se o usuário foi encontrado
    if (!$user) {
      return 0;
    }

    return $user;
  }

  // public static function token()
  // {
  //   $authHeader = Yii::$app->request->headers->get('Authorization') ?? '';
  //   preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches);

  //   if (!$matches) {

  //     throw new Exception('Invalid Token', StatusCode::STATUS_UNAUTHORIZED);
  //   }
  //   $user = UserIdentity::findIdentityByAccessToken($matches[1]);

  //   if (!$user) {
  //     throw new Exception('Invalid Token', StatusCode::STATUS_UNAUTHORIZED);
  //   }
  //   return $user;
  // }

}