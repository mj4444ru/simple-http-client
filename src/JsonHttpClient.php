<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient;

use Mj4444\SimpleHttpClient\Contracts\HttpClientInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpMethod;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestBodyInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;
use Mj4444\SimpleHttpClient\Exceptions\HttpClientException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\HttpException;
use Mj4444\SimpleHttpClient\HttpRequest\JsonHttpRequest;

class JsonHttpClient
{
    /**
     * @var int|non-empty-array<int>|null
     */
    public int|array|null $allowedHttpCode = 200;
    /**
     * @var lowercase-string|non-empty-array<lowercase-string|null>|null
     */
    public string|array|null $expectedContentType = [
        'application/json; charset=utf-8',
        'application/json;charset=utf-8',
        'application/json',
    ];
    /**
     * @var non-empty-string[]
     */
    public array $headers = [];
    /**
     * @var non-empty-string|null
     */
    public ?string $requestAccept = 'application/json; charset=utf-8';
    /**
     * @var non-empty-string|null
     */
    public ?string $requestContentType = 'application/json; charset=utf-8';
    public ?bool $responseHeadersRequired = null;

    public function __construct(
        protected HttpClientInterface $client
    ) {
    }

    /**
     * @param non-empty-string $header
     * @return $this
     */
    public function addHeader(string $header): static
    {
        $this->headers[] = $header;

        return $this;
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     * @throws HttpClientException
     * @throws HttpException
     */
    public function delete(string $url, ?array $query = null, array $headers = []): mixed
    {
        return $this->deleteEx($url, $query, $headers)->getData();
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     * @throws HttpClientException
     * @throws HttpException
     */
    public function deleteEx(string $url, ?array $query = null, array $headers = []): HttpResponseInterface
    {
        return $this->doGet($url, $query, HttpMethod::Delete, $headers);
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     * @throws HttpClientException
     * @throws HttpException
     */
    public function get(string $url, ?array $query = null, array $headers = []): mixed
    {
        return $this->getEx($url, $query, $headers)->getData();
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     * @throws HttpClientException
     * @throws HttpException
     */
    public function getEx(string $url, ?array $query = null, array $headers = []): HttpResponseInterface
    {
        return $this->doGet($url, $query, HttpMethod::Get, $headers);
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     * @throws HttpClientException
     * @throws HttpException
     */
    public function patch(string $url, mixed $body, ?array $query = null, array $headers = []): mixed
    {
        return $this->patchEx($url, $body, $query, $headers)->getData();
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     * @throws HttpClientException
     * @throws HttpException
     */
    public function patchEx(string $url, mixed $body, ?array $query = null, array $headers = []): HttpResponseInterface
    {
        return $this->doPost($url, $body, $query, HttpMethod::Patch, $headers);
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     * @throws HttpClientException
     * @throws HttpException
     */
    public function post(string $url, mixed $body, ?array $query = null, array $headers = []): mixed
    {
        return $this->postEx($url, $body, $query, $headers)->getData();
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     * @throws HttpClientException
     * @throws HttpException
     */
    public function postEx(string $url, mixed $body, ?array $query = null, array $headers = []): HttpResponseInterface
    {
        return $this->doPost($url, $body, $query, HttpMethod::Post, $headers);
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     * @throws HttpClientException
     * @throws HttpException
     */
    public function put(string $url, mixed $body, ?array $query = null, array $headers = []): mixed
    {
        return $this->putEx($url, $body, $query, $headers)->getData();
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     * @throws HttpClientException
     * @throws HttpException
     */
    public function putEx(string $url, mixed $body, ?array $query = null, array $headers = []): HttpResponseInterface
    {
        return $this->doPost($url, $body, $query, HttpMethod::Put, $headers);
    }

    /**
     * @param int|non-empty-array<int>|null $allowedHttpCode
     * @return $this
     */
    public function setAllowedHttpCode(int|array|null $allowedHttpCode): static
    {
        $this->allowedHttpCode = $allowedHttpCode;

        return $this;
    }

    /**
     * @param lowercase-string|non-empty-array<lowercase-string|null>|null $expectedContentType
     * @return $this
     */
    public function setExpectedContentType(array|string|null $expectedContentType): static
    {
        $this->expectedContentType = $expectedContentType;

        return $this;
    }

    /**
     * @param non-empty-string|null $header
     * @return $this
     */
    public function setHeader(string|int $index, ?string $header): static
    {
        if ($header === null) {
            unset($this->headers[$index]);
        } else {
            $this->headers[$index] = $header;
        }

        return $this;
    }

    /**
     * @param non-empty-string[] $headers
     * @return $this
     */
    public function setHeaders(array $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param non-empty-string|null $requestAccept
     * @return $this
     */
    public function setRequestAccept(?string $requestAccept): static
    {
        $this->requestAccept = $requestAccept;

        return $this;
    }

    /**
     * @param non-empty-string|null $requestContentType
     * @return $this
     */
    public function setRequestContentType(?string $requestContentType): static
    {
        $this->requestContentType = $requestContentType;

        return $this;
    }

    /**
     * @return $this
     */
    public function setResponseHeadersRequired(?bool $responseHeadersRequired = true): static
    {
        $this->responseHeadersRequired = $responseHeadersRequired;

        return $this;
    }

    /**
     * @throws HttpException
     */
    protected function checkHttpCode(HttpResponseInterface $response): void
    {
        if ($this->allowedHttpCode !== null) {
            $response->checkHttpCode($this->allowedHttpCode);
        }
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     * @throws HttpClientException
     * @throws HttpException
     */
    protected function doGet(string $url, ?array $query, HttpMethod $method, array $headers): HttpResponseInterface
    {
        $request = (new JsonHttpRequest($url, $query, $method))
            ->setHeaders([...$this->headers, ...$headers])
            ->setAccept($this->requestAccept)
            ->setExpectedContentType($this->expectedContentType)
            ->setResponseHeadersRequired($this->responseHeadersRequired);

        $response = $this->doRequest($request);

        $this->checkHttpCode($response);

        return $response;
    }

    /**
     * @param non-empty-string $url
     * @param array<string|int>|null $query
     * @param array<non-empty-string> $headers
     * @throws HttpClientException
     * @throws HttpException
     */
    protected function doPost(
        string $url,
        mixed $body,
        ?array $query,
        HttpMethod $method,
        array $headers
    ): HttpResponseInterface {
        $request = (new JsonHttpRequest($url, $query, $method))
            ->setHeaders([...$this->headers, ...$headers])
            ->setAccept($this->requestAccept)
            ->setExpectedContentType($this->expectedContentType)
            ->setResponseHeadersRequired($this->responseHeadersRequired);

        if ($body instanceof HttpRequestBodyInterface) {
            $request->setBody($body);
        } else {
            $request->setJsonBody($body, $this->requestContentType);
        }

        $response = $this->doRequest($request);

        $this->checkHttpCode($response);

        return $response;
    }

    /**
     * @throws HttpClientException
     */
    protected function doRequest(JsonHttpRequest $request): HttpResponseInterface
    {
        return $this->client->request($request);
    }
}
