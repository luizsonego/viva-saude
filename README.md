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

## Observações Importantes

1. Todos os endpoints requerem autenticação via token
2. As respostas são sempre retornadas em formato JSON
3. Os dados são ordenados alfabeticamente por nome quando aplicável
4. A API suporta CORS para requisições cross-origin
5. Os endpoints são case-sensitive para os parâmetros de busca
