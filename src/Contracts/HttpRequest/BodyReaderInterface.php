<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts\HttpRequest;

interface BodyReaderInterface
{
    public function getBytesLeft(): int;

    /**
     * @param positive-int $maxBytesToRead
     * @return string
     */
    public function read(int $maxBytesToRead): string;
}
