<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Exceptions\HttpResponse;

use JsonException;
use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;

final class JsonDecodeException extends ParseDataException
{
    public function __construct(HttpResponseInterface $response, ?JsonException $previous = null)
    {
        parent::__construct('JSON decode error.', 0, $response, $previous);
    }
}
