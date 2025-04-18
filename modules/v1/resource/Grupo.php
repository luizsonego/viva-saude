<?php
namespace app\modules\v1\resource;

use app\models\Etiqueta;
use app\models\Grupo as ModelsGrupo;

class Grupo extends ModelsGrupo
{
  public function fields()
  {
    $fields = parent::fields();
    $fields['etiqueta'] = 'etiqueta';

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

  public function getEtiqueta()
  {
    return $this->hasMany(Etiqueta::className(), ['grupo' => 'id']);
  }

}