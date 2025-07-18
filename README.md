# Documentação da API Viva Saúde

Esta documentação descreve os endpoints disponíveis na API do Viva Saúde para consumo externo.

## Autenticação

Todos os endpoints requerem autenticação via token. O token deve ser enviado no header da requisição.

## Endpoints Disponíveis

### 1. Buscar Médicos por Procedimento

Retorna uma lista de médicos que realizam um determinado procedimento.

**Endpoint:** `GET /v1/api/medicos-procedimento`

**Parâmetros:**
- `search` (string, opcional): Termo de busca para filtrar procedimentos

**Exemplo de Requisição:**
```bash
curl -X GET "https://clinica.rtxoperacoes.com.br/web/v1/api/medicos-procedimento?search=Academia" \
-H "Authorization: Bearer seu-token-aqui"
```

**Exemplo de Resposta:**
```json
{
  "status": "OK",
  "message": "",
  "data": [
    {
      "id": 1,
      "nome": "Dr. João Silva",
      "procedimento_valor": "...",
      "local": ["Clínica Central", "Unidade Sul"]
    }
  ]
}
```

### 2. Listar Procedimentos

Retorna uma lista de todos os procedimentos disponíveis no sistema.

**Endpoint:** `GET /v1/api/procedimentos`

**Exemplo de Requisição:**
```bash
curl -X GET "https://clinica.rtxoperacoes.com.br/web/v1/api/procedimentos" \
-H "Authorization: Bearer seu-token-aqui"
```

**Exemplo de Resposta:**
```json
{
  "status": "OK",
  "message": "",
  "data": [
    {
      "id": 1,
      "procedimento": "Consulta Clínica",
      "valor": "150.00"
    },
    {
      "id": 2,
      "procedimento": "Exame de Sangue",
      "valor": "80.00"
    }
  ]
}
```

### 3. Buscar Locais de Atendimento por Médico

Retorna os locais de atendimento de um médico específico.

**Endpoint:** `GET /v1/api/medicos-local`

**Parâmetros:**
- `medico` (string, obrigatório): Nome do médico para busca

**Exemplo de Requisição:**
```bash
curl -X GET "https://clinica.rtxoperacoes.com.br/web/v1/api/medicos-local?medico=Ariane" \
-H "Authorization: Bearer seu-token-aqui"
```

**Exemplo de Resposta:**
```json
{
  "status": "OK",
  "message": "",
  "data": [
    {
      "id": 1,
      "nome": "Dra. Ariane Santos",
      "local": ["Clínica Central", "Unidade Norte"]
    }
  ]
}
```

### 4. Buscar Vagas Disponíveis

Retorna as vagas disponíveis agrupadas por médico, incluindo apenas datas futuras ou do dia atual.

**Endpoint:** `GET /v1/api/vagas`

**Exemplo de Requisição:**
```bash
curl -X GET "https://clinica.rtxoperacoes.com.br/web/v1/api/vagas" \
-H "Authorization: Bearer seu-token-aqui"
```

**Exemplo de Resposta:**
```json
{
  "status": "OK",
  "message": "Vagas recuperadas com sucesso.",
  "data": [
    {
      "medico_id": 1,
      "medico_nome": "Dr. João Silva",
      "medico_especialidade": "Cardiologia",
      "tipos": [
        {
          "tipo": "Consulta",
          "datas": [
            {
              "id": 1,
              "data": "2025-01-20",
              "quantidade": 5,
              "atendimento": null,
              "local_nome": "Clínica Central",
              "created_at": "2025-01-15 10:00:00",
              "updated_at": "2025-01-15 10:00:00"
            }
          ]
        },
        {
          "tipo": "Exame",
          "datas": [
            {
              "id": 2,
              "data": "2025-01-21",
              "quantidade": 3,
              "atendimento": null,
              "local_nome": "Unidade Sul",
              "created_at": "2025-01-15 10:00:00",
              "updated_at": "2025-01-15 10:00:00"
            }
          ]
        }
      ]
    }
  ]
}
```

**Descrição da Resposta:**
- `medico_id`: ID único do médico
- `medico_nome`: Nome completo do médico
- `medico_especialidade`: Especialidade do médico
- `tipos`: Array de tipos de atendimento disponíveis
  - `tipo`: Nome do tipo de atendimento (ex: "Consulta", "Exame")
  - `datas`: Array de datas disponíveis para este tipo
    - `id`: ID único da vaga
    - `data`: Data da vaga (formato YYYY-MM-DD)
    - `quantidade`: Número de vagas disponíveis
    - `atendimento`: ID do atendimento associado (null se disponível)
    - `local_nome`: Nome do local de atendimento
    - `created_at`: Data de criação da vaga
    - `updated_at`: Data da última atualização

