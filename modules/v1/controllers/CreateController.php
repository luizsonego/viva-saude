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

    $userToken = TokenAuthenticationHelper::token();
    if (!$userToken || !isset($userToken['id'])) {
      throw new \Exception('Token de autenticação inválido.');
    }

    $profile = Profile::findOne(['user_id' => $userToken['id']]);
    if (!$profile) {
      throw new \Exception('Perfil do usuário não encontrado.');
    }
    $atendente = "{$profile['name']} ({$profile['email']})";

    try {
      $arrEtapas = [];
      $outro = isset($params['nome_outro']) ? (string) $params['nome_outro'] : '';
      $para = (isset($params['para_quem']) && $params['para_quem'] === 'titular') ? ',' : " para {$outro},";


      $modelMedicos = new Medicos();
      $medicoNome = '';
      // Verifica se o ID do médico foi enviado e busca o nome
      if (!empty($params['medico_atendimento'])) {
        $medico = $modelMedicos->find()
          ->select('nome')
          ->where(['id' => (int) $params['medico_atendimento']])
          ->one();
        $medicoNome = $medico->nome ?? '';
      }

      // **Correção do erro: Garante que "onde_deseja_ser_atendido" seja string**
      $ondeAtendido = !empty($params['onde_deseja_ser_atendido']) ? (string) $params['onde_deseja_ser_atendido'] : 'não informado';

      // Construção do título do atendimento
      if (!empty($params['o_que_deseja']) && !empty($ondeAtendido)) {
        $title = "{$params['titular_plano']} solicita atendimento{$para} de {$params['o_que_deseja']}, em {$ondeAtendido} pelo profissional: {$medicoNome}";
      } else {
        $title = "{$params['titular_plano']} solicita atendimento{$para}";
      }

      // Adiciona a etapa inicial do atendimento
      $arrEtapas[] = [
        "hora" => date('d-m-Y H:i:s'),
        "descricao" => "Atendimento iniciado por {$atendente}"
      ];

      // Determina o status do atendimento
      // $emEspera = !empty($params['em_espera']) ? 'FILA DE ESPERA' :
      //   (!empty($params['aguardando_vaga']) ? 'AGUARDANDO VAGA' : 'ABERTO');
      $status = 'ABERTO';
      if (!empty($params['em_espera'])) {
        $status = 'FILA DE ESPERA';
      } elseif (!empty($params['aguardando_vaga'])) {
        $status = 'AGUARDANDO VAGA';
      }

      // Criação do modelo de atendimento
      $model = new Atendimento();
      $model->attributes = $params;
      $model->status = $status;
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
        Yii::error("Erro ao salvar atendimento: " . json_encode($model->getErrors(), JSON_UNESCAPED_UNICODE), 'atendimento');
        throw new \Exception('Erro ao salvar atendimento: ');
      }

      // Inicializa o temporizador se o status for ABERTO
      $temporizador = null;
      if ($status === 'ABERTO') {
        // Adiciona a etapa de status inicial
        $arrEtapas[] = [
          "hora" => date('d-m-Y H:i:s'),
          "descricao" => "Status foi alterado para {$status}"
        ];
        
        // Atualiza o modelo com as etapas atualizadas
        $model->etapas = json_encode($arrEtapas, JSON_UNESCAPED_UNICODE);
        $model->save();
        
        // Processa o temporizador
        $temporizador = $this->processarTemporizador($model);
      }

      $transaction->commit();

      return [
        'status' => StatusCode::STATUS_CREATED,
        'message' => 'Atendimento criado com sucesso!',
        'data' => [
          'atendimento' => $model->attributes,
          'temporizador' => $temporizador
        ],
      ];

    } catch (\Throwable $th) {
      $transaction->rollBack();
      Yii::error("Erro ao processar atendimento: {$th->getMessage()}", 'atendimento');
      return [
        'status' => StatusCode::STATUS_ERROR,
        'message' => "Erro ao processar atendimento, tente novamente. ",
        'data' => [],
      ];
    }
  }

  /**
   * Processa o temporizador para um atendimento
   * @param Atendimento $data O modelo de atendimento
   * @return array|null Informações do temporizador ou null se não aplicável
   */
  private function processarTemporizador($data)
  {
    $statusTemporizadores = [
      'ABERTO' => 30 * 60,
      'EM ANALISE' => 60 * 60,
      'AGUARDANDO PAGAMENTO' => 24 * 60 * 60,
      'AUTORIZAÇÃO' => 3 * 60 * 60,
      'PAGAMENTO EFETUADO' => 60 * 60,
      'FILA DE ESPERA' => null,
      'AGUARDANDO VAGA' => 48 * 60 * 60
    ];

    $statusAtual = strtoupper($data->status);
    $ultimoStatusAlterado = $this->getUltimaAlteracaoStatus($data->etapas);

    if ($ultimoStatusAlterado && isset($statusTemporizadores[$statusAtual]) && $statusTemporizadores[$statusAtual] !== null) {
      $tempoExpiracao = strtotime($ultimoStatusAlterado) + $statusTemporizadores[$statusAtual];
      $tempoRestante = $tempoExpiracao - time();
      
      // Verificar se está em atraso
      if ($tempoRestante <= 0) {
        return [
          'em_atraso' => true,
          'tempo_atraso' => date('Y-m-d H:i:s', $tempoExpiracao)
        ];
      }
      
      return [
        'tempo_restante' => max(0, $tempoRestante),
        'expira_em' => date('Y-m-d H:i:s', $tempoExpiracao),
        'em_atraso' => false
      ];
    }
    
    return null;
  }

  /**
   * Obtém a data/hora da última alteração de status
   * @param array|string $etapas As etapas do atendimento
   * @return string|null A data/hora da última alteração de status ou null
   */
  private function getUltimaAlteracaoStatus($etapas)
  {
    if (is_string($etapas)) {
      $etapas = json_decode($etapas, true);
    }
    
    if (!is_array($etapas)) {
      return null;
    }

    $ultimoStatus = null;
    foreach ($etapas as $etapa) {
      if (isset($etapa['descricao']) && strpos(strtolower($etapa['descricao']), 'status foi alterado') !== false) {
        $ultimoStatus = $etapa['hora'] ?? null;
      }
    }
    return $ultimoStatus;
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

  public function actionAgendarConsulta()
    {
      $dados = Yii::$app->request->getBodyParams();
        // Validações básicas
        if (!isset(
          $dados['medico'], 
          $dados['onde_deseja_ser_atendido'], 
          $dados['titular_plano'], 
          $dados['medico_atendimento_data'], 
          $dados['o_que_deseja']
        )) {
            return ['erro' => 'Dados incompletos para agendamento'];
        }
        
        // Verifica disponibilidade de vagas
        $data = date('Y-m-d', strtotime($dados['medico_atendimento_data']));

        $actionVagas = new GetController('vagasDisponiveisMedico', $this->module);
        $vagas = $actionVagas->actionVagasDisponiveisMedico($dados['medico'], $data);
        // $vagas = $this->getVagasDisponiveis($dados['medico'], $data);
        // return $vagas['locais'];
        // Encontra o local específico
        $local_encontrado = null;
        foreach ($vagas['locais'] as $local) {
            if ($local['local_nome'] == $dados['onde_deseja_ser_atendido']) {
                $local_encontrado = $local;
                break;
            }
        }
        
        if (!$local_encontrado) {
            return ['erro' => 'Local não encontrado para este médico'];
        }
        
        // Verifica se há vagas disponíveis
        $tipo = $dados['o_que_deseja'];
        if ($tipo == 'consulta' && $local_encontrado['vagas_consulta']['disponiveis'] <= 0) {
            return ['erro' => 'Não há vagas disponíveis para consulta neste local e data'];
        }
        
        if ($tipo == 'retorno' && $local_encontrado['vagas_retorno']['disponiveis'] <= 0) {
            return ['erro' => 'Não há vagas disponíveis para retorno neste local e data'];
        }
        
        // Identifica o local_nome pelo local_id
        $local_nome = $local_encontrado['local_nome'];
        
        // Cria um novo modelo de Atendimento
        $atendimento = new Atendimento();
        
        // Atribui os valores
        $atendimento->titular_plano = $dados['titular_plano'];
        $atendimento->cpf_titular = $dados['cpf'] ?? null;
        $atendimento->whatsapp_titular = $dados['whatsapp'] ?? null;
        $atendimento->para_quem = $dados['para_quem'] ?? 'titular';
        $atendimento->nome_outro = $dados['nome_outro'] ?? null;
        $atendimento->cpf_outro = $dados['cpf_outro'] ?? null;
        $atendimento->o_que_deseja = $dados['o_que_deseja']; // 'consulta' ou 'retorno'
        $atendimento->medico_atendimento = (string) $dados['medico'];
        $atendimento->onde_deseja_ser_atendido = $local_nome;
        $atendimento->medico_atendimento_local = $local_nome;
        $atendimento->medico_atendimento_data = $dados['medico_atendimento_data'];
        $atendimento->medico_atendimento_status = 'agendado';
        $atendimento->status = 'agendado';
        $atendimento->observacoes = $dados['observacoes'] ?? null;
        $atendimento->cliente = $dados['titular_plano'];
        $atendimento->cliente_telefone = $dados['whatsapp'] ?? null;
        $atendimento->aguardando_vaga = false;
        $atendimento->created_at = date('Y-m-d H:i:s');
        $atendimento->updated_at = date('Y-m-d H:i:s');
        
        // Salva o modelo
        if (!$atendimento->save()) {
            return [
                'erro' => 'Erro ao registrar agendamento', 
                'details' => $atendimento->getErrors()
            ];
        }
        
        // Retorna os dados do agendamento
        return [
            'sucesso' => true,
            'mensagem' => 'Agendamento realizado com sucesso',
            'agendamento_id' => $atendimento->id,
            'vagas_restantes' => $tipo == 'consulta' 
                ? $local_encontrado['vagas_consulta']['disponiveis'] - 1 
                : $local_encontrado['vagas_retorno']['disponiveis'] - 1
        ];
    }



}