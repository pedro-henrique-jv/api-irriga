<?php

class Irrigation
{
    public string $id;
    public string $pivotId;
    public float $applicationAmount;
    public string $irrigationDate;
    public string $userId;

    public function __construct(
        string $id,
        string $pivotId,
        float $applicationAmount,
        string $irrigationDate,
        string $userId
    ) {
        $this->id = $id;
        $this->pivotId = $pivotId;
        $this->applicationAmount = $applicationAmount;
        $this->irrigationDate = $irrigationDate;
        $this->userId = $userId;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'pivotId' => $this->pivotId,
            'applicationAmount' => $this->applicationAmount,
            'irrigationDate' => $this->irrigationDate,
            'userId' => $this->userId,
        ];
    }
}
