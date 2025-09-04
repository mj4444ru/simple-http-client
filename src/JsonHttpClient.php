<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient;

use JsonException;
use Mj4444\SimpleHttpClient\Contracts\HttpClientInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpMethod;
use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;
use Mj4444\SimpleHttpClient\Exceptions\JsonHttpClient\BodyNotValidJsonException;
use Mj4444\SimpleHttpClient\Exceptions\JsonHttpClient\ResponseNotJsonException;
use Mj4444\SimpleHttpClient\Exceptions\JsonHttpClient\ResponseNotValidJsonException;

use function is_array;

class JsonHttpClient
{
    /**
     * @var list<int>
     */
    public array $allowedResponseCodes = [200];
    /**
     * @var non-empty-string|null
     */
    public ?string $expectedContentType = 'application/json; charset=utf-8';
    /**
     * @var non-empty-string|null
     */
    public ?string $requestAccept = 'application/json; charset=utf-8';
    /**
     * @var non-empty-string
     */
    public string $requestContentType = 'application/json; charset=utf-8';

    public function __construct(
        protected HttpClientInterface $client
    ) {
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     */
    public function delete(string $url, ?array $query = null, array $headers = []): array
    {
        return $this->getEx($url, $query, HttpMethod::Delete, $headers);
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     */
    public function get(string $url, ?array $query = null, array $headers = []): array
    {
        return $this->getEx($url, $query, HttpMethod::Get, $headers);
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     */
    public function patch(string $url, mixed $body, ?array $query = null, array $headers = []): array
    {
        return $this->postEx($url, $body, $query, HttpMethod::Patch, $headers);
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     */
    public function post(string $url, mixed $body, ?array $query = null, array $headers = []): array
    {
        return $this->postEx($url, $body, $query, HttpMethod::Post, $headers);
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     */
    public function put(string $url, mixed $body, ?array $query = null, array $headers = []): array
    {
        return $this->postEx($url, $body, $query, HttpMethod::Put, $headers);
    }

    /**
     * @param list<int> $allowedResponseCodes
     * @return $this
     */
    public function setAllowedResponseCodes(array $allowedResponseCodes): static
    {
        $this->allowedResponseCodes = $allowedResponseCodes;

        return $this;
    }

    protected function checkResponseContentType(HttpResponseInterface $response): void
    {
        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if ($this->expectedContentType) {
            $contentType = $response->getContentType() ?? '';

            if (
                strcasecmp($contentType, $this->expectedContentType) !== 0
                && (
                    !$this->requestAccept
                    || strcasecmp($contentType, $this->requestAccept) !== 0
                )
            ) {
                throw new ResponseNotJsonException($response);
            }
        }
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     */
    protected function getEx(string $url, ?array $query, HttpMethod $method, array $headers = []): array
    {
        $request = (new HttpRequest($url, $query, $method))
            ->setHeaders($headers)
            ->setAccept($this->requestAccept);

        $response = $this->client->request($request);

        if ($this->allowedResponseCodes) {
            $response->checkHttpCode($this->allowedResponseCodes);
        }

        return $this->parseResponse($response);
    }

    protected function parseResponse(HttpResponseInterface $response): array
    {
        $this->checkResponseContentType($response);

        try {
            $jsonData = json_decode($response->getBody(), true, 16, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ResponseNotValidJsonException($response, $e);
        }

        if (!is_array($jsonData)) {
            throw new ResponseNotValidJsonException($response);
        }

        return $jsonData;
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     */
    protected function postEx(string $url, mixed $body, ?array $query, HttpMethod $method, array $headers = []): array
    {
        try {
            $bodyString = json_encode($body, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new BodyNotValidJsonException($body, $e);
        }

        $request = (new HttpRequest($url, $query, $method))
            ->setHeaders($headers)
            ->setAccept($this->requestAccept)
            ->setContentType($this->requestContentType)
            ->setBody($bodyString);

        $response = $this->client->request($request);

        if ($this->allowedResponseCodes) {
            $response->checkHttpCode($this->allowedResponseCodes);
        }

        return $this->parseResponse($response);
    }
}
