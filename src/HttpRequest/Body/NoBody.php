<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body;

use Mj4444\SimpleHttpClient\Contracts\HttpRequestBodyInterface;

final class NoBody implements HttpRequestBodyInterface
{
    public function getBody(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getBodyContentType(): null
    {
        return null;
    }
}
