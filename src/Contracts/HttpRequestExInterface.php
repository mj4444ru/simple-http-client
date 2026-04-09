<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

use Closure;

/**
 * Extended HTTP request interface with additional features such as
 * progress tracking, direct response body streaming, and custom write callbacks.
 */
interface HttpRequestExInterface
{
    /**
     * Returns the minimum transfer speed, in bytes per second, that is considered acceptable.
     *
     * If the average transfer speed falls below this value for the duration specified by `getLowSpeedTime()`,
     * the transfer will be aborted.
     *
     * @return positive-int|null
     */
    public function getLowSpeedLimit(): ?int;

    /**
     * Returns the number of seconds that the transfer speed must stay below `getLowSpeedLimit()`
     * before the transfer is aborted.
     *
     * @return positive-int|null
     */
    public function getLowSpeedTime(): ?int;

    /**
     * Returns the progress callback that is called during the transfer.
     *
     * The callback function must return true to continue and false to abort the transfer.
     *
     * @return (Closure(int $bytesToDownload, int $bytesDownloaded, int $bytesToUpload, int $bytesUploaded): bool)|null
     */
    public function getProgressCallback(): ?Closure;

    /**
     * Returns the resource used to write the response body to directly.
     *
     * @return resource|null
     */
    public function getResourceForResponseBody(): mixed;

    /**
     * Returns the offset, in bytes, to resume a transfer from.
     *
     * Used together with `getResourceForResponseBody()` to continue writing to the resource at the specified position.
     *
     * @return non-negative-int|null
     */
    public function getResumeFrom(): ?int;

    /**
     * Returns the write callback used for processing received data.
     *
     * The data must be saved by the callback and the callback must return the exact number of bytes written or
     * the transfer will be aborted with an error.
     *
     * @return (Closure(string $data): int)|null
     */
    public function getWriteFunction(): ?Closure;
}
