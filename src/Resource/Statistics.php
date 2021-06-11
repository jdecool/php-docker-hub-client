<?php

declare(strict_types=1);

namespace JDecool\DockerHub\Resource;

class Statistics
{
    public static function fromArray(array $data): self
    {
        return new self(
            $data['total'],
            $data['active'],
            $data['inactive'],
        );
    }
    
    public function __construct(
        private int $total,
        private int $active,
        private int $inactive,
    ) {
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getActive(): int
    {
        return $this->active;
    }

    public function getInactive(): int
    {
        return $this->inactive;
    }
}
