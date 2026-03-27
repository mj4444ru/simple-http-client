<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts\HttpRequest;

interface FormInterface
{
    /**
     * @return array<non-empty-string, string|int|FileInterface|StringFileInterface>
     */
    public function getFields(): array;
}
