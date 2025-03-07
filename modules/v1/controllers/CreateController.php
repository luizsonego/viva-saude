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
      'status' => \app\models\StatusCode::STATUS_OK,
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
      $outro = isset($params['nome_outro']) ? (string) $params['nome_outro'] : '';
      $para = ($params['para_quem'] === 'titular') ? ',' : " para {$outro},";

      $modelMedicos = new Medicos();
      $medicoNome = '';

      // Verifica se o ID do médico foi enviado e busca o nome
      if (!empty($params['medico_atendimento'])) {
        $medico = $modelMedicos->find()
          ->select('nome')
          ->where(['id' => $params['medico_atendimento']])
          ->one();
        $medicoNome = $medico ? $medico->nome : '';
      }

      // **Correção do erro: Garante que "onde_deseja_ser_atendido" seja string**
      $ondeAtendido = isset($params['onde_deseja_ser_atendido']) ? (string) $params['onde_deseja_ser_atendido'] : 'o';

      // Construção do título do atendimento
      if (!empty($params['o_que_deseja']) && !empty($ondeAtendido)) {
        $title = "{$params['titular_plano']} solicita atendimento{$para} de {$params['o_que_deseja']}, em {$ondeAtendido} pelo profissional: {$medicoNome}";
      } else {
        $title = "{$params['titular_plano']} solicita atendimento{$para}";
      }

      // Adiciona a etapa inicial do atendimento
      $arrEtapas[] = [
        'hora' => date('d-m-Y H:i:s'),
        'descricao' => 'Atendimento iniciado pelo auto-atendimento'
      ];

      // Determina o status do atendimento
      $emEspera = !empty($params['em_espera']) ? 'FILA DE ESPERA' :
        (!empty($params['aguardando_vaga']) ? 'AGUARDANDO VAGA' : 'ABERTO');

      // Criação do modelo de atendimento
      $model = new Atendimento();
      $model->attributes = $params;
      $model->status = $emEspera;
      $model->atendimento_iniciado = date('Y-m-d H:i:s');
      $model->atendido_por = $params['atendido_por'] ?? 'AUTO-ATENDIMENTO';
      $model->titulo = $title;
      $model->onde_deseja_ser_atendido = $ondeAtendido;
      $model->medico_atendimento = $medicoNome;
      $model->medico = $params['medico_atendimento'] ?? null;
      $model->medico_atendimento_data = !empty($params['medico_atendimento_data'])
        ? date('Y-m-d H:i:s', strtotime($params['medico_atendimento_data']))
        : null;
      $model->etapas = json_encode($arrEtapas, JSON_UNESCAPED_UNICODE);



      // Salva o modelo no banco
      if (!$model->save()) {
        throw new \Exception('Erro ao salvar atendimento: ' . json_encode($model->getErrors(), JSON_UNESCAPED_UNICODE));
      }

      $transaction->commit();

      return [
        'status' => StatusCode::STATUS_CREATED,
        'message' => 'Atendimento criado com sucesso!',
        'data' => $model->attributes,
      ];

    } catch (\Throwable $th) {
      $transaction->rollBack();
      return [
        'status' => StatusCode::STATUS_ERROR,
        'message' => "Erro ao processar atendimento: " . $th->getMessage(),
        'data' => [],
      ];
    }
  }



  // public function actionAtendimento()
  // {
  //   $params = Yii::$app->request->getBodyParams();

  //   $transaction = Yii::$app->db->beginTransaction();
  //   try {

  //     $arrEtapas = [];
  //     $outro = isset($params['nome_outro']);
  //     $para = $params['para_quem'] === 'titular' ? ',' : " para {$outro},";

  //     $modelMedicos = new Medicos();

  //     if (isset($params['medico_atendimento'])) {
  //       $medico = $modelMedicos->find()->select('nome')->where(['id' => $params['medico_atendimento']])->one();
  //     }

  //     $title = '';
  //     if (isset($params['o_que_deseja']) || isset($params['onde_deseja_ser_atendido']) || isset($medico['nome'])) {
  //       $title = "{$params['titular_plano']} solicita atendimento{$para} de {$params['o_que_deseja']}, em {$params['onde_deseja_ser_atendido']} pelo profissional: {$medico['nome']}";
  //     } else {
  //       $title = "{$params['titular_plano']} solicita atendimento{$para}";
  //     }

  //     array_push($arrEtapas, ['hora' => date('d-m-Y H:m:i'), 'descricao' => 'atendimento iniciado pelo auto-atendimento']);

  //     $emEspera = !empty($params['em_espera']) ? 'FILA DE ESPERA' :
  //       (!empty($params['aguardando_vaga']) ? 'AGUARDANDO VAGA' : 'ABERTO');

  //     $model = new Atendimento();
  //     $model->attributes = $params;
  //     $model->status = $emEspera;
  //     $model->atendido_por = isset($params['atendido_por']) ? $params['atendido_por'] : 'AUTO-ATENDIMENTO';
  //     $model->titulo = $title;
  //     $model->medico_atendimento = !isset($medico['nome']) ? '' : $medico['nome'];
  //     $model->medico = !isset($params['medico_atendimento']) ? null : $params['medico_atendimento'];
  //     $model->medico_atendimento_data = isset($params['medico_atendimento_data'])
  //       ? date('Y-m-d H:i:s', strtotime($params['medico_atendimento_data']))
  //       : '';
  //     $model->etapas = json_encode($arrEtapas);

  //     $model->save();

  //     $transaction->commit();

  //     $response['status'] = StatusCode::STATUS_CREATED;
  //     $response['message'] = 'Data is created!';
  //     $response['data'] = [];

  //   } catch (\Throwable $th) {
  //     $transaction->rollBack();
  //     $response['status'] = StatusCode::STATUS_ERROR;
  //     $response['message'] = "Error: {$th}";
  //     $response['data'] = [];
  //   }

  //   return $response;

  // }

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
      $model->procedimento_valor = json_encode($params['procedimento_valor'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
      $model->etiquetas = html_entity_decode(json_encode($params['etiquetas'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
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