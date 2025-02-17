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
    $model = new Atendimento();
    $data = $model->find()->where(['id' => $id])->one();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }


}