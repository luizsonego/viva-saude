<?php
namespace app\modules\v1\controllers;

use app\helpers\SlugfyHelper;
use app\helpers\TokenAuthenticationHelper;
use app\models\Acoes;
use app\models\Atendimento;
use app\models\Etiqueta;
use app\models\Grupo;
use app\models\Medicos;
use app\models\Origem;
use app\models\Prioridade;
use app\models\Profile;
use app\models\Status;
use app\models\Unidades;
use app\models\StatusCode;
use app\models\User;
use Yii;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\rest\Controller;


class CreateController extends Controller
{
  public function actions()
  {
    $actions = parent::actions();
    unset(
      $actions['create'],
      $actions['update'],
      $actions['view'],
      $actions['index']
    );

    return $actions;
  }
  public function behaviors()
  {
    return [
      'corsFilter' => [
        'class' => Cors::className(),
        'cors' => [
          'Origin' => ['*'],
          'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
          'Access-Control-Request-Headers' => ['*'],
        ],
      ],
      'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
          'create' => ['POST', 'PUT', 'GET'],
          'update' => ['POST', 'PUT', 'PATCH', 'GET'],
          'view' => ['GET'],
          'index' => ['GET'],
        ],
      ]
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

  public function actionAtendimento()
  {
    // $payloadBot = [
    //   "cliente" => "Lucia Maria",
    //   "cliente_telefone" => "11999999999",
    //   "atendido_por" => "BOT",
    //   "atendimento_iniciado" => "2020-06-06 10:00:00",
    //   "status" => "Aberto",
    //   "prioridade" => "Alta",
    //   'grupo' => "Cardiologia",
    //   'etiqueta' => "Cardiologia",
    //   "servico" => "Consulta de rotina",
    //   "descricao" => "Preciso de uma consulta de rotina",
    //   "acao" => "Consulta",
    // ];
    $params = Yii::$app->request->getBodyParams();

    $transaction = Yii::$app->db->beginTransaction();
    try {

      $arrEtapas = [];
      $outro = isset($params['nome_outro']);
      $para = $params['para_quem'] === 'mim' ? ',' : " para {$outro},";
      $title = "{$params['titular_plano']} solicita atendimento{$para} de {$params['o_que_deseja']}, em {$params['onde_deseja_ser_atendido']} pelo profissional: {$params['medico_atendimento']}";
      array_push($arrEtapas, ['hora' => date('d-m-Y H:m:i'), 'descricao' => 'atendimento iniciado pelo auto-atendimento']);

      $model = new Atendimento();
      $model->attributes = $params;
      $model->status = "ABERTO";
      $model->atendimento_iniciado = date('d-m-Y H:m:i');
      $model->atendido_por = 'AUTO-ATENDIMENTO';
      $model->titulo = $title;
      $model->etapas = $arrEtapas;

      $model->save();

      $transaction->commit();

      $response['status'] = StatusCode::STATUS_CREATED;
      $response['message'] = 'Data is created!';
      $response['data'] = [];

    } catch (\Throwable $th) {
      $transaction->rollBack();
      $response['status'] = StatusCode::STATUS_ERROR;
      $response['message'] = "Error: {$th->getMessage()}";
      $response['data'] = [];
    }

    return $response;

  }

  /**
   * Grupo é os serviços que o usuário pode escolher para atendimento (ex: Cardio, dentista, etc)
   * @return array
   */
  public function actionGrupo()
  {
    $token = TokenAuthenticationHelper::token();
    $params = Yii::$app->request->getBodyParams();
    $transaction = Yii::$app->db->beginTransaction();

    try {
      $model = new Grupo();
      $model->attributes = $params;
      $model->slug = SlugfyHelper::slugfy($params['servico']);

      $model->save();

      $transaction->commit();

      $response['status'] = StatusCode::STATUS_CREATED;
      $response['message'] = 'Data is created!';
      $response['data'] = [];

    } catch (\Throwable $th) {
      $transaction->rollBack();
      $response['status'] = StatusCode::STATUS_BAD_REQUEST;
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }
    return $response;
  }

  /**
   * Tipo de servoço (ex: Consulta, Exame, orçamento, etc)
   * @return array
   */
  public function actionEtiqueta()
  {
    $token = TokenAuthenticationHelper::token();
    $params = Yii::$app->request->getBodyParams();
    $transaction = Yii::$app->db->beginTransaction();

    try {
      $model = new Etiqueta();
      $model->attributes = $params;
      $model->slug = SlugfyHelper::slugfy($params['servico']);

      $model->save();

      $transaction->commit();

      $response['status'] = StatusCode::STATUS_CREATED;
      $response['message'] = 'Data is created!';
      $response['data'] = [];

    } catch (\Throwable $th) {
      $transaction->rollBack();
      $response['status'] = StatusCode::STATUS_BAD_REQUEST;
      $response['message'] = $th->getMessage();
      $response['data'] = [];
      //throw $th;
    }
    return $response;
  }