### 5. Renovar Token de Acesso

Renova o token de acesso do usuário, atualizando os timestamps de criação e atualização.

**Endpoint:** `POST /v1/api/refresh-token`

**Headers:**
- `Authorization`: Bearer token atual

**Exemplo de Requisição:**
```bash
curl -X POST "https://clinica.rtxoperacoes.com.br/web/v1/api/refresh-token" \
-H "Authorization: Bearer seu-token-atual-aqui"
```

**Exemplo de Resposta:**
```json
{
  "status": "OK",
  "message": "Token atualizado com sucesso.",
  "data": {
    "token": "novo-token-gerado",
    "created_at": 1705123456,
    "updated_at": 1705123456
  }
}
```

**Observações:**
- O endpoint requer que o token atual seja válido
- O token é renovado mantendo o mesmo valor, mas atualizando os timestamps
- Útil para manter a sessão ativa sem necessidade de novo login

### 6. Criar Atendimento

Cria um novo atendimento no sistema.

**Endpoint:** `POST /v1/create/atendimento`

**Payload:**
```json
{
  "aguardando_vaga": 1,
  "atendido_por": "BOT",
  "cpf_titular": "0654897564",
  "em_espera": 1,
  "medico": 133,
  "medico_atendimento": 133,
  "medico_atendimento_data": "17-04-2025 13:07:59",
  "o_que_deseja": "Angiotomografia",
  "observacoes": "",
  "onde_deseja_ser_atendido": "Eunapolis",
  "para_quem": "titular",
  "perfil_cliente": "Áudio",
  "telefone": "forma pagamento",
  "titular_plano": "teste do cartao criado",
  "whatsapp_titular": "44999999999"
}
```

**Descrição dos Campos:**
- `aguardando_vaga` (boolean, opcional): Indica se o paciente está aguardando vaga. Se enviado, o atendimento será marcado como aguardando vaga.
- `atendido_por` (string): Nome do atendente que criou o registro ou "BOT" para atendimentos automatizados
- `cpf_titular` (string): CPF do titular do plano
- `em_espera` (boolean, opcional): Indica se o paciente está em fila de espera. Se enviado, o atendimento será marcado como em espera.
- `medico` (integer, opcional): ID do médico
- `medico_atendimento` (integer, opcional): ID do médico que realizará o atendimento
- `medico_atendimento_data` (string): Data e hora do atendimento (formato: DD-MM-YYYY HH:mm:ss)
- `o_que_deseja` (string, opcional): Descrição do procedimento desejado
- `observacoes` (string): Observações adicionais
- `onde_deseja_ser_atendido` (string): Local desejado para o atendimento. Se `medico`, `medico_atendimento` e `o_que_deseja` forem vazios, este campo deve ser falso.
- `para_quem` (string, enum): Indica se o atendimento é para o titular ou outro beneficiário. Valores possíveis: "titular" ou "outra"
- `nome_outro` (string, obrigatório se `para_quem` for "outra"): Nome do beneficiário que será atendido
- `cpf_outro` (string, obrigatório se `para_quem` for "outra"): CPF do beneficiário que será atendido
- `perfil_cliente` (string, enum): Perfil do cliente. Valores possíveis: "Áudio", "Mensagem", "Ligação", "Site"
- `telefone` (string): Telefone de contato
- `titular_plano` (string): Nome do titular do plano
- `whatsapp_titular` (string): Número do WhatsApp do titular

**Exemplo de Requisição para Atendimento do Titular:**
```bash
curl -X POST "https://clinica.rtxoperacoes.com.br/web/v1/create/atendimento" \
-H "Authorization: Bearer seu-token-aqui" \
-H "Content-Type: application/json" \
-d '{
  "aguardando_vaga": 1,
  "atendido_por": "BOT",
  "cpf_titular": "0654897564",
  "em_espera": 1,
  "medico": 133,
  "medico_atendimento": 133,
  "medico_atendimento_data": "17-04-2025 13:07:59",
  "o_que_deseja": "Angiotomografia",
  "observacoes": "",
  "onde_deseja_ser_atendido": "Eunapolis",
  "para_quem": "titular",
  "perfil_cliente": "Áudio",
  "telefone": "forma pagamento",
  "titular_plano": "teste do cartao criado",
  "whatsapp_titular": "44999999999"
}'
```

