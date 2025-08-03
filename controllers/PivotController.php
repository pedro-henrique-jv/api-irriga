<?php

namespace Controllers;

use Models\Pivot;
use Helpers\JsonStorage;
use Ramsey\Uuid\Uuid;

class PivotController {
    private JsonStorage $storage;

    public function __construct(JsonStorage $storage) {
        $this->storage = $storage;
    }

    public function create(array $data): array {
        if (!Pivot::validate($data)) {
            return ['error' => 'Dados inválidos'];
        }

        $pivots = $this->storage->read();

        $data['id'] = Uuid::uuid4()->toString();
        $data['created_at'] = date('Y-m-d H:i:s');

        $pivots[] = $data;
        $this->storage->write($pivots);

        return ['success' => true, 'pivot' => $data];
    }

    public function read(string $userId): array {
        $pivots = $this->storage->read();
        $filtered = array_filter($pivots, function ($pivot) use ($userId) {
            return $pivot['user_id'] === $userId;
        });

        return array_values($filtered);
    }

    public function show(string $id, string $userId): array {
        $pivots = $this->storage->read();

        foreach ($pivots as $pivot) {
            if ($pivot['id'] === $id && $pivot['user_id'] === $userId) {
                return ['success' => true, 'pivot' => $pivot];
            }
        }

        return ['error' => 'Pivô não encontrado'];
    }

    // Permite alterar apenas os campos 'description', 'flowRate', 'minApplicationDepth'
    public function update(string $id, array $data, string $authenticatedUserId): array {
        $pivots = $this->storage->read();
        $found = false;
        $updatedPivot = null;

        foreach ($pivots as &$pivot) {
            if ($pivot['id'] === $id) {
                if ($pivot['user_id'] !== $authenticatedUserId) {
                    return ['error' => 'Você não tem permissão para editar esse pivô'];
                }
                unset($data['id'], $data['user_id'], $data['created_at']);

                if (isset($data['description'])) {
                    $pivot['description'] = $data['description'];
                }

                if (isset($data['flowRate']) && is_numeric($data['flowRate']) && $data['flowRate'] > 0) {
                    $pivot['flowRate'] = (float) $data['flowRate'];
                }

                if (isset($data['minApplicationDepth']) && is_numeric($data['minApplicationDepth']) && $data['minApplicationDepth'] > 0) {
                    $pivot['minApplicationDepth'] = (float) $data['minApplicationDepth'];
                }

                $pivot['updated_at'] = date('Y-m-d H:i:s');
                $updatedPivot = $pivot;
                $found = true;
                break;
            }
        }

        if (!$found) {
            return ['error' => 'Pivô não encontrado'];
        }

        $this->storage->write($pivots);
        return ['success' => true, 'pivot' => $updatedPivot];
    }


    public function delete(string $id, string $authenticatedUserId): array {
        $pivots = $this->storage->read();

        foreach ($pivots as $pivot) {
            if ($pivot['id'] === $id) {
                if ($pivot['user_id'] !== $authenticatedUserId) {
                    return ['error' => 'Você não tem permissão para apagar esse pivô'];
                }

                $filtered = array_filter($pivots, fn($p) => $p['id'] !== $id);
                $this->storage->write(array_values($filtered));
                return ['success' => true];
            }
        }

        return ['error' => 'Pivô não encontrado'];
    }
}
