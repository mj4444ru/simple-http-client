<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Exceptions\HttpRequest;

use JsonException;

final class JsonEncodeExceptionHttp extends HttpRequestException
{
    public function __construct(
        public readonly mixed $data,
        JsonException $previous
    ) {
        parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
