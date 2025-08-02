<?php

namespace Controllers;

use Models\User;
use Helpers\JsonStorage;
use Firebase\JWT\JWT;
use Dotenv\Dotenv;

class UserController {
    private JsonStorage $storage;
    private string $jwtSecret;

    public function __construct() {
        $this->storage = new JsonStorage('storage/users.json');

        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
        $dotenv->load();

        $this->jwtSecret = $_ENV['JWT_SECRET'];
    }

    public function getAll(): array {
        $rawUsers = $this->storage->read();
        return array_map(function ($userData) {
            return (new User(
                $userData['id'],
                $userData['name'],
                $userData['email'],
                $userData['password']
            ))->toArray();
        }, $rawUsers);
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

        $ids = array_column($users, 'id');
        $maxId = !empty($ids) ? max($ids) : 0;
        $data['id'] = $maxId + 1;
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

    public function getById(string $id): array {
        $users = $this->storage->read();

        foreach ($users as $user) {
            if ((string)$user['id'] === $id) {
                unset($user['password']);
                return $user;
            }
        }

        return ['error' => 'Usuário não encontrado'];
    }

    public function update(string $id, array $data): array {
        $users = $this->storage->read();
        $updated = false;

        foreach ($users as &$user) {
            if ((string)$user['id'] === $id) {
                if (isset($data['password'])) {
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                }
                $user = array_merge($user, $data);
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            return ['error' => 'Usuário não encontrado'];
        }

        $this->storage->write($users);
        return ['success' => true, 'user' => $data];
    }

    public function delete(string $id): array {
        $users = $this->storage->read();
        $newUsers = array_filter($users, fn($user) => (string)$user['id'] !== (string)$id);

        if (count($newUsers) === count($users)) {
            return ['error' => 'Usuário não encontrado'];
        }

        $this->storage->write(array_values($newUsers));
        return ['success' => true];
    }
}