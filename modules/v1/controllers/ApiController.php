<?php

namespace app\modules\v1\controllers;

use app\helpers\TokenAuthenticationHelper;
use app\models\MedicosView;
use app\models\StatusCode;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\rest\Controller;

class ApiController extends Controller
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

  /**
   * GET v1/api/medicos-procedimento?search=Academia
   * @param string $search
   * @return array
   */
  public function actionMedicosProcedimento(string $search = '')
  {

    try {
      $userToken = TokenAuthenticationHelper::token();

      if ((!$userToken || !isset($userToken['id'])) || $userToken['id'] != 1) {
        throw new \Exception('Token de autenticação inválido.');
      }

      $model = new MedicosView();
      $data = $model->find()
        ->where(['like', 'procedimento_valor', $search])
        ->orderBy(['nome' => SORT_ASC])
        ->all();

      $medicos = new \stdClass();

      foreach ($data as $medico) {
        if (!empty($medico->local)) {
          self::is_serialized($medico->local) ? $medico->local = unserialize($medico->local) : [$medico->local];
        }

        $medicoId = $medico->id;
        $medicos->$medicoId = $medico;
      }

      $response['status'] = StatusCode::STATUS_OK;
      $response['message'] = "";
      $response['data'] = $medicos;
    } catch (\Throwable $th) {
      $response['status'] = StatusCode::STATUS_ERROR;
      $response['message'] = $th->getMessage();
      $response['data'] = new \stdClass();
    }

    return $response;
  }

  /**
   * GET v1/api/procedimentos
   * @return array
   */
  public function actionProcedimentos()
  {
    try {
      $userToken = TokenAuthenticationHelper::token();

      if ((!$userToken || !isset($userToken['id'])) || $userToken['id'] != 1) {
        throw new \Exception('Token de autenticação inválido.');
      }

      $model = new MedicosView();
      $data = $model->find()
        ->select(['procedimento_valor'])
        ->orderBy(['nome' => SORT_ASC])
        ->all();

      $procedimentos = new \stdClass();
      $processedProcedimentos = [];

      foreach ($data as $medico) {
        if (!empty($medico->procedimento_valor)) {
          $procedimentoData = json_decode($medico->procedimento_valor, true);

          if (is_array($procedimentoData)) {
            foreach ($procedimentoData as $item) {
              if (isset($item['procedimento']) && !isset($processedProcedimentos[$item['procedimento']])) {
                $procedimentoId = $item['id'];
                $procedimentos->$procedimentoId = (object) [
                  'id' => $item['id'],
                  'procedimento' => $item['procedimento'],
                  'valor' => $item['valor'] ?? ''
                ];
                $processedProcedimentos[$item['procedimento']] = true;
              }
            }
          }
        }
      }

      // Ordenar por nome do procedimento (não é possível ordenar objetos diretamente)
      // A ordenação será feita no lado do cliente

      $response['status'] = StatusCode::STATUS_OK;
      $response['message'] = "";
      $response['data'] = $procedimentos;
    } catch (\Throwable $th) {
      $response['status'] = StatusCode::STATUS_ERROR;
      $response['message'] = $th->getMessage();
      $response['data'] = new \stdClass();
    }

    return $response;
  }

  /**
   * GET v1/api/medicos-local?medico=Ariane
   * @param string $medico
   * @return array
   */
  public function actionMedicosLocal(string $medico)
  {
    try {
      $userToken = TokenAuthenticationHelper::token();

      if ((!$userToken || !isset($userToken['id'])) || $userToken['id'] != 1) {
        throw new \Exception('Token de autenticação inválido.');
      }

      $model = new MedicosView();
      $data = $model->find()
        ->select(['id', 'nome', 'local'])
        ->where(['like', 'nome', $medico])
        ->orderBy(['nome' => SORT_ASC])
        ->all();

      $medicos = new \stdClass();

      foreach ($data as $medico) {
        if (!empty($medico->local)) {
          self::is_serialized($medico->local) ? $medico->local = unserialize($medico->local) : [$medico->local];
        }

        $medicoId = $medico->id;
        $medicos->$medicoId = $medico;
      }

      $response['status'] = StatusCode::STATUS_OK;
      $response['message'] = "";
      $response['data'] = $medicos;
    } catch (\Throwable $th) {
      $response['status'] = StatusCode::STATUS_ERROR;
      $response['message'] = $th->getMessage();
      $response['data'] = new \stdClass();
    }

    return $response;
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
