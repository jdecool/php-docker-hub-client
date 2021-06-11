<?php

declare(strict_types=1);

namespace JDecool\DockerHub\Resource;

use function json_decode;

class DeletionResult
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
            $data['dry_run'],
            Metrics::fromArray($data['metrics']),
        );
    }

    public function __construct(
        private bool $dryRun,
        private Metrics $metrics,
    ) {
    }

    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    public function getMetrics(): Metrics
    {
        return $this->metrics;
    }
}
