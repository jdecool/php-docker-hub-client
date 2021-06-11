<?php

declare(strict_types=1);

namespace JDecool\DockerHub\Resource;

use DateTimeImmutable;
use JDecool\DockerHub\Date;
use function json_decode;

class RepositoryImageSummary
{
    public static function fromJson(string $json): self
    {
        return self::fromArray(
            json_decode($json, true, 512, JSON_THROW_ON_ERROR),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            Date::fromString($data['active_from']),
            Statistics::fromArray($data['statistics']),
        );
    }

    public function __construct(
        private DateTimeImmutable $activeFrom,
        private Statistics $statistics,
    ) {
    }

    public function getActiveFrom(): DateTimeImmutable
    {
        return $this->activeFrom;
    }

    public function getStatistics(): Statistics
    {
        return $this->statistics;
    }
}