  public function actionPrioridade()
  {
    $token = TokenAuthenticationHelper::token();
    $params = Yii::$app->request->getBodyParams();
    $transaction = Yii::$app->db->beginTransaction();

    try {
      $model = new Prioridade();
      $model->attributes = $params;
      $model->slug = SlugfyHelper::slugfy($params['nome']);

      $model->save();

      $transaction->commit();

      $response['status'] = StatusCode::STATUS_CREATED;
      $response['message'] = 'Data is created!';
      $response['data'] = [];

    } catch (\Throwable $th) {
      $transaction->rollBack();
      $response['status'] = StatusCode::STATUS_BAD_REQUEST;
      $response['message'] = $th->getMessage();
      $response['data'] = [];
      //throw $th;
    }
    return $response;
  }

  public function actionStatus()
  {
    $response['status'] = StatusCode::STATUS_CREATED;
    $response['message'] = 'Data is created!';

    $token = TokenAuthenticationHelper::token();
    $params = Yii::$app->request->getBodyParams();
    $transaction = Yii::$app->db->beginTransaction();
    try {

      $model = new Status();
      $model->attributes = $params;
      $model->slug = SlugfyHelper::slugfy($params['nome']);

      $model->save();

      $transaction->commit();

      $response['data'] = [];

    } catch (\Throwable $th) {
      $transaction->rollBack();
      $response['status'] = StatusCode::STATUS_BAD_REQUEST;
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;

  }
  public function actionAcoes()
  {
    $response['status'] = StatusCode::STATUS_CREATED;
    $response['message'] = 'Data is created!';

    $token = TokenAuthenticationHelper::token();
    $params = Yii::$app->request->getBodyParams();
    $transaction = Yii::$app->db->beginTransaction();
    try {

      $model = new Acoes();
      $model->attributes = $params;
      $model->slug = SlugfyHelper::slugfy($params['nome']);

      $model->save();

      $transaction->commit();

      $response['data'] = [];

    } catch (\Throwable $th) {
      $transaction->rollBack();
      $response['status'] = StatusCode::STATUS_BAD_REQUEST;
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }
  public function actionOrigem()
  {
    $response['status'] = StatusCode::STATUS_CREATED;
    $response['message'] = 'Data is created!';

    $token = TokenAuthenticationHelper::token();
    $params = Yii::$app->request->getBodyParams();
    $transaction = Yii::$app->db->beginTransaction();
    try {

      $model = new Origem();
      $model->attributes = $params;
      $model->slug = SlugfyHelper::slugfy($params['nome']);

      $model->save();

      $transaction->commit();

      $response['data'] = [];

    } catch (\Throwable $th) {
      $transaction->rollBack();
      $response['status'] = StatusCode::STATUS_BAD_REQUEST;
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }

  public function actionUnidade()
  {

    $token = TokenAuthenticationHelper::token();
    $params = Yii::$app->request->getBodyParams();
    $transaction = Yii::$app->db->beginTransaction();
    try {

      $model = new Unidades();
      $model->attributes = $params;
      $model->slug = SlugfyHelper::slugfy($params['nome']);

      $model->save();

      $transaction->commit();

      $response['status'] = StatusCode::STATUS_CREATED;
      $response['message'] = 'Data is created!';
      $response['data'] = [];

    } catch (\Throwable $th) {
      $transaction->rollBack();
      $response['status'] = StatusCode::STATUS_BAD_REQUEST;
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }


  public function actionMedico()
  {
    $token = TokenAuthenticationHelper::token();
    $params = Yii::$app->request->getBodyParams();
    $transaction = Yii::$app->db->beginTransaction();
    try {
      $model = new Medicos();
      $model->attributes = $params;
      $model->horarios = serialize($params['horarios']);
      $model->procedimento_valor = serialize($params['procedimento_valor']);
      $model->save();

      $transaction->commit();

      $response['status'] = StatusCode::STATUS_CREATED;
      $response['message'] = 'Data is created!';
      $response['data'] = [];

    } catch (\Throwable $th) {
      $transaction->rollBack();
      $response['status'] = StatusCode::STATUS_BAD_REQUEST;
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }

  public function actionAtendente()
  {
    $token = TokenAuthenticationHelper::token();
    $params = Yii::$app->request->getBodyParams();
    $transaction = Yii::$app->db->beginTransaction();

    try {
      $model = new User();
      $model->attributes = $params;
      $model->access_given = 10;
      $model->status = 10;
      $model->username = $params['email'];
      $model->setPassword($params['password']);
      $model->generateAuthKey();
      $model->save();

      $profile = new Profile();
      $profile->attributes = $params;
      $profile->user_id = $model->id;
      $profile->save();

      $transaction->commit();

      $response['status'] = StatusCode::STATUS_CREATED;
      $response['message'] = "{$profile['name']} agora é um membro da equipe!";
      $response['data'] = [];

    } catch (\Throwable $th) {
      $transaction->rollBack();
      $response['status'] = StatusCode::STATUS_BAD_REQUEST;
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }


}