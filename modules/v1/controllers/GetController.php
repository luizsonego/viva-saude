<?php
namespace app\modules\v1\controllers;

use app\helpers\TokenAuthenticationHelper;
use app\models\Acoes;
use app\models\Atendimento;
use app\models\Especialidade;
use app\models\Etiqueta;
use app\models\Medicos;
use app\models\Origem;
use app\models\Prioridade;
use app\models\Profile;
use app\models\Status;
use app\models\StatusCode;
use app\models\Unidades;
use app\models\User;
use app\modules\v1\resource\Atendente;
use app\modules\v1\resource\Atendimento as ResourceAtendimento;
use app\modules\v1\resource\Grupo;
use Yii;
use yii\db\Expression;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\rest\Controller;

class GetController extends Controller
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


  public function actionIndex()
  {
    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "You may customize this page by editing the following file:" . __FILE__,
      'data' => ''
    ];
  }
  public function actionAtendimentos()
  {
    $user = TokenAuthenticationHelper::token();
    $modelProfile = new Profile();
    $profile = $modelProfile->find()->where(['user_id' => $user->id])->one();

    $model = new ResourceAtendimento();

    // Get pagination parameters from request
    $page = Yii::$app->request->get('page', 1);
    $pageSize = Yii::$app->request->get('pageSize', 20);
    $status = Yii::$app->request->get('status', '');
    $prioridade = Yii::$app->request->get('prioridade', '');
    $medico = Yii::$app->request->get('medico', '');
    $local = Yii::$app->request->get('local', '');
    $atendente = Yii::$app->request->get('atendente', '');
    $cliente = Yii::$app->request->get('cliente', '');
    $dataInicio = Yii::$app->request->get('dataInicio', '');
    $dataFim = Yii::$app->request->get('dataFim', '');
    $procedimento = Yii::$app->request->get('procedimento', '');
    $convenio = Yii::$app->request->get('convenio', '');
    $especialidade = Yii::$app->request->get('especialidade', '');

    // Log para depuração
    // Yii::info("Parâmetros recebidos: status={$status}, prioridade={$prioridade}, medico={$medico}, local={$local}, atendente={$atendente}, cliente={$cliente}");

    // Define roles that have full access to all cards
    $adminRoles = ['gerente', 'supervisor', 'administrador'];

    // Define statuses that are visible to all users
    $publicStatuses = ['ABERTO', 'NOVO', 'AGUARDANDO VAGA', 'FILA DE ESPERA'];

    // Base query with selected fields only
    $query = $model->find();

    // Apply role-based filtering
    if (in_array($profile->cargo, $adminRoles)) {
      // Admin roles can see all cards
    } else if ($profile->cargo === 'atendente') {
      // Atendentes can only see cards assigned to them or with public statuses
      $query->andWhere([
        'or',
        ['atendente' => $user->id],
        ['in', 'status', $publicStatuses]
      ]);
    } else {
      // Other roles can see cards with specific statuses
      $query->andWhere([
        'in',
        'status',
        [
          'ABERTO',
          'NOVO',
          'AGUARDANDO VAGA',
          'FILA DE ESPERA',
          'EM ANALISE',
          'PAGAMENTO',
          'AGUARDANDO AUTORIZACAO',
          'CONCLUIDO',
          'PAGAMENTO EFETUADO',
          'AGUARDANDO EFETUADO'
        ]
      ]);
    }

    // Apply filters from request
    if (!empty($status) && $status !== 'TODOS' && $status !== 'ABERTOS') {
      $query->andWhere(['status' => $status]);
      Yii::info("Filtro de status aplicado: {$status}");
    }

    if (!empty($prioridade)) {
      // Verificar se é um ID ou um nome
      if (is_numeric($prioridade)) {
        $query->andWhere(['prioridade' => $prioridade]);
      } else {
        $query->andWhere(['like', 'prioridade', $prioridade]);
      }
      Yii::info("Filtro de prioridade aplicado: {$prioridade}");
    }

    if (!empty($medico)) {
      // Verificar se é um ID ou um nome
      if (is_numeric($medico)) {
        $query->andWhere([
          'or',
          ['medico_atendimento' => $medico],
          ['id_medico' => $medico]
        ]);
      } else {
        $query->andWhere([
          'or',
          ['like', 'medico_atendimento', $medico],
          ['like', 'nome_medico', $medico]
        ]);
      }
      Yii::info("Filtro de médico aplicado: {$medico}");
    }

    if (!empty($local)) {
      $query->andWhere(['like', 'onde_deseja_ser_atendido', $local]);
      Yii::info("Filtro de local aplicado: {$local}");
    }

    if (!empty($atendente)) {
      // Verificar se é um ID ou um nome
      if (is_numeric($atendente)) {
        $query->andWhere(['atendente' => $atendente]);
      } else {
        $query->andWhere(['like', 'atendente', $atendente]);
      }
      Yii::info("Filtro de atendente aplicado: {$atendente}");
    }

    if (!empty($cliente)) {
      try {
        // Sanitiza o input do cliente
        $cliente = trim($cliente);

        // Busca por nome, CPF ou qualquer parte do texto
        $query->andWhere([
          'or',
          ['like', 'titular_plano', $cliente],
          ['like', 'cpf_titular', $cliente],
          ['like', 'nome_outro', $cliente],
          ['like', 'cpf_outro', $cliente],
          ['like', 'o_que_deseja', $cliente]
        ]);

        // Log para depuração
        Yii::info("Busca por cliente: {$cliente}");
      } catch (\Exception $e) {
        Yii::error("Erro na busca por cliente: " . $e->getMessage());
        // Não interrompe a execução, apenas loga o erro
      }
    }

    // Filtro por data de atendimento
    if (!empty($dataInicio)) {
      $query->andWhere(['>=', 'medico_atendimento_data', $dataInicio]);
      Yii::info("Filtro de data início aplicado: {$dataInicio}");
    }

    if (!empty($dataFim)) {
      $query->andWhere(['<=', 'medico_atendimento_data', $dataFim]);
      Yii::info("Filtro de data fim aplicado: {$dataFim}");
    }

    // Filtro por procedimento
    if (!empty($procedimento)) {
      $query->andWhere(['like', 'o_que_deseja', $procedimento]);
      Yii::info("Filtro de procedimento aplicado: {$procedimento}");
    }

    // Filtro por convênio
    if (!empty($convenio)) {
      $query->andWhere(['like', 'convenio', $convenio]);
      Yii::info("Filtro de convênio aplicado: {$convenio}");
    }

    // Filtro por especialidade
    if (!empty($especialidade)) {
      $query->andWhere(['like', 'especialidade', $especialidade]);
      Yii::info("Filtro de especialidade aplicado: {$especialidade}");
    }

    // Get total count for pagination
    $totalCount = $query->count();

    // Log para depuração
    Yii::info("Total de resultados: {$totalCount}");

    // Apply pagination
    $offset = ($page - 1) * $pageSize;
    $query->offset($offset)->limit($pageSize);

    // Execute query
    $data = $query->all();

    // Process data for response
    foreach ($data as &$item) {
      // Ensure etapas is properly decoded from JSON if needed
      if (!empty($item->etapas)) {
        if (is_string($item->etapas)) {
          $item->etapas = json_decode($item->etapas, true) ?: $item->etapas;
        }
      } else {
        $item->etapas = [];
      }

      // Add temporizador calculation
      $item->temporizador = $this->processarTemporizador($item);
    }

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => [
        'items' => $data,
        'pagination' => [
          'total' => $totalCount,
          'page' => (int)$page,
          'pageSize' => (int)$pageSize,
          'totalPages' => ceil($totalCount / $pageSize)
        ]
      ]
    ];
  }

  

  public function actionEtiquetasMedico()
  {

  }

  function flattenArray($array)
  {
    $result = [];
    array_walk_recursive($array, function ($item) use (&$result) {
      $result[] = $item;
    });
    return array_unique($result); // Remover duplicatas
  }

  public function actionGrupos()
  {
    $model = new Grupo();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionEtiquetas()
  {
    $model = new Etiqueta();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionMedicos()
  {
    $model = new Medicos();
    $data = $model->find()->all();

    foreach ($data as &$medico) {
      if (!empty($medico->local)) {
        self::is_serialized($medico->local) ? $medico->local = unserialize($medico->local) : ["local" => $medico->local];
      }
    }

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }



  public function actionPrioridade()
  {
    $model = new Prioridade();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }

  public function actionOrigem()
  {
    $model = new Origem();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }

  public function actionUnidades()
  {
    $model = new Unidades();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }
  public function actionEspecialidade()
  {
    $model = new Especialidade();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }

  public function actionAcoes()
  {
    $model = new Acoes();
    $data = $model->find()->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }


  public function actionAtendente()
  {
    $model = new Atendente();
    $data = $model->find()
      ->where(['access_given' => 10])
      ->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }


  public function actionProcedimentos()
  {
    $model = new Medicos();
    $data = $model->find()
      ->orderBy(['nome' => SORT_ASC])
      ->all();

    $procedimentos = [];

    // Itera sobre os resultados e extrai apenas o campo "procedimento"
    foreach ($data as $item) {
      if (isset($item->procedimento_valor) && !empty($item->procedimento_valor)) {
        $decoded = json_decode($item->procedimento_valor, true);
        if (is_array($decoded)) {
          foreach ($decoded as $proc) {
            if (isset($proc['procedimento'])) {
              $procedimentos[] = $proc['procedimento'];
            }
          }
        }
      }
    }

    // Remove duplicatas
    $procedimentos = array_values(array_unique($procedimentos));
    sort($procedimentos);

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $procedimentos
    ];
  }

  public function actionProcedimentoCor(string $prcedimento)
  {
    $model = new Acoes();
    $data = $model->find()
      ->where(['nome' => $prcedimento])
      ->one();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }

  public function actionUserAuthenticaded()
  {
    $user = TokenAuthenticationHelper::token();

    $profile = Profile::findOne(['user_id' => $user->id]);
    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $profile['cargo']
    ];
  }

  public function actionMedicosLocal()
  {
    $model = new Medicos();
    // $data = $model->find()->where(['like', 'procedimento_valor', $search])->all();
    $data = $model->find()->all();

    foreach ($data as &$medico) {
      if (!empty($medico->local)) {
        self::is_serialized($medico->local) ? $medico->local = unserialize($medico->local) : [$medico->local];
      }
    }


    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
  }

  public function actionOndeSerAtendido()
  {

    $model = new Atendimento();
    $data = $model->find()
      ->select(['onde_deseja_ser_atendido'])
      ->distinct()
      ->all();

    return [
      'status' => StatusCode::STATUS_OK,
      'message' => "",
      'data' => $data
    ];
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

  public function actionVagasDisponiveisMedico($medico_id, $data = null)
  {
    if (!$data) {
      $data = date('Y-m-d');
    }
  

    $medico = Medicos::findOne($medico_id);

    if (!$medico) {
      return ['erro' => 'Médico não encontrado'];
  }

     $locais = self::is_serialized($medico->local) ? $medico->local = unserialize($medico->local) : [$medico->local];
        $resultado = [];

        foreach ($locais as $local) {
          // Conta quantos agendamentos já foram feitos para este médico/local nesta data
          $consultas_agendadas = Atendimento::find()
              ->where([
                  'medico_atendimento' => $medico_id,
                  'medico_atendimento_local' => $local['local'],
                  'medico_atendimento_status' => 'agendado',
                  'o_que_deseja' => 'consulta'
              ])
              ->andWhere(['>=', new Expression('DATE(medico_atendimento_data)'), $data])
              ->andWhere(['<', new Expression('DATE(medico_atendimento_data)'), date('Y-m-d', strtotime($data . ' +1 day'))])
              ->count();
              
          $retornos_agendados = Atendimento::find()
              ->where([
                  'medico_atendimento' => $medico_id,
                  'medico_atendimento_local' => $local['local'],
                  'medico_atendimento_status' => 'agendado',
                  'o_que_deseja' => 'retorno'
              ])
              ->andWhere(['>=', new Expression('DATE(medico_atendimento_data)'), $data])
              ->andWhere(['<', new Expression('DATE(medico_atendimento_data)'), date('Y-m-d', strtotime($data . ' +1 day'))])
              ->count();
          
          // Calcula vagas disponíveis
          $consultas_disponiveis = intval($local['consulta']) - intval($consultas_agendadas);
          $retornos_disponiveis = intval($local['retorno']) - intval($retornos_agendados);
          
          $resultado[] = [
              'local_id' => $local['id'],
              'local_nome' => $local['local'],
              'vagas_consulta' => [
                  'total' => intval($local['consulta']),
                  'agendadas' => intval($consultas_agendadas),
                  'disponiveis' => $consultas_disponiveis
              ],
              'vagas_retorno' => [
                  'total' => intval($local['retorno']),
                  'agendadas' => intval($retornos_agendados),
                  'disponiveis' => $retornos_disponiveis
              ]
          ];
      }
      
    return [
            'medico' => [
                'id' => $medico->id,
                'nome' => $medico->nome,
                'especialidade' => $medico->especialidade
            ],
            'data' => $data,
            'locais' => $resultado
        ];
  }

  public function actionVagasDisponiveisMedicos($data = null)
  {
    if (!$data) {
      $data = date('Y-m-d');
    }

    // Busca todos os médicos ativos
    $medicos = Medicos::find()->all();
    $resultado_geral = [];

    foreach ($medicos as $medico) {
      // Corrige o tratamento do campo local
      $locais = [];
      if (!empty($medico->local)) {
        if (self::is_serialized($medico->local)) {
          $locais = unserialize($medico->local);
        } else {
          // Se não for serializado, cria um array com um único local
          $locais = [['local' => $medico->local, 'id' => 1, 'consulta' => 0, 'retorno' => 0]];
        }
      }

      $resultado_medico = [];

      foreach ($locais as $local) {
        // Verifica se o local tem a estrutura esperada
        if (!isset($local['local'])) {
          continue;
        }

        // Conta quantos agendamentos já foram feitos para este médico/local nesta data
        $consultas_agendadas = Atendimento::find()
          ->where([
            'medico' => $medico->id,
            'onde_deseja_ser_atendido' => $local['local'],
            // 'medico_atendimento_status' => 'agendado',
          ])
          ->andWhere(['like', 'o_que_deseja', 'consulta'])
          ->andWhere(['>=', new Expression('DATE(medico_atendimento_data)'), $data])
          ->andWhere(['<', new Expression('DATE(medico_atendimento_data)'), date('Y-m-d', strtotime($data . ' +1 day'))])
          ->count();
          
        $retornos_agendados = Atendimento::find()
          ->where([
            'medico' => $medico->id,
            'onde_deseja_ser_atendido' => $local['local'],
            // 'medico_atendimento_status' => 'agendado',
            'o_que_deseja' => 'Retorno'
          ])
          ->andWhere(['>=', new Expression('DATE(medico_atendimento_data)'), $data])
          ->andWhere(['<', new Expression('DATE(medico_atendimento_data)'), date('Y-m-d', strtotime($data . ' +1 day'))])
          ->count();

        // Conta atendimentos não concluídos ou inativos
        $consultas_nao_concluidas = Atendimento::find()
          ->where([
            'medico' => $medico->id,
            'onde_deseja_ser_atendido' => $local['local'],
          ])
          ->andWhere(['like', 'o_que_deseja', 'consulta'])
          ->andWhere(['>=', new Expression('DATE(medico_atendimento_data)'), $data])
          ->andWhere(['<', new Expression('DATE(medico_atendimento_data)'), date('Y-m-d', strtotime($data . ' +1 day'))])
          ->andWhere(['not in', 'status', ['CONCLUIDO', 'INATIVIDADE']])
          ->count();

        $retornos_nao_concluidos = Atendimento::find()
          ->where([
            'medico' => $medico->id,
            'onde_deseja_ser_atendido' => $local['local'],
            'o_que_deseja' => 'Retorno'
          ])
          ->andWhere(['>=', new Expression('DATE(medico_atendimento_data)'), $data])
          ->andWhere(['<', new Expression('DATE(medico_atendimento_data)'), date('Y-m-d', strtotime($data . ' +1 day'))])
          ->andWhere(['not in', 'status', ['CONCLUIDO', 'INATIVIDADE']])
          ->count();
        
        // Conta quantos procedimentos já foram agendados para este médico/local nesta data
        $procedimentos_agendados = Atendimento::find()
          ->where([
            'medico' => $medico->id,
            'onde_deseja_ser_atendido' => $local['local']
          ])
          ->andWhere(['not', ['like', 'o_que_deseja', 'consulta']])
          ->andWhere(['!=', 'o_que_deseja', 'Retorno'])
          ->andWhere(['>=', new Expression('DATE(medico_atendimento_data)'), $data])
          ->andWhere(['<', new Expression('DATE(medico_atendimento_data)'), date('Y-m-d', strtotime($data . ' +1 day'))])
          ->count();

        // Conta procedimentos não concluídos ou inativos
        $procedimentos_nao_concluidos = Atendimento::find()
          ->where([
            'medico' => $medico->id,
            'onde_deseja_ser_atendido' => $local['local']
          ])
          ->andWhere(['not', ['like', 'o_que_deseja', 'consulta']])
          ->andWhere(['!=', 'o_que_deseja', 'Retorno'])
          ->andWhere(['>=', new Expression('DATE(medico_atendimento_data)'), $data])
          ->andWhere(['<', new Expression('DATE(medico_atendimento_data)'), date('Y-m-d', strtotime($data . ' +1 day'))])
          ->andWhere(['not in', 'status', ['CONCLUIDO', 'INATIVIDADE']])
          ->count();
        
        // Adiciona os não concluídos aos agendados
        $consultas_agendadas += $consultas_nao_concluidas;
        $retornos_agendados += $retornos_nao_concluidos;
        $procedimentos_agendados += $procedimentos_nao_concluidos;
        
        // Calcula vagas disponíveis
        $consultas_disponiveis = intval($local['consulta'] ?? 0) - intval($consultas_agendadas);
        $retornos_disponiveis = intval($local['retorno'] ?? 0) - intval($retornos_agendados);
        $procedimentos_disponiveis = intval($local['procedimento'] ?? 0) - intval($procedimentos_agendados);
        
        $resultado_medico[] = [
          'local_id' => $local['id'] ?? 1,
          'local_nome' => $local['local'],
          'vagas_consulta' => [
            'total' => intval($local['consulta'] ?? 0),
            'agendadas' => intval($consultas_agendadas),
            'disponiveis' => $consultas_disponiveis
          ],
          'vagas_retorno' => [
            'total' => intval($local['retorno'] ?? 0),
            'agendadas' => intval($retornos_agendados),
            'disponiveis' => $retornos_disponiveis
          ],
          'vagas_procedimento' => [
            'total' => intval($local['procedimento'] ?? 0),
            'agendadas' => intval($procedimentos_agendados),
            'disponiveis' => $procedimentos_disponiveis
          ]
        ];
      }

      // Adiciona informações do médico ao resultado
      $resultado_geral[] = [
        'medico' => [
          'id' => $medico->id,
          'nome' => $medico->nome,
          'especialidade' => $medico->especialidade
        ],
        'locais' => $resultado_medico
      ];
    }
    
    return [
      'data' => $data,
      'medicos' => $resultado_geral
    ];
  }

  
  private function processarTemporizador(&$data)
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

    // Debug logging
    Yii::info("Processando temporizador para atendimento ID: {$data->id}, Status: {$statusAtual}, Último status alterado: " . ($ultimoStatusAlterado ? $ultimoStatusAlterado : 'null'));
    Yii::info("Etapas: " . json_encode($data->etapas));

    if ($ultimoStatusAlterado && isset($statusTemporizadores[$statusAtual]) && $statusTemporizadores[$statusAtual] !== null) {
      $tempoExpiracao = strtotime($ultimoStatusAlterado) + $statusTemporizadores[$statusAtual];
      $tempoRestante = $tempoExpiracao - time();

      // Debug logging
      Yii::info("Tempo expiração: " . date('Y-m-d H:i:s', $tempoExpiracao) . ", Tempo restante: {$tempoRestante} segundos");

      // Verificar se está em atraso
      if ($tempoRestante <= 0) {
        // $data->em_atraso = true;
        // $data->tempo_atraso = date('Y-m-d H:i:s', $tempoExpiracao);
        // if (!$data->save()) {
        //   error_log('Erro ao salvar o status atualizado.');
        // }
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

    // Debug logging for why we're returning null
    if (!$ultimoStatusAlterado) {
      Yii::info("Retornando null porque último status alterado é null");
    } elseif (!isset($statusTemporizadores[$statusAtual])) {
      Yii::info("Retornando null porque status atual '{$statusAtual}' não está nos temporizadores");
    } elseif ($statusTemporizadores[$statusAtual] === null) {
      Yii::info("Retornando null porque temporizador para status '{$statusAtual}' é null");
    }

    return null;
  }

  private function getUltimaAlteracaoStatus($etapas)
  {
    // Debug logging
    Yii::info("getUltimaAlteracaoStatus recebeu: " . json_encode($etapas));

    if (!is_array($etapas)) {
      Yii::info("getUltimaAlteracaoStatus: etapas não é um array");
      return null;
    }

    $ultimoStatus = null;
    foreach ($etapas as $etapa) {
      if (isset($etapa['descricao']) && strpos(strtolower($etapa['descricao']), 'status foi alterado') !== false) {
        $ultimoStatus = $etapa['hora'] ?? null;
        Yii::info("getUltimaAlteracaoStatus: encontrou status alterado em: " . ($ultimoStatus ? $ultimoStatus : 'null'));
      }
    }

    Yii::info("getUltimaAlteracaoStatus: retornando: " . ($ultimoStatus ? $ultimoStatus : 'null'));
    return $ultimoStatus;
  }
}