**Exemplo de Requisição para Atendimento de Outro Beneficiário:**
```bash
curl -X POST "https://clinica.rtxoperacoes.com.br/web/v1/create/atendimento" \
-H "Authorization: Bearer seu-token-aqui" \
-H "Content-Type: application/json" \
-d '{
  "atendido_por": "BOT",
  "cpf_titular": "0654897564",
  "medico_atendimento_data": "17-04-2025 13:07:59",
  "observacoes": "",
  "onde_deseja_ser_atendido": "Eunapolis",
  "para_quem": "outra",
  "nome_outro": "Maria Silva",
  "cpf_outro": "12345678900",
  "perfil_cliente": "Mensagem",
  "telefone": "forma pagamento",
  "titular_plano": "João Silva",
  "whatsapp_titular": "44999999999"
}'
```

**Exemplo de Resposta:**
```json
{
  "status": "201",
  "message": "Atendimento criado com sucesso!",
  "data": {
    "atendimento": {
      "id": 1,
      "titulo": "teste do cartao criado solicita atendimento, de Angiotomografia, em Eunapolis pelo profissional: Dr. Nome do Médico",
      "status": "AGUARDANDO VAGA",
      "atendimento_iniciado": "2024-03-17 13:07:59",
      "atendido_por": "BOT",
      "onde_deseja_ser_atendido": "Eunapolis",
      "medico_atendimento": "Dr. Nome do Médico",
      "medico": 133,
      "medico_atendimento_data": "2025-04-17 13:07:59",
      "etapas": "[{\"hora\":\"17-03-2024 13:07:59\",\"descricao\":\"Atendimento iniciado por BOT\"}]"
    },
    "temporizador": {
      "tempo_restante": 172800,
      "expira_em": "2024-03-19 13:07:59",
      "em_atraso": false
    }
  }
}
```

**Observações sobre o Endpoint de Criar Atendimento:**
1. O status do atendimento será determinado automaticamente:
   - Se `em_espera` for enviado e verdadeiro: "FILA DE ESPERA"
   - Se `aguardando_vaga` for enviado e verdadeiro: "AGUARDANDO VAGA"
   - Caso contrário: "ABERTO"
2. Um temporizador é criado automaticamente baseado no status:
   - ABERTO: 30 minutos
   - EM ANALISE: 1 hora
   - AGUARDANDO PAGAMENTO: 24 horas
   - AUTORIZAÇÃO: 3 horas
   - PAGAMENTO EFETUADO: 1 hora
   - FILA DE ESPERA: sem temporizador
   - AGUARDANDO VAGA: 48 horas
3. Campos opcionais:
   - `aguardando_vaga`: Se não enviado, o atendimento não será marcado como aguardando vaga
   - `em_espera`: Se não enviado, o atendimento não será marcado como em espera
   - `medico`: Pode ser enviado vazio
   - `medico_atendimento`: Pode ser enviado vazio
   - `o_que_deseja`: Pode ser enviado vazio
4. Campos condicionais:
   - Se `para_quem` for "outra", os campos `nome_outro` e `cpf_outro` são obrigatórios
   - Se `medico`, `medico_atendimento` e `o_que_deseja` forem vazios, `onde_deseja_ser_atendido` deve ser falso
5. Valores permitidos:
   - `para_quem`: "titular" ou "outra"
   - `perfil_cliente`: "Áudio", "Mensagem", "Ligação", "Site"

## Tratamento de Erros

Todos os endpoints retornam respostas no seguinte formato:

```json
{
  "status": "ERROR",
  "message": "Mensagem de erro detalhada",
  "data": []
}
```

Possíveis códigos de status:
- `OK`: Requisição bem-sucedida
- `ERROR`: Ocorreu um erro na requisição
- `201`: Recurso criado com sucesso

## Observações Importantes

1. Todos os endpoints requerem autenticação via token
2. As respostas são sempre retornadas em formato JSON
3. Os dados são ordenados alfabeticamente por nome quando aplicável
4. A API suporta CORS para requisições cross-origin
5. Os endpoints são case-sensitive para os parâmetros de busca


