<?php

namespace Helpers;

class JsonStorage {
    private string $filepath;

    public function __construct(string $filepath) {
        $this->filepath = $filepath;

        if (!file_exists($this->filepath)) {
            file_put_contents($this->filepath, json_encode([]));
        }
    }

    public function read(): array {
        $json = file_get_contents($this->filepath);
        return json_decode($json, true) ?? [];
    }

    public function write(array $data): void {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($this->filepath, $json);
    }
}
