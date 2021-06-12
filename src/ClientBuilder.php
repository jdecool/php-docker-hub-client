<?php

declare(strict_types=1);

namespace JDecool\DockerHub;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class ClientBuilder
{
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;
    private UriFactoryInterface $uriFactory;
    /** @var Plugin[] */
    private array $plugins;

    public static function createDefault(): Client
    {
        return (new self())->create('https://hub.docker.com/v2');
    }

    public function __construct(
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        ?UriFactoryInterface $uriFactory = null,
    ) {
        $this->httpClient = $httpClient ?? Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?? Psr17FactoryDiscovery::findStreamFactory();
        $this->uriFactory = $uriFactory ?? Psr17FactoryDiscovery::findUriFactory();
        $this->plugins = [];
    }

    public function create(string $url): Client
    {
        $uri = $this->uriFactory->createUri($url);
        $plugins = array_merge([
            new Plugin\ContentTypePlugin(),
            new Plugin\AddHostPlugin($uri),
            new Plugin\AddPathPlugin($uri),
        ], $this->plugins);

        $http = new HttpMethodsClient(
            new PluginClient($this->httpClient, $plugins),
            $this->requestFactory,
            $this->streamFactory,
        );

        return new Client($http);
    }

    public function addPlugin(Plugin $plugin): void
    {
        $this->plugins[] = $plugin;
    }
}
