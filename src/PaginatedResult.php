<?php

declare(strict_types=1);

namespace JDecool\DockerHub;

use function json_decode;

/**
 * @template T
 */
class PaginatedResult
{
    /**
     * @return static<T>
     */
    public static function fromJson(string $json, callable $instanciator): self
    {
        $body = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return new PaginatedResult(
            $body['count'],
            $body['next'],
            $body['previous'],
            $instanciator($body['results']),
        );
    }

    /**
     * @param array<T> $results
     */
    public function __construct(
        private int $count,
        private ?string $next,
        private ?string $previous,
        private array $results,
    ) {
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getNext(): ?string
    {
        return $this->next;
    }

    public function getPrevious(): ?string
    {
        return $this->previous;
    }

    /**
     * @return array<T>
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
