<?php
namespace app\modules\v1\controllers;

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
use app\models\Unidades;
use app\modules\v1\resource\Atendente;
use Yii;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\rest\Controller;

class UpdateController extends Controller
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

  public function actionAgendamento()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Atendimento::findOne($params['id']);

      $model->setAttributes($params);
      $model->prioridade = '';
      $model->status = 'Agendado';
      $model->save();

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Agendamento atualizado com sucesso!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;

  }
  public function actionGrupo()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Grupo::findOne($params['id']);

      $modelEtiqueta = Etiqueta::find()->where(['grupo' => $params['id']])->all();

      foreach ($modelEtiqueta as $etiqueta) {
        $etiqueta->cor = isset($params['cor']) ? $params['cor'] : $params['cor'];
        $etiqueta->save();
      }

      $model->setAttributes($params);
      $model->save();

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Agendamento atualizado com sucesso!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;

  }
  public function actionAcao()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Acoes::findOne($params['id']);

      $model->setAttributes($params);
      $model->save();

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Agendamento atualizado com sucesso!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }
  public function actionEspecialidade()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Especialidade::findOne($params['id']);

      $model->setAttributes($params);
      $model->save();

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Agendamento atualizado com sucesso!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }
  public function actionEtiqueta()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Etiqueta::findOne($params['id']);

      $model->setAttributes($params);
      $model->save();

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Agendamento atualizado com sucesso!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }
  public function actionMedico()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Medicos::findOne($params['id']);

      // $arrEtiquetas = json_decode($model['etiquetas']);

      $model->setAttributes($params);
      // $model->horarios = serialize($params['horarios']);
      $model->procedimento_valor =
        json_encode(
          $params['procedimento_valor'],
          JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
      $model->etiquetas =
        html_entity_decode(
          json_encode(
            $params['etiquetas'],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
          )
        );
      // $model->procedimento_valor = serialize($params['procedimento_valor']);
      // $model->local = serialize($params['local']);

      $model->local = serialize($params['local']);
      // array_push($arrEtiquetas, $params['etiquetas']);
      // $model->etiquetas = json_encode($arrEtiquetas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
      $model->save();

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Agendamento atualizado com sucesso!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }
  public function actionOrigem()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Origem::findOne($params['id']);

      $model->setAttributes($params);
      $model->save();

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Agendamento atualizado com sucesso!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }
  public function actionPrioridade()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Prioridade::findOne($params['id']);

      $model->attributes = $params;
      $model->save();

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Agendamento atualizado com sucesso!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }

  public function actionProfile()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Profile::findOne($params['id']);

      $model->setAttributes($params);
      $model->save();

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Agendamento atualizado com sucesso!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }
  public function actionUnidade()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Unidades::findOne($params['id']);

      $model->setAttributes($params);
      $model->save();

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Agendamento atualizado com sucesso!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }

  public function actionAtendimento()
  {
    $params = Yii::$app->request->post();
    $transactionDb = Yii::$app->db->beginTransaction();
    try {
      $userToken = TokenAuthenticationHelper::token();
      if (!$userToken || !isset($userToken['id'])) {
        throw new \Exception('Token de autenticação inválido.');
      }

      $profile = Profile::findOne(['user_id' => $userToken['id']]);
      if (!$profile) {
        throw new \Exception('Perfil do usuário não encontrado.');
      }
      $atendente = "{$profile['name']} ({$profile['email']})";

      if (empty($params['id']) || !is_numeric($params['id'])) {
        throw new \Exception('ID do atendimento inválido.');
      }

      $model = Atendimento::findOne($params['id']);
      
      if (!$model) {
        throw new \Exception('Atendimento não encontrado.');
      }

      $arrEtapas = json_decode($model->etapas, true);
      if (!is_array($arrEtapas)) {
        $arrEtapas = []; // Garante que sempre seja um array
      }

      $model->attributes = $params;
      array_push($arrEtapas, [
        "hora" => date('d-m-Y H:i:s'),
        "descricao" => "Atendimento alterado pelo atendente {$atendente}"
      ]);
      $model->etapas = json_encode($arrEtapas);
      $model->medico_atendimento_data = "$params[data_local_atendimento] $params[hora_atendimento]";
      $model->medico_atendimento_local = "$params[onde_deseja_ser_atendido]";

      if (!$model->save()) {
        Yii::error("Erro ao atualizar atendimento: " . json_encode($model->getErrors(), JSON_UNESCAPED_UNICODE), 'atendimento');
        throw new \Exception('Erro ao atualizar atendimento.');
      }

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Agendamento atualizado com sucesso!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      Yii::error("Erro ao atualizar atendimento: {$th->getMessage()}", 'atendimento');
      return [
        'status' => 'error',
        'message' => "Erro ao atualizar atendimento, tente novamente. {$th}",
        'data' => [],
      ];
    }

    return $response;
  }

  public function actionTrocaStatus()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();
    
    try {
      $userToken = TokenAuthenticationHelper::token();
      if (!$userToken || !isset($userToken['id'])) {
        throw new \Exception('Token de autenticação inválido.');
      }

      $profile = Profile::findOne(['user_id' => $userToken['id']]);
      if (!$profile) {
        throw new \Exception('Perfil do usuário não encontrado.');
      }
      $atendente = "{$profile['name']} ({$profile['email']})";

      if (empty($params['id']) || !is_numeric($params['id'])) {
        throw new \Exception('ID do atendimento inválido.');
      }
      $model = Atendimento::findOne($params['id']);

      $arrEtapas = is_string($model->etapas) ? json_decode($model->etapas, true) : $model->etapas;

      // Garante que $arrEtapas sempre seja um array
      if (!is_array($arrEtapas)) {
        $arrEtapas = [];
      }


      $model->attributes = $params;
      array_push($arrEtapas, [
        "hora" => date('d-m-Y H:i:s'),
        "descricao" => "Status foi alterado por {$atendente}"
      ]);
      $model->etapas = json_encode($arrEtapas);

      $model->save();
      $errorVaga = [];
      // Se o status for PAGAMENTO EFETUADO, registrar o uso da vaga
      if (isset($params['status']) && strtoupper($params['status']) === 'PAGAMENTO EFETUADO') {
        // Buscar local_id pelo nome do local do atendimento
        $localNome = $model->medico_atendimento_local;
        $localModel = \app\models\Local::find()->where(['nome' => $localNome])->one();
        return $localModel;
        // if ($localModel) {
          $vaga = new \app\models\Vaga();
          $vaga->medico_id = $model->medico;
          $vaga->horario = $model->hora_atendimento;
          $vaga->local_id = $localModel->id;
          $vaga->data = date('Y-m-d', strtotime($model->medico_atendimento_data));
          $vaga->tipo = $model->o_que_deseja;
          $vaga->local = $model->onde_deseja_ser_atendido;
          $vaga->quantidade = 1;
          $vaga->atendimento = $model->o_que_deseja;
          $vaga->created_at = date('Y-m-d H:i:s');
          $vaga->updated_at = date('Y-m-d H:i:s');
          if (!$vaga->save()) {
            $errorVaga[] = $vaga->getErrors();
            \Yii::error("Erro ao registrar vaga usada: " . json_encode($vaga->getErrors(), JSON_UNESCAPED_UNICODE), 'vaga');
          }
        // } else {
        //   $errorVaga[] = "Local não encontrado para registrar vaga usada: $localNome";
        //   \Yii::error("Local não encontrado para registrar vaga usada: $localNome", 'vaga');
        // }
      }

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Agendamento atualizado com sucesso!';
      $response['errorVaga'] = $errorVaga;
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }
  public function actionTrocaPrioridade()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $userToken = TokenAuthenticationHelper::token();
      if (!$userToken || !isset($userToken['id'])) {
        throw new \Exception('Token de autenticação inválido.');
      }

      $profile = Profile::findOne(['user_id' => $userToken['id']]);
      if (!$profile) {
        throw new \Exception('Perfil do usuário não encontrado.');
      }
      $atendente = "{$profile['name']} ({$profile['email']})";

      if (empty($params['id']) || !is_numeric($params['id'])) {
        throw new \Exception('ID do atendimento inválido.');
      }
      $model = Atendimento::findOne($params['id']);

      $arrEtapas = is_string($model->etapas) ? json_decode($model->etapas, true) : $model->etapas;

      // Garante que $arrEtapas sempre seja um array
      if (!is_array($arrEtapas)) {
        $arrEtapas = [];
      }

      $model->attributes = $params;
      array_push($arrEtapas, [
        "hora" => date('d-m-Y H:i:s'),
        "descricao" => "Prioridade foi alterado por  {$atendente}"
      ]);
      $model->etapas = json_encode($arrEtapas);

      $model->save();

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Agendamento atualizado com sucesso!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }
  public function actionTrocaAtendente()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $userToken = TokenAuthenticationHelper::token();
      if (!$userToken || !isset($userToken['id'])) {
        throw new \Exception('Token de autenticação inválido.');
      }

      $profile = Profile::findOne(['user_id' => $userToken['id']]);
      if (!$profile) {
        throw new \Exception('Perfil do usuário não encontrado.');
      }
      $atendente = "{$profile['name']} ({$profile['email']})";

      if (empty($params['id']) || !is_numeric($params['id'])) {
        throw new \Exception('ID do atendimento inválido.');
      }
      $model = Atendimento::findOne($params['id']);

      $arrEtapas = is_string($model->etapas) ? json_decode($model->etapas, true) : $model->etapas;

      // Garante que $arrEtapas sempre seja um array
      if (!is_array($arrEtapas)) {
        $arrEtapas = [];
      }


      $model->attributes = $params;
      array_push($arrEtapas, [
        "hora" => date('d-m-Y H:i:s'),
        "descricao" => "O atendente foi trocado por {$atendente}"
      ]);
      $model->etapas = json_encode($arrEtapas);

      $model->save();

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Agendamento atualizado com sucesso!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }
  public function actionAtendente()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $userToken = TokenAuthenticationHelper::token();
      if (!$userToken || !isset($userToken['id'])) {
        throw new \Exception('Token de autenticação inválido.');
      }

      if (empty($params['id']) || !is_numeric($params['id'])) {
        throw new \Exception('ID do atendimento inválido.');
      }
      $model = Atendente::findOne($params['id']);
      $profile = Profile::findOne(['user_id' => $params['id']]);
      if (!$profile) {
        throw new \Exception('Perfil do usuário não encontrado.');
      }
      $model->attributes = $params;
      $profile->attributes = $params['profile'];

      if (isset($params['senha'])) {
        $model->password_hash = Yii::$app->getSecurity()->generatePasswordHash($params['senha']);
      }
      
      $model->save();
      $profile->save();

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'atendente atualizado com sucesso!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;
  }

  public function actionAnexo()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $userToken = TokenAuthenticationHelper::token();
      if (!$userToken || !isset($userToken['id'])) {
        throw new \Exception('Token de autenticação inválido.');
      }

      $profile = Profile::findOne(['user_id' => $userToken['id']]);
      if (!$profile) {
        throw new \Exception('Perfil do usuário não encontrado.');
      }
      $atendente = "{$profile['name']} ({$profile['email']})";

      if (empty($params['id']) || !is_numeric($params['id'])) {
        throw new \Exception('ID do atendimento inválido.');
      }
      $model = Atendimento::findOne($params['id']);
      
      if ($model['anexos'] == null) {
        $model->anexos = '[]';
      }
      
      $arrAnexos = json_decode($model['anexos']);
      array_push($arrAnexos, [
        'nome' => $params['nome'],
        'url' => $params['url'],
        'fileType' => $params['fileType'],
        'fileId' => $params['fileId'],
      ]);
      $model->anexos = json_encode($arrAnexos);

      $arrEtapas = json_decode($model->etapas, true);
      if (!is_array($arrEtapas)) {
        $arrEtapas = []; // Garante que sempre seja um array
      }
      array_push($arrEtapas, [
        "hora" => date('d-m-Y H:i:s'),
        "descricao" => "Anexo adicionado por {$atendente}"
      ]);
      $model->etapas = json_encode($arrEtapas);

      $model->save();

      $transactionDb->commit();

      $response['status'] = 'success';
      $response['message'] = 'Anexo enviado!';
      $response['data'] = $model;

    } catch (\Throwable $th) {
      $transactionDb->rollBack();
      $response['status'] = 'error';
      $response['message'] = $th->getMessage();
      $response['data'] = [];
    }

    return $response;

  }

}