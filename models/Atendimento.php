<?php
namespace app\models;

use app\behaviors\GenerateUuid;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Atendimento extends ActiveRecord
{
  public static function tableName()
  {
    return '{{%atendimento}}';
  }

  public function behaviors()
  {
    return [
      'timestamp' => [
        'class' => TimestampBehavior::className(),
      ],
    ];
  }

  public function rules()
  {
    return [
      [
        [
          'titulo',
          'slug',
          'descricao',
          'cliente',
          'cliente_telefone',
          'atendido_por',
          'atendimento_iniciado',
          'atendimento_finalizado',
          'atendimento_observacao',
          'medico_telefone',
          'medico_atendimento',
          'medico_atendimento_data',
          'medico_atendimento_observacao',
          'medico_atendimento_status',
          'medico_atendimento_local',
          'status_cliente',
          'status',
          'grupo',
          'etiqueta',
          'titular_plano',
          'cpf_titular',
          'whatsapp_titular',
          'para_quem',
          'nome_outro',
          'cpf_outro',
          'perfil_cliente',
          'onde_deseja_ser_atendido',
          'o_que_deseja',
          'comentario',
        ],
        'string'
      ],
      [['atendimento_valor'], 'number'],
      [['etapas', 'em_espera', 'aguardando_vaga', 'anexos'], 'safe'],
      [
        [
          'medico',
          'prioridade',
          'atendente',
        ],
        'integer'
      ]
    ];
  }
}