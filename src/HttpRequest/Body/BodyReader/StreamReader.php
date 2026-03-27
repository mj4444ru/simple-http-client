<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body\BodyReader;

use CurlHandle;
use Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyReaderInterface;
use Mj4444\SimpleHttpClient\Exceptions\ReaderException;
use Throwable;

use function is_int;
use function strlen;

class StreamReader implements BodyReaderInterface
{
    private int $bytesLeft = 0;

    /**
     * @param resource $resource
     */
    public function __construct(
        protected $resource,
        ?int $offset = null,
        ?int $length = null
    ) {
        if ($offset !== null) {
            if ($offset < 0) {
                throw new ReaderException('Stream offset cannot be negative.');
            }
            $this->checkSeekable();
            if (fseek($this->resource, $offset) !== 0) {
                throw new ReaderException('Invalid offset for stream.');
            }
        }

        if ($length !== null && $length >= 0) {
            $this->bytesLeft = $length;
        } else {
            if ($offset === null) {
                $this->checkSeekable();
                $pos = ftell($resource);
                if ($pos === false) {
                    throw new ReaderException('Stream seekable is not available.');
                }
                $offset = $pos;
            }

            $stat = @fstat($this->resource);
            if ($stat === false || !is_int($size = $stat['size'] ?? null)) {
                if (@fseek($resource, 0, SEEK_END) !== 0) {
                    throw new ReaderException('Stream seekable is not available.');
                }
                $size = @ftell($resource);
                if ($size === false) {
                    throw new ReaderException('Stream seekable is not available.');
                }
                if (@fseek($resource, $offset) !== 0) {
                    throw new ReaderException('Stream seekable is not available.');
                }
            }

            $this->bytesLeft = $length === null
                ? $size - $offset
                : $size - $offset + $length;

            if ($this->bytesLeft < 0) {
                throw new ReaderException('A negative data length value was received.');
            }
        }
    }

    public function getBytesLeft(): int
    {
        return $this->bytesLeft;
    }

    public function getResource(): null
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function read(CurlHandle $curlHandle, $streamResource, int $maxAmountOfDataToRead): string
    {
        if ($this->bytesLeft === 0) {
            return '';
        }

        $bytesToRead = max(min($maxAmountOfDataToRead, $this->bytesLeft), 0);

        $this->bytesLeft -= $bytesToRead;

        $content = @fread($this->resource, $bytesToRead);

        if ($content === false || strlen($content) < $bytesToRead) {
            throw new ReaderException('File content is too short.');
        }

        return $content;
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
