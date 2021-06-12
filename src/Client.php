<?php

declare(strict_types=1);

namespace JDecool\DockerHub;

use DateTimeInterface;
use Http\Client\Common\HttpMethodsClient;
use JDecool\DockerHub\Exception\BadRequest;
use JDecool\DockerHub\Exception\NotFound;
use JDecool\DockerHub\Exception\Unauthorized;
use JDecool\DockerHub\Exception\DockerHubException;
use JDecool\DockerHub\Exception\Forbidden;
use JDecool\DockerHub\Resource\DeletionResult;
use JDecool\DockerHub\Resource\ImageTag;
use JDecool\DockerHub\Resource\RepositoryImageDetail;
use JDecool\DockerHub\Resource\RepositoryImageSummary;
use JDecool\DockerHub\Resource\Tag;
use JDecool\DockerHub\Resource\User;
use JDecool\DockerHub\Resource\UserToken;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Throwable;

/**
 * @see https://docs.docker.com/registry/spec/api/
 * @see https://docs.docker.com/docker-hub/api/latest/
 */
class Client
{
    private ?UserToken $authenticationToken = null;

    public function __construct(
        private HttpMethodsClient $http,
    ) {
    }

    public function authenticate(UserToken $token): void
    {
        $this->authenticationToken = $token;
    }

    public function login(string $username, string $password): UserToken
    {
        $response = $this->http->post(
            '/users/login',
            body: json_encode([
                'username' => $username,
                'password' => $password,
            ], JSON_THROW_ON_ERROR),
        );

        if (200 !== $response->getStatusCode()) {
            throw $this->createException($response);
        }

        return UserToken::fromJson($response->getBody()->getContents());
    }

    public function getRepositoryImagesSummary(string $namespace, string $repository, array $parameters = []): RepositoryImageSummary
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined('active_from');
        $resolver->setAllowedTypes('active_from', 'DateTimeInterface');
        $resolver->setNormalizer('active_from', static fn(Options $options, DateTimeInterface $value): string => $value->format('Y-m-d\TH:i:s.u\Z'));
        $parameters = $resolver->resolve($parameters);

        $response = $this->http->get(
            $this->generateUrl("/namespaces/$namespace/repositories/$repository/images-summary?", $parameters),
            $this->getHeaders(),
        );

        if (200 !== $response->getStatusCode()) {
            throw $this->createException($response);
        }

