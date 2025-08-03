<?php

namespace Models;

class Pivot {
    private string $id;
    private string $description;
    private float $flowRate;
    private float $minApplicationDepth;
    private string $userId;

    public function __construct(string $id, string $description, float $flowRate, float $minApplicationDepth, string $userId) {
        $this->id = $id;
        $this->description = $description;
        $this->flowRate = $flowRate;
        $this->minApplicationDepth = $minApplicationDepth;
        $this->userId = $userId;
    }
    /*
    public function toArray(): array {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'flowRate' => $this->flowRate,
            'minApplicationDepth' => $this->minApplicationDepth,
            'userId' => $this->userId
        ];
    }
    */
    public static function validate(array $data): bool {
        return isset($data['description'], $data['flowRate'], $data['minApplicationDepth']) &&
            is_numeric($data['flowRate']) &&
            is_numeric($data['minApplicationDepth']) &&
            $data['flowRate'] > 0 &&
            $data['minApplicationDepth'] > 0;
    }
    /*
    public static function fromArray(array $data): ?Pivot {
        if (!self::validate($data)) return null;

        return new Pivot(
            $data['id'],
            $data['description'],
            (float)$data['flowRate'],
            (float)$data['minApplicationDepth'],
            $data['userId']
        );
    }
    */
}