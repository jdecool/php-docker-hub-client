<?php

declare(strict_types=1);

namespace JDecool\DockerHub;

use DateTimeImmutable;
use InvalidArgumentException;

final class Date
{
    public static function fromString(string $date): DateTimeImmutable
    {
        preg_match('/([0-9]{4}-[0-9]{2}-[0-9]{2})T([0-9]{2}:[0-9]{2}:[0-9]{2})(\.\d+)?Z/', $date, $matches);

        $object = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', "{$matches[1]} {$matches[2]}", new \DateTimeZone('UTC'));
        if (false === $object) {
            throw new InvalidArgumentException("Invalid date format: $date");
        }

        return $object;
    }
}
