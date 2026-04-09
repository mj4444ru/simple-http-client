<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\HttpRequest\Body\NoBody;
use Mj4444\SimpleHttpClient\HttpRequest\Body\StreamBody;
use Mj4444\SimpleHttpClient\HttpRequest\HttpMethod;
use Mj4444\SimpleHttpClient\HttpRequest\HttpRequestEx;

use function sprintf;

final class HttpRequestExTest extends Unit
{
    public function testGetProgressFunction(): void
    {
        // Simple test
        $request = new HttpRequestEx('https://example.com');
        self::assertNull($request->getProgressCallback());

        // Simple test
        $closure = fn(int $_a, int $_b, int $_c, int $_d): bool => true;
        $request->setProgressCallback($closure);
        self::assertSame($closure, $request->getProgressCallback());
    }

    public function testGetResourceForResponseBody(): void
    {
        // Simple test
        $request = new HttpRequestEx('https://example.com');
        self::assertNull($request->getResourceForResponseBody());

        // Simple test
        /** @var resource $resource */
        $resource = fopen('php://temp', 'rb+');
        $request->setResourceForResponseBody($resource);
        self::assertSame($resource, $request->getResourceForResponseBody());
        fclose($resource);
    }

    public function testGetResumeFrom(): void
    {
        // Prepare complex tests
        $request = new HttpRequestEx('https://example.com');

        // Complex test
        self::assertNull($request->getResumeFrom());

        // Complex test
        $request->setResourceForResponseBody(null, 500);
        self::assertSame(500, $request->getResumeFrom());
    }

    public function testGetWriteFunction(): void
    {
        // Simple test
        $request = new HttpRequestEx('https://example.com');
        self::assertNull($request->getWriteFunction());

        // Simple test
        $closure = fn(string $_data): int => strlen($_data);
        $request->setWriteFunction($closure);
        self::assertSame($closure, $request->getWriteFunction());
    }

    public function testMakeResponse(): void
    {
        // Prepare complex tests
        $request = new HttpRequestEx('https://example.com');
        $request->setExpectedContentType('text/html');

        // Complex test
        $response = $request->makeResponse(
            200,
            'https://example.com',
            'https://example.com/final',
            'https://example.com/redirect',
            ['Content-Type' => ['text/html'], 'X-Custom' => ['value']],
            'text/html',
            '<html>Response</html>'
        );
        self::assertSame(200, $response->getHttpCode());
        self::assertSame('https://example.com', $response->getUrl());
        self::assertSame('https://example.com/final', $response->getEffectiveUrl());
        self::assertSame('https://example.com/redirect', $response->getRedirectUrl());
        self::assertSame(['Content-Type' => ['text/html'], 'X-Custom' => ['value']], $response->getHeaders());
        self::assertSame('text/html', $response->getContentType());
        self::assertSame('<html>Response</html>', $response->getBody());
    }

    public function testSetBody(): void
    {
        // Simple test
        $request = new HttpRequestEx('https://example.com', null, HttpMethod::Post);
        $body = new NoBody();
        $result = $request->setBody($body);
        self::assertSame($request, $result);
        self::assertSame($body, $request->getBody());
    }

    public function testSetConnectTimeout(): void
    {
        // Simple test
        $request = new HttpRequestEx('https://example.com');
        self::assertNull($request->getConnectTimeout());

        // Simple test
        $result = $request->setConnectTimeout(5000);
        self::assertSame($request, $result);
        self::assertSame(5000, $request->getConnectTimeout());

        // Simple test
        $result = $request->setConnectTimeout(false);
        self::assertSame($request, $result);
        self::assertFalse($request->getConnectTimeout());

        // Simple test
        $result = $request->setConnectTimeout(null);
        self::assertSame($request, $result);
        self::assertNull($request->getConnectTimeout());
    }

    public function testSetLowSpeedLimit(): void
    {
        // Simple test
        $request = new HttpRequestEx('https://example.com');
        self::assertNull($request->getLowSpeedLimit());

        // Simple test
        $result = $request->setLowSpeedLimit(100);
        self::assertSame($request, $result);
        self::assertSame(100, $request->getLowSpeedLimit());

        // Simple test
        $result = $request->setLowSpeedLimit(null);
        self::assertSame($request, $result);
        self::assertNull($request->getLowSpeedLimit());
    }

