<?php
namespace app\modules\v1\controllers;

use app\helpers\TokenAuthenticationHelper;
use app\models\Acoes;
use app\models\Atendimento;
use app\models\Especialidade;
use app\models\Etiqueta;
use app\models\Medicos;
use app\models\Origem;
use app\models\Prioridade;
use app\models\Profile;
use app\models\Status;
use app\models\StatusCode;
use app\models\Unidades;
use app\models\User;
use app\modules\v1\resource\Atendente;
use app\modules\v1\resource\Atendimento as ResourceAtendimento;
use app\modules\v1\resource\Grupo;
use Yii;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\rest\Controller;

class GetController extends Controller
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


  public function actionIndex()
  {
    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "You may customize this page by editing the following file:" . __FILE__,
      'data' => ''
    ];
  }
  public function actionAtendimentos()
  {
    $user = TokenAuthenticationHelper::token();
    $modelProfile = new Profile();
    $profile = $modelProfile->find()->where(['user_id' => $user->id])->one();

    $model = new ResourceAtendimento();

    if ($profile->cargo === "gerente") {
      $data = $model->find()
        ->all();
    } else {
      $data = $model->find()
        ->where([
          'and',
          ['atendente' => $user->id],
          ['in', 'status', ['ABERTO', 'NOVO']]
        ])
        ->all();
    }

    foreach ($data as &$anexos) {
      if (!empty($anexos->anexos)) {
        json_decode($anexos->anexos) ? $anexos->anexos = json_decode($anexos->anexos) : $anexos->anexos;
      }
    }

    // foreach ($data as &$etapas) {
    //   if (!empty($etapas->etapas)) {
    //     json_decode($etapas->etapas) ? $etapas->etapas = json_decode($etapas->etapas) : $etapas->etapas;
    //   }
    // }


    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];

  }

  public function actionGrupos()
  {
    $model = new Grupo();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionEtiquetas()
  {
    $model = new Etiqueta();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionMedicos()
  {
    $model = new Medicos();
    $data = $model->find()->all();

    foreach ($data as &$medico) {
      if (!empty($medico->local)) {
        self::is_serialized($medico->local) ? $medico->local = unserialize($medico->local) : ["local" => $medico->local];
      }
    }

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }



  public function actionPrioridade()
  {
    $model = new Prioridade();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }

  public function actionOrigem()
  {
    $model = new Origem();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }

  public function actionUnidades()
  {
    $model = new Unidades();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionEspecialidade()
  {
    $model = new Especialidade();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }

  public function actionAcoes()
  {
    $model = new Acoes();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }



  public function actionAtendente()
  {
    $model = new Atendente();
    $data = $model->find()
      ->where(['access_given' => 10])
      ->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }


  public function actionProcedimentos()
  {
    $model = new Medicos();
    $data = $model->find()->all();

    $procedimentos = [];

    // Itera sobre os resultados e extrai apenas o campo "procedimento"
    foreach ($data as $item) {
      if (isset($item->procedimento_valor) && !empty($item->procedimento_valor)) {
        $decoded = json_decode($item->procedimento_valor, true);
        if (is_array($decoded)) {
          foreach ($decoded as $proc) {
            if (isset($proc['procedimento'])) {
              $procedimentos[] = $proc['procedimento'];
            }
          }
        }
      }
    }

    // Remove duplicatas
    $procedimentos = array_values(array_unique($procedimentos));

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $procedimentos
    ];
  }

  public function actionUserAuthenticaded()
  {
    $user = TokenAuthenticationHelper::token();

    $profile = Profile::findOne(['user_id' => $user->id]);
    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $profile['cargo']
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
}