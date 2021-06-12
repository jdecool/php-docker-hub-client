<?php declare(strict_types=1);

namespace JDecool\DockerHub\Resource;

use DateTimeImmutable;
use JDecool\DockerHub\Date;

class ImageTag
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
            $data['id'],
            $data['creator'],
            $data['image_id'],
            Image::fromList($data['images']),
            Date::fromString($data['last_updated']),
            $data['last_updater'],
            $data['last_updater_username'],
            $data['name'],
            $data['repository'],
            $data['full_size'],
            $data['v2'],
            $data['tag_status'],
            !empty($data['tag_last_pulled']) ? Date::fromString($data['tag_last_pulled']) : null,
            !empty($data['tag_last_pushed']) ? Date::fromString($data['tag_last_pushed']) : null,
        );
    }

    public function __construct(
        private int $id,
        private int $creator,
        private ?int $imageId,
        private array $images,
        private DateTimeImmutable $lastUpdated,
        private int $lastUpdater,
        private string $lastUpdaterUsername,
        private string $name,
        private int $repository,
        private int $fullSize,
        private bool $v2,
        private string $tagStatus,
        private ?DateTimeImmutable $tagLastPulled,
        private ?DateTimeImmutable $tagLastPushed,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreator(): int
    {
        return $this->creator;
    }

    public function getImageId(): ?int
    {
        return $this->imageId;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function getLastUpdated(): DateTimeImmutable
    {
        return $this->lastUpdated;
    }

    public function getLastUpdater(): int
    {
        return $this->lastUpdater;
    }

    public function getLastUpdaterUsername(): string
    {
        return $this->lastUpdaterUsername;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRepository(): int
    {
        return $this->repository;
    }

    public function getFullSize(): int
    {
        return $this->fullSize;
    }

    public function isV2(): bool
    {
        return $this->v2;
    }

    public function getTagStatus(): string
    {
        return $this->tagStatus;
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
