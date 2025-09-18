<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\NotAcceptableException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\UnexpectedContentTypeException;
use Mj4444\SimpleHttpClient\HttpRequest\Body\JsonBody;
use Mj4444\SimpleHttpClient\HttpRequest\Body\NoBody;
use Mj4444\SimpleHttpClient\HttpRequest\HttpMethod;
use Mj4444\SimpleHttpClient\HttpRequest\HttpRequest;
use Mj4444\SimpleHttpClient\HttpResponse\BaseHttpResponse;

/**
 * @api
 */
final class HttpRequestTest extends Unit
{
    public function testAddHeader(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com');

        // Complex test
        $request->addHeader('X-Test: Test');
        self::assertSame(['X-Test: Test'], $request->getHeaders());

        // Complex test
        $request->addHeader('X-Test: Test');
        self::assertSame(['X-Test: Test', 'X-Test: Test'], $request->getHeaders());
    }

    public function testGetBody(): void
    {
        // Simple test
        $request = new HttpRequest('https://example.com', null, HttpMethod::Get);
        self::assertNull($request->getBody());

        // Simple test
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);
        $request->setBody('Test');
        self::assertSame('Test', $request->getBody());

        // Simple test
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);
        self::assertNull($request->getBody());
    }

    public function testGetHeaders(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com');

        // Complex test
        self::assertSame([], $request->getHeaders());

        // Complex test
        $request->setHeaders([]);
        self::assertSame([], $request->getHeaders());

        // Complex test
        $request->setHeaders(['X-Test: Test']);
        self::assertSame(['X-Test: Test'], $request->getHeaders());
    }

    public function testGetMethod(): void
    {
        // Simple test
        $request = new HttpRequest('https://example.com');
        self::assertSame('GET', $request->getMethod());
    }

    public function testGetUrl(): void
    {
        // Simple test
        $request = new HttpRequest('https://example.com');
        self::assertSame('https://example.com', $request->getUrl());
    }

    public function testIsResponseHeadersRequired(): void
    {
        // Simple test
        $request = new HttpRequest('https://example.com');
        self::assertNull($request->isResponseHeadersRequired());
    }

    public function testSetAccept(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com');

        // Complex test
        $request->setAccept('text/html');
        self::assertSame(['Accept: text/html'], $request->getHeaders());

        // Complex test
        $request->setAccept('text/plain');
        self::assertSame(['Accept: text/plain'], $request->getHeaders());

        // Complex test
        $request->setHeaders(['Accept: text/html']);
        self::assertSame(['Accept: text/html', 'Accept: text/plain'], $request->getHeaders());

        // Complex test
        /** @psalm-suppress InvalidArgument */
        $request->setAccept('');
        self::assertSame(['Accept: text/html'], $request->getHeaders());

        // Complex test
        $request->setAccept(null);
        self::assertSame(['Accept: text/html'], $request->getHeaders());
    }

    public function testSetBody(): void
    {
        // Prepare tests
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);

        // Simple test
        $request->setBody('');
        self::assertSame('', $request->getBody());

        // Simple test
        $request->setBody('Test &amp; + <>');
        self::assertSame('Test &amp; + <>', $request->getBody());

        // Simple test
        $request->setBody(' ');
        self::assertSame(' ', $request->getBody());

        // Simple test
        $request->setBody(null);
        self::assertNull($request->getBody());

        $request->setBody('test');
        $request->setBody(new NoBody());
        self::assertSame('', $request->getBody());
    }

    public function testSetContentType(): void
    {
        // Simple test
        $request = new HttpRequest('https://example.com', null);
        $request->setContentType('text/html');
        self::assertSame([], $request->getHeaders());

        // Prepare complex tests
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);

        // Complex test
        $request->setContentType('text/html');
        self::assertSame(['Content-Type: text/html'], $request->getHeaders());

        // Complex test
        $request->setContentType('text/plain');
        self::assertSame(['Content-Type: text/plain'], $request->getHeaders());

        // Complex test
        $request->setHeaders(['Content-Type: text/html']);
        self::assertSame(['Content-Type: text/html', 'Content-Type: text/plain'], $request->getHeaders());

        // Complex test
        /** @psalm-suppress InvalidArgument */
        $request->setContentType('');
        self::assertSame(['Content-Type: text/html'], $request->getHeaders());

        // Complex test
        $request->setContentType(null);
        self::assertSame(['Content-Type: text/html'], $request->getHeaders());
    }

    public function testSetExpectedContentType(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com');

        // Complex test
        self::assertNull($request->expectedContentType);

        // Complex test
        $request->setExpectedContentType('text/html');
        self::assertSame('text/html', $request->expectedContentType);

        // Complex test
        $request->setExpectedContentType(['text/html', 'text/plain']);
        self::assertSame(['text/html', 'text/plain'], $request->expectedContentType);

        // Complex test
        $request->setExpectedContentType(null);
        self::assertNull($request->expectedContentType);

        // Simple test
        $response = $this->createResponse();
        $response->checkContentType();

        // Simple test
        $response = $this->createResponse(expectedContentType: 'text/html');
        $response->checkContentType();

        // Simple test
        $response = $this->createResponse(expectedContentType: ['text/html']);
        $response->checkContentType();

        // Simple test
        $response = $this->createResponse(expectedContentType: ['text/html']);
        $response->checkContentType();

        // Simple test
        $response = $this->createResponse(expectedContentType: ['text/plain', 'text/html']);
        $response->checkContentType();

        // Simple test
        /** @psalm-suppress InvalidArgument */
        $response = $this->createResponse(expectedContentType: []);
        try {
            $response->checkContentType();
            self::failException(NotAcceptableException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }

        // Simple test
        $response = $this->createResponse(expectedContentType: 'text/plain');
        try {
            $response->checkContentType();
            self::failException(NotAcceptableException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }

        // Simple test
        $response = $this->createResponse(expectedContentType: ['text/plain']);
        try {
            $response->checkContentType();
            self::failException(NotAcceptableException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }
    }

    public function testSetHeader(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com');

        // Complex test
        self::assertSame([], $request->getHeaders());

        // Complex test
        $request->setHeader('X-Test', 'X-Test: Test');
        self::assertSame(['X-Test' => 'X-Test: Test'], $request->getHeaders());

        // Complex test
        $request->setHeader('X-Test', 'X-Test: Test2');
        self::assertSame(['X-Test' => 'X-Test: Test2'], $request->getHeaders());

        // Complex test
        $request->addHeader('X-Test: Test');
        $request->setHeader(0, 'X-Test: Test2');
        self::assertSame(['X-Test' => 'X-Test: Test2', 0 => 'X-Test: Test2'], $request->getHeaders());
    }

    public function testSetHeaders(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com');

        // Complex test
        self::assertSame([], $request->getHeaders());

        // Complex test
        $request->setHeaders(['X-Test: Test']);
        self::assertSame(['X-Test: Test'], $request->getHeaders());

        // Complex test
        $request->setHeaders(['X-Test' => 'X-Test: Test2']);
        self::assertSame(['X-Test' => 'X-Test: Test2'], $request->getHeaders());

        // Complex test
        $request->setHeaders([]);
        self::assertSame([], $request->getHeaders());
    }

    public function testSetJsonBody(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);

        // Complex test
        self::assertNull($request->body);

        // Complex test
        $request->setJsonBody(null);
        /** @psalm-suppress DocblockTypeContradiction */
        self::assertInstanceOf(JsonBody::class, $request->body);
        self::assertNull($request->contentType);
        self::assertSame('null', $request->getBody());
        self::assertSame(['Content-Type: application/json; charset=utf-8'], $request->getHeaders());

        // Complex test
        $request->setJsonBody(null, 'application/json')
            ->setContentType('text/html');
        self::assertSame(['Content-Type: application/json'], $request->getHeaders());
    }

    public function testSetMethod(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com');

        // Complex test
        self::assertSame('GET', $request->getMethod());

        // Complex test
        $request->setMethod(HttpMethod::Post);
        self::assertSame('POST', $request->getMethod());

        // Complex test
        $request->setMethod(HttpMethod::Put);
        self::assertSame('PUT', $request->getMethod());

        // Complex test
        $request->setMethod(HttpMethod::Patch);
        self::assertSame('PATCH', $request->getMethod());

        // Complex test
        $request->setMethod(HttpMethod::Delete);
        self::assertSame('DELETE', $request->getMethod());

        // Complex test
        $request->setMethod(HttpMethod::Get);
        self::assertSame('GET', $request->getMethod());
    }

    public function testSetNoBody(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);

        // Complex test
        self::assertNull($request->body);

        // Complex test
        $request->setNoBody();
        /** @psalm-suppress DocblockTypeContradiction */
        self::assertInstanceOf(NoBody::class, $request->body);
        self::assertNull($request->contentType);
        self::assertSame('', $request->getBody());

        // Complex test
        $request->setNoBody()
            ->setContentType('text/html');
        self::assertSame([], $request->getHeaders());
    }

    public function testSetQuery(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com');

        // Complex test
        self::assertSame('https://example.com', $request->getUrl());

        // Complex test
        $request->setQuery(['param1' => '1']);
        self::assertSame('https://example.com?param1=1', $request->getUrl());

        // Complex test
        $request->setQuery(['param1' => '1', 'param2' => '2']);
        self::assertSame('https://example.com?param1=1&param2=2', $request->getUrl());

        // Complex test
        $request->setQuery(['param1' => ['1', '2']]);
        self::assertSame('https://example.com?param1%5B0%5D=1&param1%5B1%5D=2', $request->getUrl());

        // Complex test
        $request->setQuery(['param' => '& + &amp;']);
        self::assertSame('https://example.com?param=%26+%2B+%26amp%3B', $request->getUrl());

        // Complex test
        $request->setQuery([]);
        self::assertSame('https://example.com', $request->getUrl());

        // Complex test
        $request->setQuery(['param1' => '1']);
        self::assertSame('https://example.com?param1=1', $request->getUrl());

        // Complex test
        $request->setQuery(null);
        self::assertSame('https://example.com', $request->getUrl());

        // Prepare complex tests
        $request = new HttpRequest('https://example.com?param1=1');

        // Complex test
        self::assertSame('https://example.com?param1=1', $request->getUrl());

        // Complex test
        $request->setQuery(['param1' => '1']);
        self::assertSame('https://example.com?param1=1&param1=1', $request->getUrl());

        // Complex test
        $request->setQuery([]);
        self::assertSame('https://example.com?param1=1', $request->getUrl());

        // Complex test
        $request->setQuery(null);
        self::assertSame('https://example.com?param1=1', $request->getUrl());
    }

    public function testSetResponseHeadersRequired(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com');

        // Complex test
        self::assertNull($request->isResponseHeadersRequired());

        // Complex test
        $request->setResponseHeadersRequired();
        self::assertTrue($request->isResponseHeadersRequired());

        // Complex test
        $request->setResponseHeadersRequired(false);
        self::assertFalse($request->isResponseHeadersRequired());

        // Complex test
        /** @noinspection PhpRedundantOptionalArgumentInspection */
        $request->setResponseHeadersRequired(true);
        self::assertTrue($request->isResponseHeadersRequired());

        // Complex test
        $request->setResponseHeadersRequired(null);
        self::assertNull($request->isResponseHeadersRequired());
    }

    public function testSetUrl(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com', ['param1' => '1']);

        // Complex test
        self::assertSame('https://example.com?param1=1', $request->getUrl());

        // Complex test
        $request->setUrl('https://example.com/');
        self::assertSame('https://example.com/?param1=1', $request->getUrl());
    }

    public function testSetWwwFormUrlencodedBody(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);

        // Complex test
        $request->setUrlencodedBody([])
            ->setUrlencodedBody(['p1' => '1', 'p2' => '& + &amp;']);
        self::assertSame('p1=1&p2=%26+%2B+%26amp%3B', $request->getBody());
        self::assertSame(['Content-Type: application/x-www-form-urlencoded'], $request->getHeaders());

        // Complex test
        $request->setHeaders([]);
        self::assertSame(['Content-Type: application/x-www-form-urlencoded'], $request->getHeaders());
    }

    public function test__construct(): void
    {
        // Simple test
        $request = new HttpRequest('https://example.com');
        self::assertSame('https://example.com', $request->getUrl());

        // Simple test
        $request = new HttpRequest('https://example.com', []);
        self::assertSame('https://example.com', $request->getUrl());

        // Simple test
        $request = new HttpRequest('https://example.com', ['param1' => '1']);
        self::assertSame('https://example.com?param1=1', $request->getUrl());

        // Simple test
        $request = new HttpRequest('https://example.com', ['param1' => '1', 'param2' => '2']);
        self::assertSame('https://example.com?param1=1&param2=2', $request->getUrl());

        // Simple test
        $request = new HttpRequest('https://example.com', ['param1' => ['1', '2']]);
        self::assertSame('https://example.com?param1%5B0%5D=1&param1%5B1%5D=2', $request->getUrl());

        // Simple test
        $request = new HttpRequest('https://example.com', ['param' => '& + &amp;']);
        self::assertSame('https://example.com?param=%26+%2B+%26amp%3B', $request->getUrl());

        // Simple test
        $request = new HttpRequest('https://example.com');
        self::assertSame('GET', $request->getMethod());

        // Simple test
        $request = new HttpRequest('https://example.com', null, HttpMethod::Get);
        self::assertSame('GET', $request->getMethod());

        // Simple test
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);
        self::assertSame('POST', $request->getMethod());

        // Simple test
        $request = new HttpRequest('https://example.com', null, HttpMethod::Put);
        self::assertSame('PUT', $request->getMethod());

        // Simple test
        $request = new HttpRequest('https://example.com', null, HttpMethod::Patch);
        self::assertSame('PATCH', $request->getMethod());

        // Simple test
        $request = new HttpRequest('https://example.com', null, HttpMethod::Delete);
        self::assertSame('DELETE', $request->getMethod());
    }

    /**
     * @param lowercase-string|non-empty-array<lowercase-string|null>|null $expectedContentType
     */
    private function createResponse(
        string|array|null $expectedContentType = null
    ): BaseHttpResponse {
        $request = new HttpRequest('https://example.com');
        $url = $request->getUrl();

        if ($expectedContentType !== null) {
            $request->setExpectedContentType($expectedContentType);
        }

        return $request->makeResponse(200, $url, $url, null, [], 'text/html', '<html lang="en"></html>');
    }

    /**
     * @param class-string $className
     */
    private static function failException(string $className): never
    {
        self::fail(sprintf('Failed asserting that exception of type "%s" is thrown.', $className));
    }
}