    public function testSetLowSpeedTime(): void
    {
        // Simple test
        $request = new HttpRequestEx('https://example.com');
        self::assertNull($request->getLowSpeedTime());

        // Simple test
        $result = $request->setLowSpeedTime(30);
        self::assertSame($request, $result);
        self::assertSame(30, $request->getLowSpeedTime());

        // Simple test
        $result = $request->setLowSpeedTime(null);
        self::assertSame($request, $result);
        self::assertNull($request->getLowSpeedTime());
    }

    public function testSetProgressFunction(): void
    {
        // Simple test
        $request = new HttpRequestEx('https://example.com');
        $closure = fn(int $_a, int $_b, int $_c, int $_d): bool => true;
        $result = $request->setProgressCallback($closure);
        self::assertSame($request, $result);
        self::assertSame($closure, $request->getProgressCallback());

        // Simple test
        $result = $request->setProgressCallback(null);
        self::assertSame($request, $result);
        self::assertNull($request->getProgressCallback());
    }

    public function testSetResourceForResponseBody(): void
    {
        // Simple test
        $request = new HttpRequestEx('https://example.com');
        /** @var resource $resource */
        $resource = fopen('php://temp', 'rb+');
        $result = $request->setResourceForResponseBody($resource);
        self::assertSame($request, $result);
        self::assertSame($resource, $request->getResourceForResponseBody());
        self::assertNull($request->getResumeFrom());
        $request->setResourceForResponseBody(null);
        self::assertNull($request->getResourceForResponseBody());
        fclose($resource);

        // Simple test
        $result = $request->setResourceForResponseBody(null);
        self::assertSame($request, $result);
        self::assertNull($request->getResourceForResponseBody());

        // Simple test
        /** @var resource $resource */
        $resource = fopen('php://temp', 'rb+');
        $result = $request->setResourceForResponseBody($resource, 1024);
        self::assertSame($request, $result);
        self::assertSame($resource, $request->getResourceForResponseBody());
        self::assertSame(1024, $request->getResumeFrom());
        $request->setResourceForResponseBody($resource);
        self::assertSame(1024, $request->getResumeFrom());
        fclose($resource);
    }

    public function testSetResumeFrom(): void
    {
        // Simple test
        $request = new HttpRequestEx('https://example.com');
        self::assertNull($request->getResumeFrom());

        // Simple test
        $result = $request->setResumeFrom(2048);
        self::assertSame($request, $result);
        self::assertSame(2048, $request->getResumeFrom());

        // Simple test
        $result = $request->setResumeFrom(2048)->setResumeFrom(0);
        self::assertSame($request, $result);
        self::assertSame(0, $request->getResumeFrom());

        // Simple test
        $result = $request->setResumeFrom(2048)->setResumeFrom(null);
        self::assertSame($request, $result);
        self::assertNull($request->getResumeFrom());
    }

    public function testSetStreamBody(): void
    {
        // Simple test
        $request = new HttpRequestEx('https://example.com', null, HttpMethod::Post);
        /** @var resource $resource */
        $resource = fopen('php://temp', 'rb+');
        $result = $request->setStreamBody($resource);
        self::assertSame($request, $result);
        self::assertInstanceOf(StreamBody::class, $request->getBody());
        fclose($resource);
    }

    public function testSetTimeout(): void
    {
        // Simple test
        $request = new HttpRequestEx('https://example.com');
        self::assertNull($request->getTimeout());

        // Simple test
        $result = $request->setTimeout(30000);
        self::assertSame($request, $result);
        self::assertSame(30000, $request->getTimeout());

        // Simple test
        $result = $request->setTimeout(false);
        self::assertSame($request, $result);
        self::assertFalse($request->getTimeout());

        // Simple test
        $result = $request->setTimeout(null);
        self::assertSame($request, $result);
        self::assertNull($request->getTimeout());
    }

    public function testSetWriteFunction(): void
    {
        // Simple test
        $request = new HttpRequestEx('https://example.com');
        $closure = fn(string $data): int => strlen($data);
        $result = $request->setWriteFunction($closure);
        self::assertSame($request, $result);
        self::assertSame($closure, $request->getWriteFunction());

        // Simple test
        $result = $request->setWriteFunction(null);
        self::assertSame($request, $result);
        self::assertNull($request->getWriteFunction());
    }

    /**
     * @param class-string $className
     */
    private static function failException(string $className): never
    {
        self::fail(sprintf('Failed asserting that exception of type "%s" is thrown.', $className));
    }
}
