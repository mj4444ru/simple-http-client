<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body;

use Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyInterface;

final class NoBody implements BodyInterface
{
    public function getBody(): string
    {
        return '';
    }

    public function getContentType(): null
    {
        return null;
    }
}
