<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http;

use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;

use function count;

final class UnexpectedHttpCodeException extends HttpException
{
    /**
     * @param non-empty-array<int> $allowedCodes
     */
    public function __construct(HttpResponseInterface $response, array $allowedCodes)
    {
        if (count($allowedCodes) === 1) {
            $message = sprintf(
                'Expected http code %d, but received http code %d.',
                reset($allowedCodes),
                $response->getHttpCode()
            );
        } else {
            $message = sprintf(
                'Expected http codes [%s], but received http code %d.',
                implode(',', $allowedCodes),
                $response->getHttpCode()
            );
        }
        parent::__construct($response, $message);
    }
}
