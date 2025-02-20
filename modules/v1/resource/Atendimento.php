<?php
namespace app\modules\v1\resource;

use app\models\Acoes;
use app\models\Atendimento as ModelsAtendimento;
use app\models\Especialidade;
use app\models\Etiqueta;
use app\models\Medicos;
use app\models\Prioridade;
use app\models\Profile;
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
    $fields['prioridadeAtendimento'] = 'prioridadeAtendimento';
    $fields['especialidades'] = 'especialidades';
    $fields['profile'] = 'profile';

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
  public function getPrioridadeAtendimento()
  {
    return $this->hasOne(Prioridade::className(), ['id' => 'prioridade']);
  }
  public function getEspecialidades()
  {
    return $this->hasOne(Especialidade::className(), ['id' => 'o_que_deseja']);
  }
  public function getProfile()
  {
    return $this->hasOne(Profile::className(), ['user_id' => 'atendente']);
  }

}