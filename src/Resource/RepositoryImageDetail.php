<?php

declare(strict_types=1);

namespace JDecool\DockerHub\Resource;

use JDecool\DockerHub\Date;

class RepositoryImageDetail
{
    public static function fromList(array $data): array
    {
        return array_map(static fn(array $item): self => self::fromArray($item), $data);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['namespace'],
            $data['repository'],
            $data['digest'],
            Tag::fromList($data['tags']),
            !empty($data['last_pushed']) ? Date::fromString($data['last_pushed']) : null,
            !empty($data['last_pulled']) ? Date::fromString($data['last_pulled']) : null,
            $data['status'],
        );
    }

    /**
     * @param Tag[] $tags
     */
    public function __construct(
        private string $namespace,
        private string $repository,
        private string $digest,
        private array $tags,
        private ?\DateTimeImmutable $lastPushed,
        private ?\DateTimeImmutable $lastPulled,
        private string $status,
    ) {
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getRepository(): string
    {
        return $this->repository;
    }

    public function getDigest(): string
    {
        return $this->digest;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getLastPushed(): ?\DateTimeImmutable
    {
        return $this->lastPushed;
    }

    public function getLastPulled(): ?\DateTimeImmutable
    {
        return $this->lastPulled;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
