<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body;

use Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequest\FileInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequest\FormInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequest\StringFileInterface;

final class MultipartFormBody implements BodyInterface, FormInterface
{
    /**
     * @param array<non-empty-string, string|int|FileInterface|StringFileInterface> $fields
     */
    public function __construct(
        public array $fields
    ) {
    }

    public function getBody(): self
    {
        return $this;
    }

    public function getContentType(): null
    {
        return null;
    }

    /**
     * @return array<non-empty-string, string|int|FileInterface|StringFileInterface>
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
