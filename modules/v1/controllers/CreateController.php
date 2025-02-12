<?php
namespace app\modules\v1\controllers;

use app\helpers\SlugfyHelper;
use app\helpers\TokenAuthenticationHelper;
use app\models\Acoes;
use app\models\Atendimento;
use app\models\Especialidade;
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
    $params = Yii::$app->request->getBodyParams();

    $transaction = Yii::$app->db->beginTransaction();
    try {

      $arrEtapas = [];
      $outro = isset($params['nome_outro']);
      $para = $params['para_quem'] === 'titular' ? ',' : " para {$outro},";

      // $modelAcao = new Acoes();
      // $desejo = $modelAcao->find()->select('nome')->where(['id' => $params['o_que_deseja']])->one();

      $modelMedicos = new Medicos();
      $medico = $modelMedicos->find()->select('nome')->where(['id' => $params['medico_atendimento']])->one();

      $title = "{$params['titular_plano']} solicita atendimento{$para} de {$params['o_que_deseja']}, em {$params['onde_deseja_ser_atendido']} pelo profissional: {$medico['nome']}";
      array_push($arrEtapas, ['hora' => date('d-m-Y H:m:i'), 'descricao' => 'atendimento iniciado pelo auto-atendimento']);

      // $emEspera = isset($params['em_espera']) && $params['em_espera'] !== false ? 'FILA DE ESPERA' : isset($params['"aguardando_vaga']) && $params['aguardando_vaga'] !== false ? '"AGUARDANDO VAGA' : 'ABERTO';
      // $aguardandoVaga = isset($params['"aguardando_vaga']) && $params['aguardando_vaga'] !== false ? '"AGUARDANDO VAGA' : 'ABERTO';
      $emEspera = !empty($params['em_espera']) ? 'FILA DE ESPERA' :
        (!empty($params['aguardando_vaga']) ? 'AGUARDANDO VAGA' : 'ABERTO');

      $model = new Atendimento();
      $model->attributes = $params;
      $model->status = $emEspera;
      // $model->atendimento_iniciado = date('d-m-Y H:m:i');
      $model->atendido_por = isset($params['atendido_por']) ? $params['atendido_por'] : 'AUTO-ATENDIMENTO';
      $model->titulo = $title;
      $model->medico_atendimento = $medico['nome'];
      $model->medico = $params['medico_atendimento'];
      $model->medico_atendimento_data = isset($params['medico_atendimento_data']) ? date('Y-m-d', strtotime($params['medico_atendimento_data'])) : '';
      $model->etapas = json_encode($arrEtapas);

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
      $response['message'] = "Error: {$th->getCode()} - {$th->getMessage()}";
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
  public function actionEspecialidade()
  {

    $token = TokenAuthenticationHelper::token();
    $params = Yii::$app->request->getBodyParams();
    $transaction = Yii::$app->db->beginTransaction();
    try {

      $model = new Especialidade();
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
      $model->procedimento_valor = json_encode($params['procedimento_valor']);
      // $model->procedimento_valor = serialize($params['procedimento_valor']);
      $model->local = serialize($params['local']);
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