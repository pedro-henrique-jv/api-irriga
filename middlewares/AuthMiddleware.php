<?php

namespace Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

class AuthMiddleware {
    private string $secret;

    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $this->secret = $_ENV['JWT_SECRET'];
    }

    public function verify(): string|false {
        $headers = getallheaders();
        $auth = $headers['Authorization'] ?? '';

        if (!str_starts_with($auth, 'Bearer ')) return false;

        $token = substr($auth, 7);

        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return $decoded->email ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }
}