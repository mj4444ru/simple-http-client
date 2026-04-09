<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient;

use CURLFile;
use CurlHandle;
use CurlShareHandle;
use CURLStringFile;
use LogicException;
use Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyReaderInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequest\FileInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequest\FormInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequest\StringFileInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestExInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;
use Mj4444\SimpleHttpClient\Exceptions\CurlException;
use Mj4444\SimpleHttpClient\Exceptions\HttpRequest\BodyRequiredException;

use function count;
use function is_string;
use function strlen;

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
     * @return array<int, mixed> Curl options
     */
    public function getOptions(): array
    {
        return $this->options;
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
            if (curl_share_setopt($sh, CURLSHOPT_SHARE, CURL_LOCK_DATA_CONNECT) === false) {
                throw new LogicException('Failed to set CURL_LOCK_DATA_CONNECT on curl share handle.');
            }
        }
        if ($all || $cookie) {
            if (curl_share_setopt($sh, CURLSHOPT_SHARE, CURL_LOCK_DATA_COOKIE) === false) {
                throw new LogicException('Failed to set CURL_LOCK_DATA_COOKIE on curl share handle.');
            }
        }
        if ($all || $dns) {
            if (curl_share_setopt($sh, CURLSHOPT_SHARE, CURL_LOCK_DATA_DNS) === false) {
                throw new LogicException('Failed to set CURL_LOCK_DATA_DNS on curl share handle.');
            }
        }
        if ($all || $psl) {
            if (curl_share_setopt($sh, CURLSHOPT_SHARE, CURL_LOCK_DATA_PSL) === false) {
                throw new LogicException('Failed to set CURL_LOCK_DATA_PSL on curl share handle.');
            }
        }
        if ($all || $sslSession) {
            if (curl_share_setopt($sh, CURLSHOPT_SHARE, CURL_LOCK_DATA_SSL_SESSION) === false) {
                throw new LogicException('Failed to set CURL_LOCK_DATA_SSL_SESSION on curl share handle.');
            }
        }
        $this->setOption(CURLOPT_SHARE, $sh);

        return $this;
    }

    /**
     * {@inheritDoc}
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
            $body = $request->getBody() ?? throw new BodyRequiredException($request);
            $this->preparePost($options, $body);
        }

        if (($connectTimeout = $request->getConnectTimeout()) !== null) {
            unset($options[CURLOPT_CONNECTTIMEOUT]);
            if ($connectTimeout === 0 || $connectTimeout > 0) {
                $options[CURLOPT_CONNECTTIMEOUT_MS] = $connectTimeout;
            } else {
                unset($options[CURLOPT_CONNECTTIMEOUT_MS]);
            }
        }
        if (($timeout = $request->getTimeout()) !== null) {
            unset($options[CURLOPT_TIMEOUT]);
            if ($timeout === 0 || $timeout > 0) {
                $options[CURLOPT_TIMEOUT_MS] = $timeout;
            } else {
                unset($options[CURLOPT_TIMEOUT_MS]);
            }
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

    public function setConnectTimeout(?int $connectTimeout): static
    {
        return $this->setOption(CURLOPT_CONNECTTIMEOUT_MS, $connectTimeout);
    }

    /**
     * @return $this
     */
    public function setOption(int $option, string|int|bool|CurlShareHandle|null $value): static
    {
        if ($value === null) {
            unset($this->options[$option]);
        } else {
            $this->options[$option] = $value;
        }

        return $this;
    }

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
        return $this->setOption(CURLOPT_REFERER, $referer);
    }

    public function setTimeout(?int $timeout): static
    {
        return $this->setOption(CURLOPT_TIMEOUT_MS, $timeout);
    }

    public function setUsePersistentCurlHandle(bool $usePersistentCurlHandle): void
    {
        $this->usePersistentCurlHandle = $usePersistentCurlHandle;
    }

    public function setUserAgent(?string $userAgent): static
    {
        return $this->setOption(CURLOPT_USERAGENT, $userAgent);
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
                } else {
                    $headers[''][] = trim($header);
                }

                return strlen($header);
            };
        }

        $curlHandle = $this->getCurlHandle();

        curl_setopt_array($curlHandle, $options);

        /** @var string|false $response */
        $response = $request instanceof HttpRequestExInterface
            ? $this->executeEx($curlHandle, $request)
            : curl_exec($curlHandle);

        if ($this->curlInfoRequired) {
            /** @psalm-suppress MixedAssignment */
            $this->lastCurlInfo = curl_getinfo($curlHandle) ?: [];
        }

        if ($response === false) {
            throw new CurlException(curl_error($curlHandle), curl_errno($curlHandle));
        }

        return $this->makeResponse($request, $curlHandle, $response, (string)$options[CURLOPT_URL], $headers);
    }

    protected function executeEx(CurlHandle $curlHandle, HttpRequestExInterface $request): string|false
    {
        $options = [];

        $lowSpeedLimit = $request->getLowSpeedLimit();
        $lowSpeedTime = $request->getLowSpeedTime();
        if ($lowSpeedLimit !== null && $lowSpeedTime !== null) {
            $options[CURLOPT_LOW_SPEED_LIMIT] = $lowSpeedLimit;
            $options[CURLOPT_LOW_SPEED_TIME] = $lowSpeedTime;
        }

        if (($resource = $request->getResourceForResponseBody()) !== null) {
            $options[CURLOPT_FILE] = $resource;

            if (($resumeFrom = $request->getResumeFrom()) !== null) {
                $options[CURLOPT_RESUME_FROM] = $resumeFrom;
            }
        } elseif (($writeFunction = $request->getWriteFunction()) !== null) {
            $options[CURLOPT_WRITEFUNCTION] = $writeFunction;
        }

        if (($progressCallback = $request->getProgressCallback()) !== null) {
            $state = null;
            $fn = static function (CurlHandle $_curlHandle, int $p1, int $p2, int $p3, int $p4) use ($progressCallback, &$state): int {
                $newState = "$p1|$p2|$p3|$p4";
                if ($state === $newState) {
                    return 0;
                }

                $state = $newState;

                return ($progressCallback)($p1, $p2, $p3, $p4) ? 0 : 1;
            };
            $options[CURLOPT_NOPROGRESS] = false;
            $options[CURLOPT_XFERINFOFUNCTION] = $fn;
            $options[CURLOPT_PROGRESSFUNCTION] = $fn;
        }

        if ($options) {
            curl_setopt_array($curlHandle, $options);
        }

        $result = curl_exec($curlHandle);

        return $result === true ? '' : $result;
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

    /**
     * @param array<int, mixed> $options
     */
    protected function preparePost(array &$options, BodyInterface $body): void
    {
        $postData = $body->getBody();

        if ($postData instanceof FormInterface) {
            $fields = $postData->getFields();

            foreach ($fields as &$value) {
                if ($value instanceof FileInterface) {
                    $value = new CURLFile($value->getFileName(), $value->getMime(), $value->getPostName());
                } elseif ($value instanceof StringFileInterface) {
                    $value = new CURLStringFile($value->getData(), $value->getPostName(), $value->getMime());
                }
            }

            $options[CURLOPT_POSTFIELDS] = $fields;

            return;
        }

        /** @psalm-suppress MixedArrayAssignment */
        $options[CURLOPT_HTTPHEADER][] = 'Content-Type: ' . ($body->getContentType() ?? '');

        if ($postData instanceof BodyReaderInterface) {
            $options[CURLOPT_INFILESIZE] = $postData->getBytesLeft();
            $options[CURLOPT_READFUNCTION] =
                /**
                 * @param positive-int $maxBytesToRead
                 */
                static fn(mixed $p1, mixed $p2, int $maxBytesToRead): string => $postData->read($maxBytesToRead);
        } else {
            $options[CURLOPT_POSTFIELDS] = $postData;
        }
    }
}
