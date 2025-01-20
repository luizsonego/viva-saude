<?php
namespace app\modules\v1\controllers;

use app\models\Acoes;
use app\models\Atendimento;
use app\models\Etiqueta;
use app\models\Medicos;
use app\models\Origem;
use app\models\Prioridade;
use app\models\Status;
use app\models\StatusCode;
use app\models\Unidades;
use app\models\User;
use app\modules\v1\resource\Grupo;
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
    $model = new Atendimento();
    $data = $model->find()->all();

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

  public function actionGrupo()
  {
    $model = new Grupo();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }

  public function actionAtendente()
  {
    $model = new User();
    $data = $model->find()
      ->where(['access_given' => 10])
      ->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }

}