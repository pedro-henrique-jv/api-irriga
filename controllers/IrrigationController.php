<?php

namespace Controllers;

use Helpers\JsonStorage;
use Ramsey\Uuid\Uuid;

class IrrigationController
{
    private JsonStorage $storage;
    private JsonStorage $pivotStorage;

    public function __construct(JsonStorage $irrigationStorage, JsonStorage $pivotStorage)
    {
        $this->storage = $irrigationStorage;
        $this->pivotStorage = $pivotStorage;
    }

    public function create(array $data): array
    {
        if (!isset($data['pivotId'], $data['applicationAmount'], $data['irrigationDate'], $data['userId'])) {
            return ['error' => 'Campos obrigatórios ausentes'];
        }

        $pivots = $this->pivotStorage->read();
        $pivotPertence = false;

        foreach ($pivots as $pivot) {
            if ($pivot['id'] === $data['pivotId'] && $pivot['user_id'] === $data['userId']) {
                $pivotPertence = true;
                break;
            }
        }

        if (!$pivotPertence) {
            return ['error' => 'Pivô não pertence ao usuário'];
        }

        $data['id'] = Uuid::uuid4()->toString();
        $data['created_at'] = date('Y-m-d H:i:s');

        $irrigations = $this->storage->read();
        $irrigations[] = $data;
        $this->storage->write($irrigations);

        return ['success' => true, 'irrigation' => $data];
    }

    public function read(string $userId): array
    {
        $irrigations = $this->storage->read();

        $filtered = array_filter($irrigations, fn($i) => $i['userId'] === $userId);

        return array_values($filtered);
    }

    public function show(string $id, string $userId): array
    {
        $irrigations = $this->storage->read();

        foreach ($irrigations as $i) {
            if ($i['id'] === $id && $i['userId'] === $userId) {
                return ['success' => true, 'irrigation' => $i];
            }
        }

        return ['error' => 'Registro não encontrado'];
    }

    public function delete(string $id, string $userId): array
    {
        $irrigations = $this->storage->read();
        $filtered = array_filter($irrigations, fn($i) => !($i['id'] === $id && $i['userId'] === $userId));

        if (count($filtered) === count($irrigations)) {
            return ['error' => 'Registro não encontrado'];
        }

        $this->storage->write(array_values($filtered));
        return ['success' => true];
    }
}
