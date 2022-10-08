<?php

declare(strict_types=1);

namespace JDecool\DockerHub\Resource;

use InvalidArgumentException;
use function json_decode;

class UserToken
{
    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data)) {
            throw new InvalidArgumentException();
        }

        return self::fromArray($data);
    }

    public static function fromArray(array $data): self
    {
        return new self($data['token']);
    }

    /**
     * @param string $token JWT
     */
    public function __construct(
        private string $token
    ) {
        if ('' === trim($token)) {
            throw new InvalidArgumentException();
        }
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
