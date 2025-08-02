<?php

namespace Models;

class User {
    private string $id;
    private string $name;
    private string $email;
    private string $password;

    public function __construct(string $id, string $name, string $email, string $password) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function toArray(): array {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password
        ];
    }

    public static function validate(array $data): bool {
        return isset($data['name'], $data['email'], $data['password']) &&
            filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    }

    public static function fromArray(array $data): ?User {
        if (!self::validate($data)) {
            return null;
        }

        return new User(
            $data['id'],
            $data['name'],
            $data['email'],
            $data['password']
        );
    }
}
