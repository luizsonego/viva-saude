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
      $response['data'] = '';
    }

    return $response;

  }
  public function actionGrupo()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Grupo::findOne($params['id']);

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
      $response['data'] = '';
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
      $response['data'] = '';
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
      $response['data'] = '';
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
      $response['data'] = '';
    }

    return $response;
  }
  public function actionMedico()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Medicos::findOne($params['id']);

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
      $response['data'] = '';
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
      $response['data'] = '';
    }

    return $response;
  }
  public function actionPrioridade()
  {
    $params = \Yii::$app->request->post();
    $transactionDb = \Yii::$app->db->beginTransaction();

    try {
      $model = Prioridade::findOne($params['id']);

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
      $response['data'] = '';
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
      $response['data'] = '';
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
      $response['data'] = '';
    }

    return $response;
  }

}