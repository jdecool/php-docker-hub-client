<?php

declare(strict_types=1);

namespace JDecool\DockerHub\Resource;

use DateTimeImmutable;
use JDecool\DockerHub\Date;

class User
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
            $data['id'],
            $data['username'],
            $data['full_name'],
            $data['location'],
            $data['company'],
            $data['profile_url'],
            Date::fromString($data['date_joined']),
            $data['gravatar_url'],
            $data['type'],
        );
    }

    public function __construct(
        private string $id,
        private string $username,
        private string $fullname,
        private string $location,
        private string $company,
        private string $profileUrl,
        private DateTimeImmutable $dateJoined,
        private string $gravatarUrl,
        private string $type,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getFullname(): string
    {
        return $this->fullname;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getProfileUrl(): string
    {
        return $this->profileUrl;
    }

    public function getDateJoined(): DateTimeImmutable
    {
        return $this->dateJoined;
    }

    public function getGravatarUrl(): string
    {
        return $this->gravatarUrl;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
