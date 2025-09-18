<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient;

use CurlHandle;
use LogicException;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;
use Mj4444\SimpleHttpClient\Exceptions\CurlException;

use function count;
use function strlen;

/**
 * @api
 */
class CurlHttpClient extends BaseHttpClient
{
    private readonly CurlHandle $curl;

    /**
     * @param array<int, mixed> $options
     */
    public function __construct(
        private array $options = [],
        CurlHandle $curlHandle = null
    ) {
        $this->curl = $curlHandle ?? curl_init()
            ?: throw new LogicException('Curl http request needs to be initialized');
    }

    /**
     * @inheritDoc
     *
     * @template TResponse of HttpResponseInterface
     * @param HttpRequestInterface<TResponse> $request
     * @return TResponse
     * @throws CurlException
     */
    public function request(HttpRequestInterface $request): HttpResponseInterface
    {
        $body = $request->getBody();
        $isPost = $body !== null;
        $maxRedirects = $request->getMaxRedirects() ?? $this->maxRedirects;

        $options = [
            ...$this->options,
            CURLOPT_URL => $request->getUrl(),
            CURLOPT_POST => $isPost,
            CURLOPT_CUSTOMREQUEST => $request->getMethod(),
            CURLOPT_HTTPHEADER => [...$this->headers, ...$request->getHeaders()],
            CURLOPT_FOLLOWLOCATION => $maxRedirects > 0,
            CURLOPT_MAXREDIRS => max($maxRedirects, 0),
        ];

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
     * @inheritDoc
     */
    public function setConnectTimeout(?int $connectTimeout): static
    {
        $this->options[CURLOPT_CONNECTTIMEOUT] = $connectTimeout;

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
            $this->options[CURLOPT_PROXY] = null;
            $this->options[CURLOPT_HTTPPROXYTUNNEL] = null;
            $this->options[CURLOPT_PROXYTYPE] = null;

            return $this;
        }

        $this->options[CURLOPT_PROXY] = $proxyString;
        $this->options[CURLOPT_HTTPPROXYTUNNEL] = true;
        $this->options[CURLOPT_PROXYTYPE] = $socks5 ? CURLPROXY_SOCKS5 : CURLPROXY_HTTP;

        return $this;
    }

    public function setReferer(?string $referer): static
    {
        $this->options[CURLOPT_REFERER] = $referer;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTimeout(?int $timeout): static
    {
        $this->options[CURLOPT_TIMEOUT] = $timeout;

        return $this;
    }

    public function setUserAgent(?string $userAgent): static
    {
        $this->options[CURLOPT_USERAGENT] = $userAgent;

        return $this;
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
     * @template TResponse of HttpResponseInterface
     * @param array<int, mixed> $options
     * @param HttpRequestInterface<TResponse> $request
     * @return TResponse
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
        $effectiveUrl = (string)curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
        $contentType = (string)(curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE) ?: null);
        /** @var string $url */
        $url = $options[CURLOPT_URL];

        return $request->makeResponse($httpCode, $url, $effectiveUrl, $headers, $contentType, $response);
    }
}
