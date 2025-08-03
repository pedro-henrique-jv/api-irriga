<?php

namespace Controllers;

use Models\User;
use Helpers\JsonStorage;
use Firebase\JWT\JWT;
use Dotenv\Dotenv;
use Ramsey\Uuid\Uuid;

class UserController {
    private JsonStorage $storage;
    private string $jwtSecret;

    public function __construct() {
        $this->storage = new JsonStorage('storage/users.json');

        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
        $dotenv->load();

        $this->jwtSecret = $_ENV['JWT_SECRET'];
    }

    public function create(array $data): array {
        if (!User::validate($data)) {
            return ['error' => 'Dados inválidos'];
        }

        $users = $this->storage->read();

        foreach ($users as $user) {
            if ($user['email'] === $data['email']) {
                return ['error' => 'Email já cadastrado'];
            }
        }

        $data['id'] = Uuid::uuid4()->toString();
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $users[] = $data;
        $this->storage->write($users);

        unset($data['password']);
        return ['success' => true, 'user' => $data];
    }

    public function login(array $credentials): array {
        $users = $this->storage->read();
        $email = $credentials['email'] ?? '';
        $password = $credentials['password'] ?? '';

        foreach ($users as $user) {
            if ($user['email'] === $email && password_verify($password, $user['password'])) {
                $payload = [
                    'iat' => time(),
                    'exp' => time() + 3600,
                    'user_id' => $user['id'],
                    'email' => $user['email']
                ];

                $jwt = JWT::encode($payload, $this->jwtSecret, 'HS256');

                unset($user['password']);
                return [
                    'success' => true,
                    'token' => $jwt,
                    'user' => $user
                ];
            }
        }

        return ['error' => 'Credenciais inválidas'];
    }
}