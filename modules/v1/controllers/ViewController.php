<?php
namespace app\modules\v1\controllers;

use app\models\Acoes;
use app\models\Atendimento;
use app\models\Especialidade;
use app\models\Etiqueta;
use app\models\Grupo;
use app\models\Medicos;
use app\models\Origem;
use app\models\Prioridade;
use app\models\Profile;
use app\models\StatusCode;
use app\models\Unidades;
use app\modules\v1\resource\Atendente;
use app\modules\v1\resource\Atendimento as ResourceAtendimento;
use app\modules\v1\resource\Etiqueta as ResourceEtiqueta;
use app\modules\v1\resource\Grupo as ResourceGrupo;
use app\modules\v1\resource\Medicos as ResourceMedicos;
use app\modules\v1\resource\Unidades as ResourceUnidades;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\rest\Controller;

class ViewController extends Controller
{
  public function behaviors()
  {
    return [
      'corsFilter' => [
        'class' => Cors::className(),
      ],
      'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
          'create' => ['POST', 'PUT', 'GET'],
        ],
      ],
    ];
  }

  public function actionEtiqueta($id)
  {
    $model = new ResourceEtiqueta();
    $data = $model->find()->where(['id' => $id])->one();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }

  public function actionMedico($id)
  {
    $model = new ResourceMedicos();
    $data = $model->find()->where(['id' => $id])->one();

    $data->etiquetas = json_decode($data->etiquetas);
    $data->procedimento_valor = json_decode($data->procedimento_valor);
    // $data->procedimento_valor = unserialize($data['procedimento_valor']);
    if (!empty($data->local)) {
      self::is_serialized($data->local) ? $data->local = unserialize($data->local) : $data->local;
    }

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  function is_serialized($data)
  {
    // Se não é uma string, não pode ser serializado
    if (!is_string($data)) {
      return false;
    }

    // Verifica se a string é 'N;' (null serializado)
    if ($data === 'N;') {
      return true;
    }

    // Verifica se a string tem pelo menos 4 caracteres (mínimo para uma string serializada)
    if (strlen($data) < 4) {
      return false;
    }

    // Verifica se a string começa com um dos caracteres de tipos serializados
    if (':' !== $data[1] || (';' !== substr($data, -1) && '}' !== substr($data, -1))) {
      return false;
    }

    // Tenta unserializar a string
    $result = @unserialize($data);
    return $result !== false || $data === 'b:0;';
  }

  public function actionAtendente($id)
  {
    $model = new Atendente();
    $data = $model->find()->where(['id' => $id])->one();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionGrupo($id)
  {
    $model = new ResourceGrupo();
    $data = $model->find()->where(['id' => $id])->one();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionEspecialidade($id)
  {
    $model = new Especialidade();
    $data = $model->find()->where(['id' => $id])->one();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionPrioridade($id)
  {
    $model = new Prioridade();
    $data = $model->find()->where(['id' => $id])->one();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionOrigem($id)
  {
    $model = new Origem();
    $data = $model->find()->where(['id' => $id])->one();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionAcao($id)
  {
    $model = new Acoes();
    $data = $model->find()->where(['id' => $id])->one();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionUnidade($id)
  {
    $model = new ResourceUnidades();
    $data = $model->find()->where(['id' => $id])->one();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionProfile($id)
  {
    $model = new Profile();
    $data = $model->find()->where(['id' => $id])->one();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionAtendimento($id)
  {
    if (empty($id) || !is_numeric($id)) {
      return [
        'status' => StatusCode::STATUS_BAD_REQUEST,
        'message' => 'ID inválido.',
        'data' => null
      ];
    }

    $model = new ResourceAtendimento();
    $data = $model->find()->where(['id' => $id])->one();

    if (!$data) {
      return [
        'status' => StatusCode::STATUS_NOT_FOUND,
        'message' => 'Atendimento não encontrado.',
        'data' => null
      ];
    }

    // Verifica se anexos é uma string antes de tentar decodificar
    if (!empty($data->anexos)) {
      if (is_string($data->anexos)) {
        $data->anexos = json_decode($data->anexos, true) ?: $data->anexos;
      }
    } else {
      $data->anexos = [];
    }

    // Verifica se etapas é uma string antes de tentar decodificar
    if (!empty($data->etapas)) {
      if (is_string($data->etapas)) {
        $data->etapas = json_decode($data->etapas, true) ?: $data->etapas;
      }
    } else {
      $data->etapas = [];
    }

    // Converter 'medicoProfile->etiquetas' para array, se necessário
    if (!empty($data->medicoProfile) && !empty($data->medicoProfile->etiquetas)) {
      if (is_string($data->medicoProfile->etiquetas)) {
        $data->medicoProfile->etiquetas = json_decode($data->medicoProfile->etiquetas, true) ?: 
          explode(',', str_replace(['[', ']', '"'], '', $data->medicoProfile->etiquetas));
      }
    }

    $temporizador = $this->processarTemporizador($data);
    
    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => [
        'atendimento' => $data,
        'temporizador' => $temporizador
      ]
    ];
  }

  private function processarTemporizador(&$data)
  {
    $statusTemporizadores = [
      'ABERTO' => 30 * 60,
      'EM ANALISE' => 60 * 60,
      'AGUARDANDO PAGAMENTO' => 24 * 60 * 60,
      'AUTORIZAÇÃO' => 3 * 60 * 60,
      'PAGAMENTO EFETUADO' => 60 * 60,
      'FILA DE ESPERA' => null,
      'AGUARDANDO VAGA' => 48 * 60 * 60
    ];

    $statusAtual = strtoupper($data->status);
    $ultimoStatusAlterado = $this->getUltimaAlteracaoStatus($data->etapas);

    if ($ultimoStatusAlterado && isset($statusTemporizadores[$statusAtual]) && $statusTemporizadores[$statusAtual] !== null) {
      $tempoExpiracao = strtotime($ultimoStatusAlterado) + $statusTemporizadores[$statusAtual];
      $tempoRestante = $tempoExpiracao - time();
      
      // Verificar se está em atraso
      if ($tempoRestante <= 0) {
        // $data->em_atraso = true;
        // $data->tempo_atraso = date('Y-m-d H:i:s', $tempoExpiracao);
        // if (!$data->save()) {
        //   error_log('Erro ao salvar o status atualizado.');
        // }
        return [
          'em_atraso' => true,
          'tempo_atraso' => date('Y-m-d H:i:s', $tempoExpiracao)
        ];
      }
      
      return [
        'tempo_restante' => max(0, $tempoRestante),
        'expira_em' => date('Y-m-d H:i:s', $tempoExpiracao),
        'em_atraso' => false
      ];
    }
    
    return null;
  }

  private function getUltimaAlteracaoStatus($etapas)
  {
    if (!is_array($etapas)) {
      return null;
    }

    $ultimoStatus = null;
    foreach ($etapas as $etapa) {
      if (isset($etapa['descricao']) && strpos(strtolower($etapa['descricao']), 'status foi alterado') !== false) {
        $ultimoStatus = $etapa['hora'] ?? null;
      }
    }
    return $ultimoStatus;
  }

}


