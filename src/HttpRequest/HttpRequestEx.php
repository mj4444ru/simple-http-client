<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest;

use Closure;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestExInterface;
use Mj4444\SimpleHttpClient\HttpResponse\HttpResponseEx;

/**
 * @extends BaseHttpRequest<HttpResponseEx>
 */
final class HttpRequestEx extends BaseHttpRequest implements HttpRequestExInterface
{
    /**
     * @var positive-int|null
     */
    public ?int $lowSpeedLimit = null;
    /**
     * @var positive-int|null
     */
    public ?int $lowSpeedTime = null;
    /**
     * @var (Closure(int $bytesToDownload, int $bytesDownloaded, int $bytesToUpload, int $bytesUploaded): bool)|null
     */
    public ?Closure $progressCallback = null;
    /**
     * @var resource|null
     */
    public mixed $resourceForResponseBody = null;
    /**
     * @var non-negative-int|null
     */
    public ?int $resumeFrom = null;
    /**
     * @var (Closure(string $data): int)|null
     */
    public ?Closure $writeFunction = null;

    public function getLowSpeedLimit(): ?int
    {
        return $this->lowSpeedLimit;
    }

    public function getLowSpeedTime(): ?int
    {
        return $this->lowSpeedTime;
    }

    public function getProgressCallback(): ?Closure
    {
        return $this->progressCallback;
    }

    public function getResourceForResponseBody(): mixed
    {
        return $this->resourceForResponseBody;
    }

    public function getResumeFrom(): ?int
    {
        return $this->resumeFrom;
    }

    public function getWriteFunction(): ?Closure
    {
        return $this->writeFunction;
    }

    public function makeResponse(
        int $httpCode,
        string $url,
        string $effectiveUrl,
        ?string $redirectUrl,
        array $headers,
        ?string $contentType,
        string $response
    ): HttpResponseEx {
        return new HttpResponseEx(
            $this,
            $httpCode,
            $url,
            $effectiveUrl,
            $redirectUrl,
            $headers,
            $contentType,
            $response,
            $this->expectedContentType
        );
    }

    /**
     * Sets the minimum transfer speed, in bytes per second, that is considered acceptable.
     *
     * If the average transfer speed falls below this value for the duration specified by `getLowSpeedTime()`,
     * the transfer will be aborted.
     *
     * @param positive-int|null $lowSpeedLimit
     * @return $this
     */
    public function setLowSpeedLimit(?int $lowSpeedLimit): self
    {
        $this->lowSpeedLimit = $lowSpeedLimit;

        return $this;
    }

    /**
     * Sets the number of seconds that the transfer speed must stay below `getLowSpeedLimit()`
     * before the transfer is aborted.
     *
     * @param positive-int|null $lowSpeedTime
     * @return $this
     */
    public function setLowSpeedTime(?int $lowSpeedTime): self
    {
        $this->lowSpeedTime = $lowSpeedTime;

        return $this;
    }

    /**
     * Sets a progress callback that is called during the transfer.
     *
     * The callback function must return true to continue and false to abort the transfer.
     *
     * @param (Closure(int $bytesToDownload, int $bytesDownloaded, int $bytesToUpload, int $bytesUploaded): bool)|null $progressCallback
     * @return $this
     */
    public function setProgressCallback(?Closure $progressCallback): self
    {
        $this->progressCallback = $progressCallback;

        return $this;
    }

    /**
     * Sets a resource to write the response body to directly.
     *
     * @param resource|null $resourceForResponseBody
     * @param non-negative-int|null $resumeFrom
     * @return $this
     */
    public function setResourceForResponseBody(mixed $resourceForResponseBody, ?int $resumeFrom = null): self
    {
        $this->resourceForResponseBody = $resourceForResponseBody;
        if ($resumeFrom !== null) {
            $this->resumeFrom = $resumeFrom;
        }

        return $this;
    }

    /**
     * Sets the offset, in bytes, to resume a transfer from.
     *
     * Used together with `setResourceForResponseBody()` to continue writing to the resource at the specified position.
     *
     * @param non-negative-int|null $resumeFrom
     * @return $this
     */
    public function setResumeFrom(?int $resumeFrom): self
    {
        $this->resumeFrom = $resumeFrom;

        return $this;
    }

    /**
     * Sets a write callback for processing received data.
     *
     * The data must be saved by the callback and the callback must return the exact number of bytes written or
     * the transfer will be aborted with an error.
     *
     * @param (Closure(string $data): int)|null $writeFunction
     * @return $this
     */
    public function setWriteFunction(?Closure $writeFunction): self
    {
        $this->writeFunction = $writeFunction;

        return $this;
    }
}
