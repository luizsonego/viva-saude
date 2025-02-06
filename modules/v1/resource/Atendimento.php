<?php
namespace app\modules\v1\resource;

use app\models\Acoes;
use app\models\Atendimento as ModelsAtendimento;
use app\models\Etiqueta;
use app\models\Medicos;
use app\models\Unidades;

class Atendimento extends ModelsAtendimento
{
  public function fields()
  {
    $fields = parent::fields();
    $fields['etiqueta'] = 'etiqueta';
    $fields['qualMedico'] = 'medico';
    $fields['acoes'] = 'acoes';
    $fields['unidades'] = 'unidades';

    return $fields;
  }

  public function extraFields()
  {

  }

  public function getEtiqueta()
  {
    return $this->hasMany(Etiqueta::className(), ['grupo' => 'id']);
  }
  public function getUnidades()
  {
    return $this->hasMany(Unidades::className(), ['id' => 'onde_deseja_ser_atendido']);
  }
  public function getMedico()
  {
    return $this->hasMany(Medicos::className(), ['medico_atendimento' => 'id']);
  }
  public function getAcoes()
  {
    return $this->hasOne(Acoes::className(), ['id' => 'o_que_deseja']);
  }

}