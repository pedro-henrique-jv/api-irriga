# API de Gerenciamento de Irrigação

Este projeto é uma API RESTful desenvolvida em PHP 8.2.12 puro, com autenticação via JWT e gerenciamento de pivôs de irrigação e registros de irrigação. Foi utilizado o XAMPP para ambiente local e o Postman para testes.

## Tecnologias Utilizadas

- PHP 8.2.12
- XAMPP (Apache + PHP)
- Composer
- php-jwt (firebase/php-jwt)
- Postman (para testes)

## Como Executar o Projeto

1. Clonar o repositório:
   git clone https://github.com/pedro-henrique-jv/api-irriga.git

2. Mover para a pasta do projeto:
   cd api-irriga

3. Instalar dependências com o Composer:
   composer install

4. Colocar o projeto na pasta do servidor Apache (htdocs):
   Copie os arquivos para: C:\xampp\htdocs\api-irriga

5. Iniciar o Apache via painel do XAMPP

6. Acessar a API em:
   http://localhost/api-irriga

Observação: A persistência é feita em memória (via arrays ou arquivos), então não é necessário configurar banco de dados.

## Autenticação JWT

Todas as rotas (exceto /auth/register e /auth/login) exigem um token JWT no cabeçalho Authorization:

Authorization: Bearer SEU_TOKEN_AQUI

## Roteiro de Testes (via Postman)

### 1. Registro de Usuário

POST http://localhost/api-irriga/auth/register

Body (JSON):
{
  "username": "usuario1",
  "password": "senha123"
}

### 2. Login de Usuário

POST http://localhost/api-irriga/auth/login

Body (JSON):
{
  "username": "usuario1",
  "password": "senha123"
}

Resposta esperada:
{
  "token": "JWT_TOKEN_AQUI"
}

## Endpoints de Pivôs de Irrigação

Todas as rotas abaixo exigem token JWT.

### Criar Pivô

POST http://localhost/api-irriga/pivots

Body:
{
  "description": "Pivô Fazenda A",
  "flowRate": 150.5,
  "minApplicationDepth": 5.0
}

### Listar Pivôs

GET http://localhost/api-irriga/pivots

### Obter Pivô por ID

GET http://localhost/api-irriga/pivots/{id}

### Atualizar Pivô

PUT http://localhost/api-irriga/pivots/{id}

Body:
{
  "description": "Pivô Atualizado",
  "flowRate": 160.0,
  "minApplicationDepth": 6.0
}

### Deletar Pivô

DELETE http://localhost/api-irriga/pivots/{id}

## Endpoints de Registros de Irrigação

Todas as rotas abaixo exigem token JWT.

### Criar Registro

POST http://localhost/api-irriga/irrigations

Body:
{
  "pivotId": "UUID_DO_PIVO",
  "applicationAmount": 20.0,
  "irrigationDate": "2025-07-01T10:00:00Z"
}

### Listar Registros

GET http://localhost/api-irriga/irrigations

### Obter Registro por ID

GET http://localhost/api-irriga/irrigations/{id}

### Deletar Registro

DELETE http://localhost/api-irriga/irrigations/{id}

## Testes de Exceções

### Acesso sem Token

Qualquer rota protegida sem Authorization retorna:
{
  "error": "Unauthorized"
}

### Token Inválido

Authorization: Bearer TOKEN_INVALIDO

Resposta:
{
  "error": "Invalid token"
}

### Campos Faltando

POST http://localhost/api-irriga/pivots

Body incompleto:
{
  "description": "Pivô sem vazão"
}

Resposta:
{
  "error": "Missing required fields: flowRate, minApplicationDepth"
}

### Tentativa de Usar Pivot de Outro Usuário

POST http://localhost/api-irriga/irrigations

Body:
{
  "pivotId": "ID_QUE_NAO_PERTENCE_AO_USUARIO",
  "applicationAmount": 10,
  "irrigationDate": "2025-08-01T12:00:00Z"
}

Resposta:
{
  "error": "Access denied to pivot"
}

## Considerações Finais

Este projeto foi desenvolvido com foco em lógica de autenticação, segurança de rotas e validação. A persistência é simplificada para focar nos requisitos principais. Melhorias futuras podem incluir:

- Banco de dados relacional (MySQL ou SQLite)
- Middleware mais robusto
- Testes automatizados com PHPUnit
- Interface gráfica para consumo da API
