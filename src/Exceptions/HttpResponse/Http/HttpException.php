<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http;

use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\HttpResponseException;

/**
 * Base exception for all Http errors.
 */
abstract class HttpException extends HttpResponseException
{
    public function __construct(HttpResponseInterface $response, string $message)
    {
        parent::__construct($message, $response->getHttpCode(), $response);
    }

    /**
     * @param non-empty-array<int> $allowedCodes
     * @throws HttpException
     */
    final public static function throw(HttpResponseInterface $response, array $allowedCodes): never
    {
        throw match ($response->getHttpCode()) {
            400 => new BadRequestException($response),
            401 => new UnauthorizedException($response),
            403 => new ForbiddenException($response),
            404 => new NotFoundException($response),
            405 => new MethodNotAllowedException($response),
            406 => new NotAcceptableException($response),
            407 => new ProxyAuthenticationRequiredException($response),
            429 => new TooManyRequestsException($response),
            500 => new InternalServerErrorException($response),
            501 => new NotImplementedException($response),
            502 => new BadGatewayException($response),
            503 => new ServiceUnavailableException($response),
            504 => new GatewayTimeoutException($response),
            default => new UnexpectedHttpCodeException($response, $allowedCodes),
        };
    }
}
