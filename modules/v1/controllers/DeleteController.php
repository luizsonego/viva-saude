<?php
namespace app\modules\v1\controllers;

use app\models\StatusCode;
use yii\db\Query;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\rest\Controller;

// Created By @hoaaah * Copyright (c) 2020 belajararief.com

class DeleteController extends Controller
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

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return [
            'status' => StatusCode::STATUS_OK,
            'message' => "You may customize this page by editing the following file:" . __FILE__,
            'data' => ''
        ];
    }

    public function actionMedico($id)
    {
        try {
            (new Query)
                ->createCommand()
                ->delete('medicos', ['id' => $id])
                ->execute();

            $response['status'] = StatusCode::STATUS_CREATED;
            $response['message'] = 'Data is deleted!';
            $response['data'] = [];

        } catch (\Throwable $th) {
            $response['status'] = StatusCode::STATUS_ERROR;
            $response['message'] = "Error: {$th->getMessage()}";
            $response['data'] = [];
        }

        return $response;
    }
    public function actionGrupo($id)
    {
        try {

            (new Query)
                ->createCommand()
                ->delete('grupo', ['id' => $id])
                ->execute();

            $response['status'] = StatusCode::STATUS_CREATED;
            $response['message'] = 'Data is deleted!';
            $response['data'] = [];

        } catch (\Throwable $th) {
            $response['status'] = StatusCode::STATUS_ERROR;
            $response['message'] = "Error: {$th->getMessage()}";
            $response['data'] = [];
        }

        return $response;
    }
    public function actionEtiqueta($id)
    {
        try {

            (new Query)
                ->createCommand()
                ->delete('etiqueta', ['id' => $id])
                ->execute();

            $response['status'] = StatusCode::STATUS_CREATED;
            $response['message'] = 'Data is deleted!';
            $response['data'] = [];

        } catch (\Throwable $th) {
            $response['status'] = StatusCode::STATUS_ERROR;
            $response['message'] = "Error: {$th->getMessage()}";
            $response['data'] = [];
        }

        return $response;
    }
    public function actionPrioridade($id)
    {
        try {

            (new Query)
                ->createCommand()
                ->delete('prioridade', ['id' => $id])
                ->execute();

            $response['status'] = StatusCode::STATUS_CREATED;
            $response['message'] = 'Data is deleted!';
            $response['data'] = [];

        } catch (\Throwable $th) {
            $response['status'] = StatusCode::STATUS_ERROR;
            $response['message'] = "Error: {$th->getMessage()}";
            $response['data'] = [];
        }

        return $response;
    }
    public function actionProcedimento($id)
    {
        try {

            (new Query)
                ->createCommand()
                ->delete('acoes', ['id' => $id])
                ->execute();

            $response['status'] = StatusCode::STATUS_CREATED;
            $response['message'] = 'Data is deleted!';
            $response['data'] = [];

        } catch (\Throwable $th) {
            $response['status'] = StatusCode::STATUS_ERROR;
            $response['message'] = "Error: {$th->getMessage()}";
            $response['data'] = [];
        }

        return $response;
    }
    public function actionUnidade($id)
    {
        try {

            (new Query)
                ->createCommand()
                ->delete('unidades', ['id' => $id])
                ->execute();

            $response['status'] = StatusCode::STATUS_CREATED;
            $response['message'] = 'Data is deleted!';
            $response['data'] = [];

        } catch (\Throwable $th) {
            $response['status'] = StatusCode::STATUS_ERROR;
            $response['message'] = "Error: {$th->getMessage()}";
            $response['data'] = [];
        }

        return $response;
    }
    public function actionOrigem($id)
    {
        try {

            (new Query)
                ->createCommand()
                ->delete('origem', ['id' => $id])
                ->execute();

            $response['status'] = StatusCode::STATUS_CREATED;
            $response['message'] = 'Data is deleted!';
            $response['data'] = [];

        } catch (\Throwable $th) {
            $response['status'] = StatusCode::STATUS_ERROR;
            $response['message'] = "Error: {$th->getMessage()}";
            $response['data'] = [];
        }

        return $response;
    }
}
