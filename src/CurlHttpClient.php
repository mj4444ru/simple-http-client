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

    public function request(HttpRequestInterface $request): HttpResponseInterface
    {
        $method = $request->getMethod();
        $url = $request->getUrl();
        $headers = $request->getHeaders() ?? [];
        $body = $request->getBody();
        $isPost = $body !== null;

        $options = $this->options;
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_POST] = $isPost;
        if ($isPost) {
            $options[CURLOPT_POSTFIELDS] = $body;
        }
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
     */
    private function execute(array $options, HttpRequestInterface $request): HttpResponseInterface
    {
        $options[CURLOPT_RETURNTRANSFER] = true;

        /** @var array<string, list<string>> $headers */
        $headers = [];
        if ($request->isResponseHeadersRequired()) {
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

        /** @psalm-suppress MixedArgument */
        return new HttpResponse(
            $request,
            curl_getinfo($this->curl, CURLINFO_HTTP_CODE),
            $headers,
            curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE),
            $response
        );
    }
}
