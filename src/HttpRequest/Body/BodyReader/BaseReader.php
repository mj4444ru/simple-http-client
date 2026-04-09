<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body\BodyReader;

use Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyReaderInterface;
use Mj4444\SimpleHttpClient\Exceptions\ReaderException;

use function strlen;

abstract class BaseReader implements BodyReaderInterface
{
    /**
     * @var non-negative-int
     */
    private int $bytesLeft;

    public function __construct(
        ?int $offset = null,
        ?int $length = null
    ) {
        if ($offset !== null) {
            if ($offset < 0) {
                throw new ReaderException('Offset cannot be negative.');
            }
            $this->setOffset($offset);
        }

        $this->bytesLeft = $this->calcBytesLeft($offset, $length);
    }

    final public function getBytesLeft(): int
    {
        return $this->bytesLeft;
    }

    final public function read(int $maxBytesToRead): string
    {
        $bytesToRead = min($maxBytesToRead, $this->bytesLeft);

        if ($this->bytesLeft <= 0 || $bytesToRead <= 0) {
            return '';
        }

        /** @psalm-suppress InvalidPropertyAssignmentValue */
        $this->bytesLeft -= $bytesToRead;

        $content = $this->readBytes($bytesToRead);

        if ($content === false || strlen($content) < $bytesToRead) {
            throw new ReaderException('Not enough data to read.');
        }

        return $content;
    }

    /**
     * @param non-negative-int|null $offset
     * @return non-negative-int
     */
    abstract protected function calcBytesLeft(?int $offset, ?int $length): int;

    /**
     * @param positive-int $bytesToRead
     * @return string|false
     */
    abstract protected function readBytes(int $bytesToRead): string|false;

    /**
     * @param non-negative-int $offset
     * @return void
     */
    abstract protected function setOffset(int $offset): void;
}
