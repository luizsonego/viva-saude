<?php
namespace app\modules\v1\controllers;

use app\models\Etiqueta;
use app\models\Medicos;
use app\models\StatusCode;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\rest\Controller;

class SearchController extends Controller
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

  public function actionMedicosProcedimento(string $search = '')
  {
    $model = new Medicos();
    $data = $model->find()
      ->where(['like', 'procedimento_valor', $search])
      ->orderBy(['nome' => SORT_ASC])
      ->all();

    foreach ($data as &$medico) {
      if (!empty($medico->local)) {
        self::is_serialized($medico->local) ? $medico->local = unserialize($medico->local) : [$medico->local];
      }
    }


    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionMedicosLocal(string $search = '')
  {
    $model = new Medicos();
    // $data = $model->find()->where(['like', 'procedimento_valor', $search])->all();
    $data = $model->find()->where(['id' => $search])->all();

    foreach ($data as &$medico) {
      if (!empty($medico->local)) {
        self::is_serialized($medico->local) ? $medico->local = unserialize($medico->local) : [$medico->local];
      }
    }


    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionEtiquetasGrupo(int $search = 0)
  {
    $model = new Etiqueta();
    // $data = $model->find()->where(['like', 'procedimento_valor', $search])->all();
    $data = $model->find()->where(['grupo' => $search])->all();

    // foreach ($data as &$medico) {
    //   if (!empty($medico->local)) {
    //     self::is_serialized($medico->local) ? $medico->local = unserialize($medico->local) : [$medico->local];
    //   }
    // }

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }

  public function actionBuscaEtiquetas($search)
  {

    $model = new Etiqueta();
    $data = $model->find()->where(['id' => explode(',', $search)])->all();

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

}