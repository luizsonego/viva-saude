<?php
namespace app\modules\v1\controllers;

use app\models\Atendimento;
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

}