<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts\HttpRequest;

interface BodyInterface
{
    public function getBody(): string|FormInterface|BodyReaderInterface;

    /**
     * @return non-empty-string|null
     */
    public function getContentType(): ?string;
}
