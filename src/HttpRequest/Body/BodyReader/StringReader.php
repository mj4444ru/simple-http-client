<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body\BodyReader;

use Closure;
use Mj4444\SimpleHttpClient\Exceptions\ReaderException;

use function strlen;

/**
 * @internal
 */
final class StringReader extends BaseReader
{
    /**
     * @param non-negative-int $offset
     * @param Closure(non-negative-int $bytesSent, non-negative-int $totalBytes): void|null $progressCallback
     */
    public function __construct(
        private readonly string $content,
        private int $offset = 0,
        ?int $length = null,
        ?Closure $progressCallback = null
    ) {
        parent::__construct($offset, $length, $progressCallback);
    }

    protected function calcBytesLeft(?int $offset, ?int $length): int
    {
        if ($length === null) {
            /** @var non-negative-int $bytesLeft */
            $bytesLeft = strlen($this->content) - $this->offset;
        } elseif ($length >= 0) {
            $bytesLeft = $length;
        } else {
            $bytesLeft = strlen($this->content) - $this->offset + $length;

            if ($bytesLeft < 0) {
                throw new ReaderException('A negative data length value was received.');
            }
        }

        return $bytesLeft;
    }

    protected function readBytes(int $bytesToRead): string
    {
        $content = substr($this->content, $this->offset, $bytesToRead);

        $this->offset += $bytesToRead;

        return $content;
    }

    protected function setOffset(int $offset): void
    {
        if ($offset > strlen($this->content)) {
            throw new ReaderException('Invalid offset.');
        }
    }
}
