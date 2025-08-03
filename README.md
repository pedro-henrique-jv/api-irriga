# API de Gerenciamento de Irrigação

Este projeto é uma API RESTful desenvolvida em PHP 8.2.12, com autenticação via JWT, gerenciamento de pivôs e de registros de irrigação. Foi utilizado o XAMPP para ambiente local e o Postman para testes.

## Tecnologias Utilizadas

- PHP 8.2.12
- XAMPP (Apache + PHP)
- Composer
- php-jwt (firebase/php-jwt)
- Postman (para testes)

## Como Executar o Projeto

1. Clonar o repositório (se possível clone direto para a pasta do servidor Apache (htdocs), ou mova para la):
   ```
   git clone https://github.com/pedro-henrique-jv/api-irriga.git
   ```
   
2. Instalar dependências com o Composer:
   ```
   composer install
   ```
   
3. Iniciar o Apache via painel do XAMPP

8. Acessar a API em:
   [http://localhost/api-irriga](http://localhost/api-irriga)

Observação: A persistência é feita em memória, então não é necessário configurar banco de dados.

## Criando o arquivo .env

Para sua API funcionar, crie um arquivo chamado .env na raiz do projeto com o conteúdo abaixo.
```
JWT_SECRET=uma_chave_secreta_forte_aqui
DB_HOST=localhost
DB_USER=root
DB_PASS=
```
Use este comando PHP para gerar a chave e copie manualmente para .env:
```
php -r "echo 'JWT_SECRET=' . base64_encode(random_bytes(32)) . PHP_EOL;"
```

## Autenticação JWT

Todas as rotas (exceto /auth/register e /auth/login) exigem um token JWT no cabeçalho Authorization:

## Guia de Testes (via Postman)

### 1. Registro de Usuário

Method: POST 
```
http://localhost/api-irriga/auth/register
````

Body (JSON):
```
{
    "name": "usuario1",
    "email": "usuario1@teste.com",
    "password": "senha123"
}
```
### 2. Login de Usuário

Method: POST 
```
http://localhost/api-irriga/auth/login
```

Body (JSON):
```
{
    "email": "usuario1@teste.com",
    "password": "senha123"
}
```
Resposta esperada:
```
{
    "success": true,
    "token": "JWT_TOKEN_AQUI",
    "user": {
        "name": "usuario1",
        "email": "usuario1@teste.com",
        "id": "UUID_DO_USUARIO_GERADO"
    }
}
```

## Endpoints de Pivôs de Irrigação

Todas as rotas abaixo exigem token JWT.

### Criar Pivô

Method: POST 
```
http://localhost/api-irriga/pivots
```
Body:
```
{
  "description": "Pivô Fazenda A",
  "flowRate": 150.5,
  "minApplicationDepth": 5.0
}
```
### Listar Pivôs

Method: GET 
```
http://localhost/api-irriga/pivots
```
### Obter Pivô por ID

Method: GET 
```
http://localhost/api-irriga/pivots/{id}
```
### Atualizar Pivô

Method: PUT
```
http://localhost/api-irriga/pivots/{id}
```
Body:
```
{
  "description": "Pivô Atualizado",
  "flowRate": 160.0,
  "minApplicationDepth": 6.0
}
```
### Deletar Pivô

Method: DELETE
```
http://localhost/api-irriga/pivots/{id}
```
## Endpoints de Registros de Irrigação

Todas as rotas abaixo exigem token JWT.

### Criar Registro

Method: POST
```
http://localhost/api-irriga/irrigations
```
Body:
```
{
  "pivotId": "UUID_DO_PIVO",
  "applicationAmount": 20.0,
  "irrigationDate": "2025-07-01T10:00:00Z"
}
```
### Listar Registros

Method: GET
```
http://localhost/api-irriga/irrigations
```
### Obter Registro por ID

Method: GET
```
http://localhost/api-irriga/irrigations/{id}
```
### Deletar Registro

Method: DELETE 
```
http://localhost/api-irriga/irrigations/{id}
```