        return RepositoryImageSummary::fromJson($response->getBody()->getContents());
    }

    /**
     * @return PaginatedResult<RepositoryImageDetail>
     */
    public function getRepositoryImagesDetails(string $namespace, string $repository, array $parameters = []): PaginatedResult
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined(['status', 'currently_tagged', 'ordering', 'active_from', 'page', 'page_size']);
        $resolver->setAllowedTypes('status', 'string');
        $resolver->setAllowedTypes('currently_tagged', 'bool');
        $resolver->setAllowedTypes('ordering', 'string'); // enum ?
        $resolver->setAllowedTypes('active_from', 'DateTimeInterface');
        $resolver->setAllowedTypes('page', 'int');
        $resolver->setAllowedTypes('page_size', 'int');
        $resolver->setAllowedValues('status', ['active', 'inactive']);
        $resolver->setAllowedValues('ordering', ['last_activity', '-last_activity', 'digest', '-digest']);
        $resolver->setAllowedValues('page', static fn(int $page): bool => 0 < $page);
        $resolver->setAllowedValues('page_size', static fn(int $pageSize): bool => 0 < $pageSize && $pageSize <= 100);
        $resolver->setNormalizer('active_from', static fn(Options $options, DateTimeInterface $value): string => $value->format('Y-m-d\TH:i:s.u\Z'));
        $parameters = $resolver->resolve($parameters);

        $response = $this->http->get(
            $this->generateUrl("/namespaces/$namespace/repositories/$repository/images", $parameters),
            $this->getHeaders(),
        );

        if (200 !== $response->getStatusCode()) {
            throw $this->createException($response);
        }

        return PaginatedResult::fromJson(
            $response->getBody()->getContents(),
            static fn(array $results): array => RepositoryImageDetail::fromList($results),
        );
    }

    /**
     * @return PaginatedResult<ImageTag>
     */
    public function getImageTags(string $namespace, string $repository, array $parameters = []): PaginatedResult
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined(['page', 'page_size']);
        $resolver->setAllowedTypes('page', 'int');
        $resolver->setAllowedTypes('page_size', 'int');
        $resolver->setAllowedValues('page', static fn(int $page): bool => 0 < $page);
        $resolver->setAllowedValues('page_size', static fn(int $pageSize): bool => 0 < $pageSize && $pageSize <= 100);
        $parameters = $resolver->resolve($parameters);

        $response = $this->http->get(
            $this->generateUrl("/repositories/$namespace/$repository/tags", $parameters),
            $this->getHeaders(),
        );

        if (200 !== $response->getStatusCode()) {
            throw $this->createException($response);
        }

        return PaginatedResult::fromJson(
            $response->getBody()->getContents(),
            static fn(array $results): array => ImageTag::fromList($results),
        );
    }

    /**
     * @return PaginatedResult<Tag>
     */
    public function getImageDigestTags(string $namespace, string $repository, string $digest, array $parameters = []): PaginatedResult
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined(['page', 'page_size']);
        $resolver->setAllowedTypes('page', 'int');
        $resolver->setAllowedTypes('page_size', 'int');
        $resolver->setAllowedValues('page', static fn(int $page): bool => 0 < $page);
        $resolver->setAllowedValues('page_size', static fn(int $pageSize): bool => 0 < $pageSize && $pageSize <= 100);
        $parameters = $resolver->resolve($parameters);

        $response = $this->http->get(
            $this->generateUrl("/namespaces/$namespace/repositories/$repository/images/$digest/tags", $parameters),
            $this->getHeaders(),
        );

        if (200 !== $response->getStatusCode()) {
            throw $this->createException($response);
        }

        return PaginatedResult::fromJson(
            $response->getBody()->getContents(),
            static fn(array $results): array => ImageTag::fromList($results),
        );
    }

    public function getUser(string $username): User
    {
        $response = $this->http->get(
            "/users/$username/",
            $this->getHeaders(),
        );

        if (200 !== $response->getStatusCode()) {
            throw $this->createException($response);
        }

        return User::fromJson($response->getBody()->getContents());
    }

    /**
     * TODO:
     */
    public function deleteImages(string $namespace): DeletionResult
    {
        $response = $this->http->delete(
            "/namespaces/$namespace/delete-images",
            $this->getHeaders(),
            json_encode([
                'dry_run' => '',
                'active_from' => '',
                'manifest' => [
                    [
                        'repository' => '', // required
                        'digest' => '', // required
                    ],
                    // ...
                ],
                'ignore_warning' => [
                    [
                        'repository' => '', // required
                        'digest' => '', // required
                        'warning' => '', // required
                        'tags' => ['', ''],
                    ],
                    // ...
                ],
            ], JSON_THROW_ON_ERROR),
        );

        if (200 !== $response->getStatusCode()) {
            throw $this->createException($response);
        }

        return DeletionResult::fromJson($response->getBody()->getContents());
    }

    private function getHeaders(): array
    {
        $headers = [];

        if (null !== $this->authenticationToken) {
            $headers['Authorization'] = "Bearer {$this->authenticationToken->getToken()}";
        }

        return $headers;
    }

    private function generateUrl(string $url, array $parameters): string
    {
        return trim("$url?".http_build_query($parameters), '?');
    }

    private function createException(ResponseInterface $response): Throwable
    {
        return match ($response->getStatusCode()) {
            400 => throw BadRequest::fromResponse($response),
            401 => throw Unauthorized::fromResponse($response),
            403 => throw Forbidden::fromResponse($response),
            404 => throw NotFound::fromResponse($response),
            default => throw DockerHubException::fromResponse($response),
        };
    }
}
