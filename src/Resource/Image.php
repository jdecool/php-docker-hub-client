<?php declare(strict_types=1);

namespace JDecool\DockerHub\Resource;

use DateTimeImmutable;
use JDecool\DockerHub\Date;

class Image
{
    /**
     * @return self[]
     */
    public static function fromList(array $data): array
    {
        return array_map(static fn(array $item): self => self::fromArray($item), $data);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['architecture'],
            $data['features'],
            $data['variant'],
            $data['digest'],
            $data['os'],
            $data['os_features'],
            $data['os_version'],
            $data['size'],
            $data['status'],
            !empty($data['last_pulled']) ? Date::fromString($data['last_pulled']) : null,
            !empty($data['last_pushed']) ? Date::fromString($data['last_pushed']) : null,
        );
    }

    public function __construct(
        private string $architecture,
        private string $features,
        private ?string $variant,
        private string $digest,
        private string $os,
        private string $osFeature,
        private ?string $osVersion,
        private int $size,
        private string $status,
        private ?DateTimeImmutable $tagLastPulled,
        private ?DateTimeImmutable $tagLastPushed,
    ) {
    }

    public function getArchitecture(): string
    {
        return $this->architecture;
    }

    public function getFeatures(): string
    {
        return $this->features;
    }

    public function getVariant(): ?string
    {
        return $this->variant;
    }

    public function getDigest(): string
    {
        return $this->digest;
    }

    public function getOs(): string
    {
        return $this->os;
    }

    public function getOsFeature(): string
    {
        return $this->osFeature;
    }

    public function getOsVersion(): ?string
    {
        return $this->osVersion;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTagLastPulled(): ?DateTimeImmutable
    {
        return $this->tagLastPulled;
    }

    public function getTagLastPushed(): ?DateTimeImmutable
    {
        return $this->tagLastPushed;
    }
}
