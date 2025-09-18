<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient;

use CurlHandle;
use LogicException;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;
use Mj4444\SimpleHttpClient\Exceptions\CurlException;
use Mj4444\SimpleHttpClient\Exceptions\HttpRequest\BodyRequiredException;

use function count;
use function is_string;
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

    public function getLastResponseInfo(): array
    {
        /** @var array */
        return curl_getinfo($this->curl) ?: [];
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
        $isPost = $request->isPost();

        $method = $request->getMethod();
        $customRequest = strcasecmp($method, $isPost ? 'POST' : 'GET') === 0
            ? null
            : $method;

        $options = array_replace($this->options, [
            CURLOPT_URL => $request->getUrl(),
            CURLOPT_POST => $isPost,
            CURLOPT_CUSTOMREQUEST => $customRequest,
            CURLOPT_HTTPHEADER => [...$this->headers, ...$request->getHeaders()],
            CURLOPT_FOLLOWLOCATION => $request->isFollowLocation() ?? $this->followLocation,
            CURLOPT_MAXREDIRS => max($request->getMaxRedirects() ?? $this->maxRedirects, -1),
        ]);

        if ($isPost) {
            $options[CURLOPT_POSTFIELDS] = $request->getBody() ?? throw new BodyRequiredException($request);
        }

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

        return $this->makeResponse($request, $response, (string)$options[CURLOPT_URL], $headers);
    }

    /**
     * @template TResponse of HttpResponseInterface
     * @param HttpRequestInterface<TResponse> $request
     * @param array<string, list<string>> $headers
     * @return TResponse
     */
    protected function makeResponse(
        HttpRequestInterface $request,
        string $response,
        string $url,
        array $headers
    ): HttpResponseInterface {
        $httpCode = (int)curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $effectiveUrl = (string)curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);
        $redirectUrl = is_string($_ = curl_getinfo($this->curl, CURLINFO_REDIRECT_URL)) ? $_ : null;
        $contentType = is_string($_ = curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE)) ? $_ : null;

        return $request->makeResponse($httpCode, $url, $effectiveUrl, $redirectUrl, $headers, $contentType, $response);
    }
}
