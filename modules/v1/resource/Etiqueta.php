<?php
namespace app\modules\v1\resource;

use app\models\Etiqueta as ModelsEtiqueta;

class Etiqueta extends ModelsEtiqueta
{
  public function fields()
  {
    $fields = parent::fields();

    unset(
      $fields['slug'],
      $fields['etiqueta'],
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