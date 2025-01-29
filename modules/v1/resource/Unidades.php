<?php
namespace app\modules\v1\resource;

use app\models\Unidades as ModelsUnidades;

class Unidades extends ModelsUnidades
{
  public function fields()
  {
    $fields = parent::fields();

    unset(
      $fields['slug'],
      $fields['created_at'],
      $fields['updated_at'],
      $fields['deleted_at'],
    );

    return $fields;
  }

  public function extraFields()
  {

  }


}