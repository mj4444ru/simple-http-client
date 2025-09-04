<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Exceptions\HttpResponse;

use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;

final class UnexpectedContentTypeException extends HttpResponseException
{
    public function __construct(HttpResponseInterface $response)
    {
        parent::__construct('Unexpected ContentType.', 0, $response);
    }
}
