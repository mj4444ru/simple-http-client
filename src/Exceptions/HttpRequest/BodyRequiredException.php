<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Exceptions\HttpRequest;

use LogicException;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;

/**
 * @api
 */
final class BodyRequiredException extends LogicException
{
    public function __construct(
        private readonly HttpRequestInterface $request
    ) {
        parent::__construct('Body required.');
    }

    public function getRequest(): HttpRequestInterface
    {
        return $this->request;
    }
}
