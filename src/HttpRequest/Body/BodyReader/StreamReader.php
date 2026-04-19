<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body\BodyReader;

use Closure;
use Mj4444\SimpleHttpClient\Exceptions\HttpRequest\ReaderException;
use Throwable;

use function is_int;

/**
 * @internal
 */
class StreamReader extends BaseReader
{
    /**
     * @param resource $resource
     * @param non-negative-int|null $offset
     * @param Closure(non-negative-int $bytesSent, non-negative-int $totalBytes): void|null $progressCallback
     */
    public function __construct(
        private readonly mixed $resource,
        ?int $offset = null,
        ?int $length = null,
        ?Closure $progressCallback = null
    ) {
        parent::__construct($offset, $length, $progressCallback);
    }

    protected function calcBytesLeft(?int $offset, ?int $length): int
    {
        if ($length !== null && $length >= 0) {
            $bytesLeft = $length;
        } else {
            if ($offset === null) {
                $this->checkSeekable();
                $pos = @ftell($this->resource);
                if ($pos === false) {
                    throw new ReaderException('Unable to determine stream position.');
                }
                $offset = $pos;
            }

            $stat = @fstat($this->resource);
            if ($stat === false || !is_int($size = $stat['size'] ?? null)) {
                if (@fseek($this->resource, 0, SEEK_END) !== 0) {
                    throw new ReaderException('Unable to seek to end of stream.');
                }
                $size = @ftell($this->resource);
                if ($size === false) {
                    throw new ReaderException('Unable to determine stream position.');
                }
                if (@fseek($this->resource, $offset) !== 0) {
                    throw new ReaderException('Unable to seek to original stream position.');
                }
            }

            $bytesLeft = $length === null
                ? $size - $offset
                : $size - $offset + $length;

            if ($bytesLeft < 0) {
                throw new ReaderException('A negative data length value was received.');
            }
        }

        return $bytesLeft;
    }

    protected function readBytes(int $bytesToRead): string|false
    {
        return @fread($this->resource, $bytesToRead);
    }

    protected function setOffset(int $offset): void
    {
        $this->checkSeekable();

        if (fseek($this->resource, $offset) !== 0) {
            throw new ReaderException('Invalid offset.');
        }
    }

    private function checkSeekable(): void
    {
        try {
            $meta = @stream_get_meta_data($this->resource);
            $seekable = $meta['seekable'] ?? false;
        } catch (Throwable) {
            $seekable = false;
        }

        if (!$seekable) {
            throw new ReaderException('Stream seekable is not available.');
        }
    }
}
