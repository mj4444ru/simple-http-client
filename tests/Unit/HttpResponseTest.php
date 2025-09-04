<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Exceptions\Http\BadGatewayException;
use Mj4444\SimpleHttpClient\Exceptions\Http\BadRequestException;
use Mj4444\SimpleHttpClient\Exceptions\Http\ForbiddenException;
use Mj4444\SimpleHttpClient\Exceptions\Http\GatewayTimeoutException;
use Mj4444\SimpleHttpClient\Exceptions\Http\InternalServerErrorException;
use Mj4444\SimpleHttpClient\Exceptions\Http\MethodNotAllowedException;
use Mj4444\SimpleHttpClient\Exceptions\Http\NotAcceptableException;
use Mj4444\SimpleHttpClient\Exceptions\Http\NotFoundException;
use Mj4444\SimpleHttpClient\Exceptions\Http\NotImplementedException;
use Mj4444\SimpleHttpClient\Exceptions\Http\ProxyAuthenticationRequiredException;
use Mj4444\SimpleHttpClient\Exceptions\Http\ServiceUnavailableException;
use Mj4444\SimpleHttpClient\Exceptions\Http\TooManyRequestsException;
use Mj4444\SimpleHttpClient\Exceptions\Http\UnauthorizedException;
use Mj4444\SimpleHttpClient\Exceptions\Http\UnexpectedHttpCodeException;
use Mj4444\SimpleHttpClient\HttpRequest;
use Mj4444\SimpleHttpClient\HttpResponse;

/**
 * @api
 */
final class HttpResponseTest extends Unit
{
    public function testCheckHttpCodes2xx(): void
    {
        $response = $this->createResponse();
        $response->checkHttpCode();

        $response = $this->createResponse(201);
        try {
            $response->checkHttpCode();
            self::failException(UnexpectedHttpCodeException::class);
        } catch (UnexpectedHttpCodeException $e) {
            self::assertSame('Expected http code 200, but received http code 201.', $e->getMessage());
            self::assertSame($response, $e->getResponse());
        }
    }

    public function testCheckHttpCodes4xx(): void
    {
        $response = $this->createResponse(400);
        try {
            $response->checkHttpCode();
            self::failException(BadRequestException::class);
        } catch (BadRequestException $e) {
            self::assertSame('Bad Request.', $e->getMessage());
        }

        $response = $this->createResponse(401);
        try {
            $response->checkHttpCode();
            self::failException(UnauthorizedException::class);
        } catch (UnauthorizedException $e) {
            self::assertSame('Unauthorized.', $e->getMessage());
        }

        $response = $this->createResponse(403);
        try {
            $response->checkHttpCode();
            self::failException(ForbiddenException::class);
        } catch (ForbiddenException $e) {
            self::assertSame('Forbidden.', $e->getMessage());
        }

        $response = $this->createResponse(404);
        try {
            $response->checkHttpCode();
            self::failException(NotFoundException::class);
        } catch (NotFoundException $e) {
            self::assertSame('Not Found.', $e->getMessage());
        }

        $response = $this->createResponse(405);
        try {
            $response->checkHttpCode();
            self::failException(MethodNotAllowedException::class);
        } catch (MethodNotAllowedException $e) {
            self::assertSame('Method Not Allowed.', $e->getMessage());
        }

        $response = $this->createResponse(406);
        try {
            $response->checkHttpCode();
            self::failException(NotAcceptableException::class);
        } catch (NotAcceptableException $e) {
            self::assertSame('Not Acceptable.', $e->getMessage());
        }

        $response = $this->createResponse(407);
        try {
            $response->checkHttpCode();
            self::failException(ProxyAuthenticationRequiredException::class);
        } catch (ProxyAuthenticationRequiredException $e) {
            self::assertSame('Proxy Authentication Required.', $e->getMessage());
        }

        $response = $this->createResponse(429);
        try {
            $response->checkHttpCode();
            self::failException(TooManyRequestsException::class);
        } catch (TooManyRequestsException $e) {
            self::assertSame('Too Many Requests.', $e->getMessage());
        }
    }

    public function testCheckHttpCodes5xx(): void
    {
        $response = $this->createResponse(500);
        try {
            $response->checkHttpCode();
            self::failException(InternalServerErrorException::class);
        } catch (InternalServerErrorException $e) {
            self::assertSame('Internal Server Error.', $e->getMessage());
        }

        $response = $this->createResponse(501);
        try {
            $response->checkHttpCode();
            self::failException(NotImplementedException::class);
        } catch (NotImplementedException $e) {
            self::assertSame('Not Implemented.', $e->getMessage());
        }

        $response = $this->createResponse(502);
        try {
            $response->checkHttpCode();
            self::failException(BadGatewayException::class);
        } catch (BadGatewayException $e) {
            self::assertSame('Bad Gateway.', $e->getMessage());
        }

        $response = $this->createResponse(503);
        try {
            $response->checkHttpCode();
            self::failException(ServiceUnavailableException::class);
        } catch (ServiceUnavailableException $e) {
            self::assertSame('Service Unavailable.', $e->getMessage());
        }

        $response = $this->createResponse(504);
        try {
            $response->checkHttpCode();
            self::failException(GatewayTimeoutException::class);
        } catch (GatewayTimeoutException $e) {
            self::assertSame('Gateway Timeout.', $e->getMessage());
        }

        $response = $this->createResponse(599);
        try {
            $response->checkHttpCode();
            self::failException(NotAcceptableException::class);
        } catch (UnexpectedHttpCodeException $e) {
            self::assertSame('Expected http code 200, but received http code 599.', $e->getMessage());
        }
    }

    public function testGetBody(): void
    {
        $response = $this->createResponse();
        self::assertSame('<html lang="en"></html>', $response->getBody());
    }

    public function testGetContentType(): void
    {
        $response = $this->createResponse();
        self::assertSame('text/html', $response->getContentType());

        $response = $this->createResponse(contentType: '');
        self::assertSame('', $response->getContentType());

        $response = $this->createResponse(contentType: null);
        self::assertNull($response->getContentType());
    }

    public function testGetFirstHeader(): void
    {
        $response = $this->createResponse();
        self::assertNull($response->getFirstHeader('X-H'));

        $response = $this->createResponse(headers: ['X-H' => ['true', '1']]);
        self::assertSame(['X-H' => ['true', 1 => '1']], $response->getHeaders());
    }

    public function testGetHeaders(): void
    {
        $response = $this->createResponse();
        self::assertSame([], $response->getHeaders());

        $response = $this->createResponse(headers: ['X-H' => ['true', '1']]);
        self::assertSame(['X-H' => ['true', '1']], $response->getHeaders());
    }

    public function testGetHttpCode(): void
    {
        $response = $this->createResponse();
        self::assertSame(200, $response->getHttpCode());
    }

    public function testGetRequest(): void
    {
        $response = $this->createResponse();
        self::assertInstanceOf(HttpRequest::class, $response->getRequest());
    }

    /**
     * @param array<string, list<string>> $headers
     */
    private function createResponse(
        int $httpCode = 200,
        ?string $contentType = 'text/html',
        array $headers = []
    ): HttpResponse {
        $request = new HttpRequest('https://example.com');

        return new HttpResponse($request, $httpCode, $headers, $contentType, '<html lang="en"></html>');
    }

    /**
     * @param class-string $className
     */
    private static function failException(string $className): never
    {
        self::fail(sprintf('Failed asserting that exception of type "%s" is thrown.', $className));
    }
}
