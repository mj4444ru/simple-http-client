<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Contracts;

interface HttpRequestTimeoutInterface
{
    /**
     * @return non-negative-int|null
     */
    public function getConnectTimeout(): ?int;

    /**
     * @return non-negative-int|null
     */
    public function getTimeout(): ?int;
}
