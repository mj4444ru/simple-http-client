<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient;

use CurlHandle;
use Mj4444\SimpleHttpClient\Contracts\HttpClientInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;
use Mj4444\SimpleHttpClient\Exceptions\CurlException;

use function count;
use function strlen;

/**
 * @api
 */
class CurlHttpClient implements HttpClientInterface
{
    /**
     * @var non-empty-string[]
     */
    public array $headers = [];
    public bool $responseHeadersRequired = false;
    private readonly CurlHandle $curl;

    /**
     * @param array<int, mixed> $options
     */
    public function __construct(
        private array $options = [],
        CurlHandle $curlHandle = null
    ) {
        $this->curl = $curlHandle ?? curl_init();
    }

    /**
     * @inheritDoc
     *
     * @throws CurlException
     */
    public function request(HttpRequestInterface $request): HttpResponseInterface
    {
        $method = $request->getMethod();
        $url = $request->getUrl();
        $body = $request->getBody();
        $isPost = $body !== null;
        $headers = [...$this->headers, ...$request->getHeaders($isPost)];

        $options = $this->options;
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_POST] = $isPost;
        $options[CURLOPT_CUSTOMREQUEST] = $method;
        $options[CURLOPT_HTTPHEADER] = $headers;

        return $this->execute($options, $request);
    }

    /**
     * @return $this
     */
    public function resetOptions(): static
    {
        $this->options = [];

        return $this;
    }

    /**
     * @param non-empty-string $header
     * @return $this
     */
    public function setHeader(string|int $index, string $header): static
    {
        $this->headers[$index] = $header;

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
     * @return $this
     */
    public function setOption(int $option, string|int|bool $value): static
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Enable proxy for curl requests. Empty string will disable proxy.
     *
     * @return $this
     */
    public function setProxy(string $proxyString = '', bool $socks5 = false): static
    {
        if (empty($proxyString)) {
            unset($this->options[CURLOPT_PROXY], $this->options[CURLOPT_HTTPPROXYTUNNEL], $this->options[CURLOPT_PROXYTYPE]);

            return $this;
        }

        $this->options[CURLOPT_PROXY] = $proxyString;
        $this->options[CURLOPT_HTTPPROXYTUNNEL] = true;

        if ($socks5) {
            $this->options[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5;
        }

        return $this;
    }

    public function setResponseHeadersRequired(bool $value): void
    {
        $this->responseHeadersRequired = $value;
    }

    /**
     * @return $this
     */
    public function unsetOption(int $option): static
    {
        unset($this->options[$option]);

        return $this;
    }

    /**
     * @param array<int, mixed> $options
     * @throws CurlException
     */
    protected function execute(array $options, HttpRequestInterface $request): HttpResponseInterface
    {
        $options[CURLOPT_RETURNTRANSFER] = true;

        /** @var array<string, list<string>> $headers */
        $headers = [];
        if ($request->isResponseHeadersRequired() ?? $this->responseHeadersRequired) {
            $options[CURLOPT_HEADERFUNCTION] = static function (CurlHandle $curl, string $header) use (&$headers): int {
                $parts = explode(':', $header, 2);
                if (count($parts) === 2) {
                    $headers[strtolower(trim($parts[0]))][] = trim($parts[1]);
                }

                return strlen($header);
            };
        }

        curl_setopt_array($this->curl, $options);

        /** @var string|false $response */
        $response = curl_exec($this->curl);
        if ($response === false) {
            throw new CurlException(curl_error($this->curl), curl_errno($this->curl));
        }

        $httpCode = (int)curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $url = (string)curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
        $contentType = (string)(curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE) ?: null);

        return $request->makeResponse($httpCode, $url, $headers, $contentType, $response);
    }
}
