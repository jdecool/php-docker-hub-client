<?php

declare(strict_types=1);

namespace JDecool\DockerHub\Resource;

use DateTimeImmutable;
use JDecool\DockerHub\Date;

class ErrorInfo
{
    public static function fromArray(array $data): ?self
    {
        if (empty($data)) {
            return null;
        }

        return new self(
            $data['api_call_docker_id'],
            $data['api_call_name'],
            Date::fromString($data['api_call_start']),
            $data['api_call_txnid'],
            $data['type'] ?? null,
        );
    }

    public function __construct(
        private string $apiCallDockerId,
        private string $apiCallName,
        private DateTimeImmutable $apiCallStart,
        private string $apiCallTxnid,
        private ?string $type,
        // TODO: private ErrorDetails
    ) {
    }

    public function getApiCallDockerId(): string
    {
        return $this->apiCallDockerId;
    }

    public function getApiCallName(): string
    {
        return $this->apiCallName;
    }

    public function getApiCallStart(): DateTimeImmutable
    {
        return $this->apiCallStart;
    }

    public function getApiCallTxnid(): string
    {
        return $this->apiCallTxnid;
    }

    public function getType(): ?string
    {
        return $this->type;
    }
}
