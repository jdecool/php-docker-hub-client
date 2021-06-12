<?php

declare(strict_types=1);

namespace JDecool\DockerHub\Resource;

class Tag
{
    public static function fromList(array $data): array
    {
        return array_map(static fn(array $item): self => self::fromArray($item), $data);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['tag'],
            $data['is_current'],
        );
    }

    public function __construct(
        private string $tag,
        private bool $isCurrent,
    ) {
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function isCurrent(): bool
    {
        return $this->isCurrent;
    }
}
