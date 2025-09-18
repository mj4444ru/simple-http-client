<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient;

use CurlHandle;
use CurlShareHandle;
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
    public bool $curlInfoRequired = false;
    public ?array $lastCurlInfo = null;
    public bool $usePersistentCurlHandle = true;
    private ?CurlHandle $curlHandle = null;

    /**
     * @param array<int, mixed> $options Curl options
     */
    public function __construct(
        protected array $options = []
    ) {
    }

    /**
     * @return $this
     */
    public function initShareData(
        bool $all = false,
        bool $connect = false,
        bool $cookie = false,
        bool $dns = false,
        bool $psl = false,
        bool $sslSession = false
    ): static {
        $sh = curl_share_init();
        if ($all || $connect) {
            curl_share_setopt($sh, CURLSHOPT_SHARE, CURL_LOCK_DATA_CONNECT);
        }
        if ($all || $cookie) {
            curl_share_setopt($sh, CURLSHOPT_SHARE, CURL_LOCK_DATA_COOKIE);
        }
        if ($all || $dns) {
            curl_share_setopt($sh, CURLSHOPT_SHARE, CURL_LOCK_DATA_DNS);
        }
        if ($all || $psl) {
            curl_share_setopt($sh, CURLSHOPT_SHARE, CURL_LOCK_DATA_PSL);
        }
        if ($all || $sslSession) {
            curl_share_setopt($sh, CURLSHOPT_SHARE, CURL_LOCK_DATA_SSL_SESSION);
        }
        $this->setOption(CURLOPT_SHARE, $sh);

        return $this;
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

        $options = array_replace($this->options, [
            CURLOPT_URL => $request->getUrl(),
            CURLOPT_POST => $isPost,
            CURLOPT_HTTPHEADER => [...$this->headers, ...$request->getHeaders()],
            CURLOPT_FOLLOWLOCATION => $request->isFollowLocation() ?? $this->followLocation,
            CURLOPT_MAXREDIRS => max($request->getMaxRedirects() ?? $this->maxRedirects, -1),
        ]);

        $method = $request->getMethod();
        if (strcasecmp($method, $isPost ? 'POST' : 'GET') !== 0) {
            $options[CURLOPT_CUSTOMREQUEST] = $method;
        }

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
    public function setOption(int $option, string|int|bool|CurlShareHandle $value): static
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
            unset(
                $this->options[CURLOPT_PROXY],
                $this->options[CURLOPT_HTTPPROXYTUNNEL],
                $this->options[CURLOPT_PROXYTYPE]
            );

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

    public function setUsePersistentCurlHandle(bool $usePersistentCurlHandle): void
    {
        $this->usePersistentCurlHandle = $usePersistentCurlHandle;
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

        $curlHandle = $this->getCurlHandle();

        curl_setopt_array($curlHandle, $options);

        /** @var string|false $response */
        $response = curl_exec($curlHandle);

        if ($this->curlInfoRequired) {
            $this->lastCurlInfo = curl_getinfo($curlHandle) ?: [];
        }

        if ($response === false) {
            throw new CurlException(curl_error($curlHandle), curl_errno($curlHandle));
        }

        return $this->makeResponse($request, $curlHandle, $response, (string)$options[CURLOPT_URL], $headers);
    }

    protected function getCurlHandle(): CurlHandle
    {
        if ($this->usePersistentCurlHandle) {
            $this->curlHandle ??= curl_init()
                ?: throw new LogicException('Curl http request needs to be initialized');
            curl_reset($this->curlHandle);

            return $this->curlHandle;
        }

        return curl_init()
            ?: throw new LogicException('Curl http request needs to be initialized');
    }

    /**
     * @template TResponse of HttpResponseInterface
     * @param HttpRequestInterface<TResponse> $request
     * @param array<string, list<string>> $headers
     * @return TResponse
     */
    protected function makeResponse(
        HttpRequestInterface $request,
        CurlHandle $curlHandle,
        string $response,
        string $url,
        array $headers
    ): HttpResponseInterface {
        $httpCode = (int)curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        $effectiveUrl = (string)curl_getinfo($curlHandle, CURLINFO_EFFECTIVE_URL);
        $redirectUrl = is_string($_ = curl_getinfo($curlHandle, CURLINFO_REDIRECT_URL)) ? $_ : null;
        $contentType = is_string($_ = curl_getinfo($curlHandle, CURLINFO_CONTENT_TYPE)) ? $_ : null;

        return $request->makeResponse($httpCode, $url, $effectiveUrl, $redirectUrl, $headers, $contentType, $response);
    }
}
