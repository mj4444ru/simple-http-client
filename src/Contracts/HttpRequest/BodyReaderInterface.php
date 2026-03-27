<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts\HttpRequest;

use CurlHandle;

interface BodyReaderInterface
{
    public function getBytesLeft(): int;

    /**
     * @return resource|null
     */
    public function getResource();

    /**
     * @param resource|null $streamResource
     * @noinspection PhpMissingParamTypeInspection
     */
    public function read(CurlHandle $curlHandle, $streamResource, int $maxAmountOfDataToRead): string;
}
