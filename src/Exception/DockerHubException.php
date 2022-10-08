<?php

declare(strict_types=1);

namespace JDecool\DockerHub\Exception;

use InvalidArgumentException;
use JDecool\DockerHub\Resource\ErrorInfo;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;
use function json_decode;

class DockerHubException extends RuntimeException
{
    private ResponseInterface $response;
    private ?ErrorInfo $errorInfo = null;

    public static function fromResponse(ResponseInterface $response): static
    {
        $response->getBody()->rewind();

        try {
            $body = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $body = [];
        }

        if (!is_array($body)) {
            throw new InvalidArgumentException();
        }

        $object = new static($response, $body['message'] ?? $body['detail'] ?? $response->getReasonPhrase());
        $object->errorInfo = ErrorInfo::fromArray($body['errinfo'] ?? []);

        return $object;
    }

    final public function __construct(ResponseInterface $response, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->response = $response;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getErrorInfo(): ?ErrorInfo
    {
        return $this->errorInfo;
    }
}
