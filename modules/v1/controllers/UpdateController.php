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
use app\models\Unidades;
use app\modules\v1\resource\Atendente;
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

      $arrEtiquetas = json_decode($model['etiquetas']);

      $model->setAttributes($params);
      $model->local = serialize($params['localizacao']);
      array_push($arrEtiquetas, $params['etiquetas']);
      $model->etiquetas = json_encode($arrEtiquetas);
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
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Atendimento::findOne($params['id']);

      $arrEtapas = json_decode($model['etapas']);

      $model->attributes = $params;
      array_push($arrEtapas, ['hora' => date('d-m-Y H:m:i'), 'descricao' => 'atendimento alterado pelo atendente {[nome do atendente]}']);
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

  public function actionTrocaStatus()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Atendimento::findOne($params['id']);

      // $arrEtapas = json_decode($model['etapas']);

      $model->attributes = $params;
      // array_push($arrEtapas, ['hora' => date('d-m-Y H:m:i'), 'descricao' => 'atendimento alterado STATUS para {$params[status]} pelo atendente {[nome do atendente]}']);
      // $model->etapas = json_encode($arrEtapas);

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
  public function actionTrocaPrioridade()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Atendimento::findOne($params['id']);

      // $arrEtapas = json_decode($model['etapas']);

      $model->attributes = $params;
      // array_push($arrEtapas, ['hora' => date('d-m-Y H:m:i'), 'descricao' => 'atendimento alterado PRIORIDADE para {$params[status]} pelo atendente {[nome do atendente]}']);
      // $model->etapas = json_encode($arrEtapas);

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
      $model = Atendimento::findOne($params['id']);

      // $arrEtapas = json_decode($model['etapas'], true);

      $model->attributes = $params;
      // array_push($arrEtapas, ['hora' => date('d-m-Y H:m:i'), 'descricao' => 'atendimento alterado de atendente para [atendente] por atendente {[nome do atendente]}']);
      // $model->etapas = json_encode($arrEtapas);

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
      $model = Atendente::findOne($params['id']);
      $profile = Profile::findOne(['user_id' => $params['id']]);

      // $arrEtapas = json_decode($model['etapas']);
      $model->attributes = $params;
      $profile->attributes = $params['profile'];

      if (isset($params['senha'])) {
        $model->password_hash = \Yii::$app->getSecurity()->generatePasswordHash($params['senha']);
      }
      // array_push($arrEtapas, ['hora' => date('d-m-Y H:m:i'), 'descricao' => 'atendimento alterado de atendente para [atendente] por atendente {[nome do atendente]}']);
      // $model->etapas = json_encode($arrEtapas);

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