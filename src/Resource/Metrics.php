<?php

declare(strict_types=1);

namespace JDecool\DockerHub\Resource;

class Metrics
{
    public static function fromArray(array $data): self
    {
        return new self(
            $data['manifest_deletes'],
            $data['manifest_errors'],
            $data['tag_deletes'],
            $data['tag_errors'],
        );
    }

    public function __construct(
        private int $manifestDeletes,
        private int $manifestErrors,
        private int $tagDeletes,
        private int $tagErrors,
    ) {
    }

    public function getManifestDeletes(): int
    {
        return $this->manifestDeletes;
    }

    public function getManifestErrors(): int
    {
        return $this->manifestErrors;
    }

    public function getTagDeletes(): int
    {
        return $this->tagDeletes;
    }

    public function getTagErrors(): int
    {
        return $this->tagErrors;
    }
}
