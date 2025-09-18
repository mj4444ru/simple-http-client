<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\BadGatewayException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\BadRequestException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\ForbiddenException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\GatewayTimeoutException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\InternalServerErrorException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\MethodNotAllowedException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\NotAcceptableException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\NotFoundException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\NotImplementedException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\ProxyAuthenticationRequiredException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\ServiceUnavailableException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\TooManyRequestsException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\UnauthorizedException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\UnexpectedHttpCodeException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\UnexpectedContentTypeException;
use Mj4444\SimpleHttpClient\HttpRequest\HttpRequest;
use Mj4444\SimpleHttpClient\HttpResponse\HttpResponse;

/**
 * @api
 */
final class HttpResponseTest extends Unit
{
    public function testCheckContentType(): void
    {
        // Prepare complex tests
        $response = $this->createResponse();

        // Complex test
        $response->checkContentType();

        // Simple test
        $response->checkContentType('text/html');

        // Simple test
        $response->checkContentType(['text/html']);

        // Simple test
        $response->checkContentType(['text/plain', 'text/html']);

        // Simple test
        try {
            $response->checkContentType('text/plain');
            self::failException(NotAcceptableException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }

        // Simple test
        try {
            $response->checkContentType(['text/plain']);
            self::failException(NotAcceptableException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }

        // Simple test
        try {
            $response->checkContentType(['text/plain', 'text/markdown']);
            self::failException(NotAcceptableException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }

        // Prepare complex tests
        $response = $this->createResponse(expectedContentType: ['text/plain']);

        // Complex test
        try {
            $response->checkContentType();
            self::failException(NotAcceptableException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }

        // Complex test
        try {
            $response->checkContentType();
            self::failException(NotAcceptableException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }

        // Complex test
        try {
            $response->checkContentType('text/markdown');
            self::failException(NotAcceptableException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }

        // Complex test
        try {
            $response->checkContentType();
            self::failException(NotAcceptableException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }

        // Complex test
        $response->checkContentType('text/html');

        // Complex test
        $response->checkContentType(['text/html']);

        // Complex test
        $response->checkContentType(['text/plain', 'text/html']);

        // Complex test
        $response->checkContentType();

        // Prepare complex tests
        $response = $this->createResponse(expectedContentType: ['text/plain', 'text/html']);

        // Complex test
        $response->checkContentType();

        // Complex test
        try {
            $response->checkContentType(['text/plain']);
            self::failException(NotAcceptableException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }
    }

    public function testCheckHttpCodes2xx(): void
    {
        // Simple test
        $response = $this->createResponse();
        $response->checkHttpCode();

        // Simple test
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
        // Simple test
        $response = $this->createResponse(400);
        try {
            $response->checkHttpCode();
            self::failException(BadRequestException::class);
        } catch (BadRequestException $e) {
            self::assertSame('Bad Request.', $e->getMessage());
        }

        // Simple test
        $response = $this->createResponse(401);
        try {
            $response->checkHttpCode();
            self::failException(UnauthorizedException::class);
        } catch (UnauthorizedException $e) {
            self::assertSame('Unauthorized.', $e->getMessage());
        }

        // Simple test
        $response = $this->createResponse(403);
        try {
            $response->checkHttpCode();
            self::failException(ForbiddenException::class);
        } catch (ForbiddenException $e) {
            self::assertSame('Forbidden.', $e->getMessage());
        }

        // Simple test
        $response = $this->createResponse(404);
        try {
            $response->checkHttpCode();
            self::failException(NotFoundException::class);
        } catch (NotFoundException $e) {
            self::assertSame('Not Found.', $e->getMessage());
        }

        // Simple test
        $response = $this->createResponse(405);
        try {
            $response->checkHttpCode();
            self::failException(MethodNotAllowedException::class);
        } catch (MethodNotAllowedException $e) {
            self::assertSame('Method Not Allowed.', $e->getMessage());
        }

        // Simple test
        $response = $this->createResponse(406);
        try {
            $response->checkHttpCode();
            self::failException(NotAcceptableException::class);
        } catch (NotAcceptableException $e) {
            self::assertSame('Not Acceptable.', $e->getMessage());
        }

        // Simple test
        $response = $this->createResponse(407);
        try {
            $response->checkHttpCode();
            self::failException(ProxyAuthenticationRequiredException::class);
        } catch (ProxyAuthenticationRequiredException $e) {
            self::assertSame('Proxy Authentication Required.', $e->getMessage());
        }

        // Simple test
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
        // Simple test
        $response = $this->createResponse(500);
        try {
            $response->checkHttpCode();
            self::failException(InternalServerErrorException::class);
        } catch (InternalServerErrorException $e) {
            self::assertSame('Internal Server Error.', $e->getMessage());
        }

        // Simple test
        $response = $this->createResponse(501);
        try {
            $response->checkHttpCode();
            self::failException(NotImplementedException::class);
        } catch (NotImplementedException $e) {
            self::assertSame('Not Implemented.', $e->getMessage());
        }

        // Simple test
        $response = $this->createResponse(502);
        try {
            $response->checkHttpCode();
            self::failException(BadGatewayException::class);
        } catch (BadGatewayException $e) {
            self::assertSame('Bad Gateway.', $e->getMessage());
        }

        // Simple test
        $response = $this->createResponse(503);
        try {
            $response->checkHttpCode();
            self::failException(ServiceUnavailableException::class);
        } catch (ServiceUnavailableException $e) {
            self::assertSame('Service Unavailable.', $e->getMessage());
        }

        // Simple test
        $response = $this->createResponse(504);
        try {
            $response->checkHttpCode();
            self::failException(GatewayTimeoutException::class);
        } catch (GatewayTimeoutException $e) {
            self::assertSame('Gateway Timeout.', $e->getMessage());
        }

        // Simple test
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
        // Simple test
        $response = $this->createResponse();
        self::assertSame('<html lang="en"></html>', $response->getBody());
    }

    public function testGetContentType(): void
    {
        // Simple test
        $response = $this->createResponse();
        self::assertSame('text/html', $response->getContentType());

        // Simple test
        $response = $this->createResponse(contentType: '');
        self::assertSame('', $response->getContentType());

        // Simple test
        $response = $this->createResponse(contentType: null);
        self::assertNull($response->getContentType());
    }

    public function testGetEffectiveUrl(): void
    {
        // Simple test
        $response = $this->createResponse();
        self::assertSame('https://example.com', $response->getEffectiveUrl());
    }

    public function testGetFirstHeader(): void
    {
        // Simple test
        $response = $this->createResponse();
        self::assertNull($response->getFirstHeader('X-H'));

        // Simple test
        $response = $this->createResponse(headers: ['X-H' => ['true', '1']]);
        self::assertSame(['X-H' => ['true', 1 => '1']], $response->getHeaders());
    }

    public function testGetHeaders(): void
    {
        // Simple test
        $response = $this->createResponse();
        self::assertSame([], $response->getHeaders());

        // Simple test
        $response = $this->createResponse(headers: ['X-H' => ['true', '1']]);
        self::assertSame(['X-H' => ['true', '1']], $response->getHeaders());
    }

    public function testGetHttpCode(): void
    {
        // Simple test
        $response = $this->createResponse();
        self::assertSame(200, $response->getHttpCode());
    }

    public function testGetRedirectUrl(): void
    {
        // Simple test
        $response = $this->createResponse();
        self::assertNull($response->getRedirectUrl());

        // Simple test
        $response = $this->createResponse(redirectUrl: 'http://example.com/redirect');
        self::assertSame('http://example.com/redirect', $response->getRedirectUrl());
    }

    public function testGetRequest(): void
    {
        // Simple test
        $response = $this->createResponse();
        self::assertInstanceOf(HttpRequest::class, $response->getRequest());
    }

    public function testGetUrl(): void
    {
        // Simple test
        $response = $this->createResponse();
        self::assertSame('https://example.com', $response->getUrl());
    }

    /**
     * @param array<string, list<string>> $headers
     * @param lowercase-string|non-empty-array<lowercase-string|null>|null $expectedContentType
     */
    private function createResponse(
        int $httpCode = 200,
        ?string $contentType = 'text/html',
        array $headers = [],
        string|array|null $expectedContentType = null,
        ?string $redirectUrl = null
    ): HttpResponse {
        $request = new HttpRequest('https://example.com');
        $url = $request->getUrl();

        if ($expectedContentType !== null) {
            $request->setExpectedContentType($expectedContentType);
        }

        return $request->makeResponse(
            $httpCode,
            $url,
            $url,
            $redirectUrl,
            $headers,
            $contentType,
            '<html lang="en"></html>'
        );
    }

    /**
     * @param class-string $className
     */
    private static function failException(string $className): never
    {
        self::fail(sprintf('Failed asserting that exception of type "%s" is thrown.', $className));
    }
}
