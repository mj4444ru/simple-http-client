<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Contracts\HttpMethod;
use Mj4444\SimpleHttpClient\Exceptions\HttpRequest\BodyRequiredException;
use Mj4444\SimpleHttpClient\HttpRequest\Body\JsonBody;
use Mj4444\SimpleHttpClient\HttpRequest\Body\NoBody;
use Mj4444\SimpleHttpClient\HttpRequest\HttpRequest;

/**
 * @api
 */
final class HttpRequestTest extends Unit
{
    public function testAddHeader(): void
    {
        $request = (new HttpRequest('https://example.com'))
            ->addHeader('X-Test: Test');
        self::assertSame(['X-Test: Test'], $request->getHeaders());

        $request->addHeader('X-Test: Test');
        self::assertSame(['X-Test: Test', 'X-Test: Test'], $request->getHeaders());
    }

    public function testGetBody(): void
    {
        $request = (new HttpRequest('https://example.com', null, HttpMethod::Get))
            ->setBody('Test');
        self::assertNull($request->getBody());

        $request = (new HttpRequest('https://example.com', null, HttpMethod::Post))
            ->setBody('Test');
        self::assertSame('Test', $request->getBody());

        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);
        try {
            $request->getBody();
            self::failException(BodyRequiredException::class);
        } catch (BodyRequiredException) {
        }

        $request = (new HttpRequest('https://example.com', null, HttpMethod::Put))
            ->setBody('Test');
        self::assertSame('Test', $request->getBody());

        $request = new HttpRequest('https://example.com', null, HttpMethod::Put);
        try {
            $request->getBody();
            self::failException(BodyRequiredException::class);
        } catch (BodyRequiredException) {
        }

        $request = (new HttpRequest('https://example.com', null, HttpMethod::Patch))
            ->setBody('Test');
        self::assertSame('Test', $request->getBody());

        $request = new HttpRequest('https://example.com', null, HttpMethod::Patch);
        try {
            $request->getBody();
            self::failException(BodyRequiredException::class);
        } catch (BodyRequiredException) {
        }

        $request = (new HttpRequest('https://example.com', null, HttpMethod::Delete))
            ->setBody('Test');
        self::assertNull($request->getBody());
    }

    public function testGetHeaders(): void
    {
        $request = new HttpRequest('https://example.com');
        self::assertSame([], $request->getHeaders());

        $request->setHeaders([]);
        self::assertSame([], $request->getHeaders());

        $request->setHeaders(['X-Test: Test']);
        self::assertSame(['X-Test: Test'], $request->getHeaders());
    }

    public function testGetMethod(): void
    {
        $request = new HttpRequest('https://example.com');
        self::assertSame('GET', $request->getMethod());
    }

    public function testGetUrl(): void
    {
        $request = new HttpRequest('https://example.com');
        self::assertSame('https://example.com', $request->getUrl());
    }

    public function testIsResponseHeadersRequired(): void
    {
        $request = new HttpRequest('https://example.com');
        self::assertNull($request->isResponseHeadersRequired());
    }

    public function testSetAccept(): void
    {
        $request = (new HttpRequest('https://example.com'))
            ->setAccept('text/html');
        self::assertSame(['Accept: text/html'], $request->getHeaders());

        $request->setAccept('text/plain');
        self::assertSame(['Accept: text/plain'], $request->getHeaders());

        $request->setHeaders(['Accept: text/html']);
        self::assertSame(['Accept: text/html', 'Accept: text/plain'], $request->getHeaders());

        /** @psalm-suppress InvalidArgument */
        $request->setAccept('');
        self::assertSame(['Accept: text/html'], $request->getHeaders());

        $request->setAccept(null);
        self::assertSame(['Accept: text/html'], $request->getHeaders());
    }

    public function testSetBody(): void
    {
        $request = (new HttpRequest('https://example.com', null, HttpMethod::Post))
            ->setBody('');
        self::assertSame('', $request->getBody());

        $request->setBody('Test &amp; + <>');
        self::assertSame('Test &amp; + <>', $request->getBody());

        $request->setBody(' ');
        self::assertSame(' ', $request->getBody());

        $request->setBody(null);
        try {
            $request->getBody();
            self::failException(BodyRequiredException::class);
        } catch (BodyRequiredException $e) {
            self::assertSame($request, $e->getRequest());
        }
    }

    public function testSetContentType(): void
    {
        $request = (new HttpRequest('https://example.com', null))
            ->setContentType('text/html');
        self::assertSame([], $request->getHeaders());

        $request = (new HttpRequest('https://example.com', null, HttpMethod::Post))
            ->setContentType('text/html');
        self::assertSame(['Content-Type: text/html'], $request->getHeaders());

        $request->setContentType('text/plain');
        self::assertSame(['Content-Type: text/plain'], $request->getHeaders());

        $request->setHeaders(['Content-Type: text/html']);
        self::assertSame(['Content-Type: text/html', 'Content-Type: text/plain'], $request->getHeaders());

        /** @psalm-suppress InvalidArgument */
        $request->setContentType('');
        self::assertSame(['Content-Type: text/html'], $request->getHeaders());

        $request->setContentType(null);
        self::assertSame(['Content-Type: text/html'], $request->getHeaders());
    }

    public function testSetExpectedContentType(): void
    {
        $request = (new HttpRequest('https://example.com'));

        self::assertNull($request->getExpectedContentType());

        $request->setExpectedContentType('text/html');
        self::assertSame('text/html', $request->getExpectedContentType());

        $request->setExpectedContentType(['text/html', 'text/plain']);
        self::assertSame(['text/html', 'text/plain'], $request->getExpectedContentType());

        $request->setExpectedContentType(null);
        self::assertNull($request->getExpectedContentType());
    }

    public function testSetHeader(): void
    {
        $request = new HttpRequest('https://example.com');
        self::assertSame([], $request->getHeaders());

        $request->setHeader('X-Test', 'X-Test: Test');
        self::assertSame(['X-Test' => 'X-Test: Test'], $request->getHeaders());

        $request->setHeader('X-Test', 'X-Test: Test2');
        self::assertSame(['X-Test' => 'X-Test: Test2'], $request->getHeaders());

        $request->addHeader('X-Test: Test');
        $request->setHeader(0, 'X-Test: Test2');
        self::assertSame(['X-Test' => 'X-Test: Test2', 0 => 'X-Test: Test2'], $request->getHeaders());
    }

    public function testSetHeaders(): void
    {
        $request = new HttpRequest('https://example.com');
        self::assertSame([], $request->getHeaders());

        $request->setHeaders(['X-Test: Test']);
        self::assertSame(['X-Test: Test'], $request->getHeaders());

        $request->setHeaders(['X-Test' => 'X-Test: Test2']);
        self::assertSame(['X-Test' => 'X-Test: Test2'], $request->getHeaders());

        $request->setHeaders([]);
        self::assertSame([], $request->getHeaders());
    }

    public function testSetJsonBody(): void
    {
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);

        self::assertNull($request->body);

        $request->setJsonBody(null);

        /** @psalm-suppress DocblockTypeContradiction */
        self::assertInstanceOf(JsonBody::class, $request->body);

        self::assertNull($request->contentType);

        self::assertSame('null', $request->getBody());

        self::assertSame(['Content-Type: application/json; charset=utf-8'], $request->getHeaders(true));

        $request->setJsonBody(null, 'application/json')
            ->setContentType('text/html');
        self::assertSame(['Content-Type: application/json'], $request->getHeaders(true));
    }

    public function testSetMethod(): void
    {
        $request = new HttpRequest('https://example.com');
        self::assertSame('GET', $request->getMethod());

        $request->setMethod(HttpMethod::Post);
        self::assertSame('POST', $request->getMethod());

        $request->setMethod(HttpMethod::Put);
        self::assertSame('PUT', $request->getMethod());

        $request->setMethod(HttpMethod::Patch);
        self::assertSame('PATCH', $request->getMethod());

        $request->setMethod(HttpMethod::Delete);
        self::assertSame('DELETE', $request->getMethod());

        $request->setMethod(HttpMethod::Get);
        self::assertSame('GET', $request->getMethod());
    }

    public function testSetNoBody(): void
    {
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);

        self::assertNull($request->body);

        $request->setNoBody();

        /** @psalm-suppress DocblockTypeContradiction */
        self::assertInstanceOf(NoBody::class, $request->body);

        self::assertNull($request->contentType);

        self::assertSame('', $request->getBody());

        $request->setNoBody()
            ->setContentType('text/html');
        self::assertSame([], $request->getHeaders(true));
    }

    public function testSetQuery(): void
    {
        $request = new HttpRequest('https://example.com');
        self::assertSame('https://example.com', $request->getUrl());

        $request->setQuery(['param1' => '1']);
        self::assertSame('https://example.com?param1=1', $request->getUrl());

        $request->setQuery(['param1' => '1', 'param2' => '2']);
        $request = new HttpRequest('https://example.com', ['param1' => '1', 'param2' => '2']);
        self::assertSame('https://example.com?param1=1&param2=2', $request->getUrl());

        $request->setQuery(['param1' => ['1', '2']]);
        self::assertSame('https://example.com?param1%5B0%5D=1&param1%5B1%5D=2', $request->getUrl());

        $request->setQuery(['param' => '& + &amp;']);
        self::assertSame('https://example.com?param=%26+%2B+%26amp%3B', $request->getUrl());

        $request->setQuery([]);
        self::assertSame('https://example.com', $request->getUrl());

        $request->setQuery(['param1' => '1']);
        self::assertSame('https://example.com?param1=1', $request->getUrl());

        $request->setQuery(null);
        self::assertSame('https://example.com', $request->getUrl());

        $request = new HttpRequest('https://example.com?param1=1');
        self::assertSame('https://example.com?param1=1', $request->getUrl());

        $request->setQuery(['param1' => '1']);
        self::assertSame('https://example.com?param1=1&param1=1', $request->getUrl());

        $request->setQuery([]);
        self::assertSame('https://example.com?param1=1', $request->getUrl());

        $request->setQuery(null);
        self::assertSame('https://example.com?param1=1', $request->getUrl());
    }

    public function testSetResponseHeadersRequired(): void
    {
        $request = new HttpRequest('https://example.com');
        self::assertNull($request->isResponseHeadersRequired());

        $request->setResponseHeadersRequired();
        self::assertTrue($request->isResponseHeadersRequired());

        $request->setResponseHeadersRequired(false);
        self::assertFalse($request->isResponseHeadersRequired());

        /** @noinspection PhpRedundantOptionalArgumentInspection */
        $request->setResponseHeadersRequired(true);
        self::assertTrue($request->isResponseHeadersRequired());

        $request->setResponseHeadersRequired(null);
        self::assertNull($request->isResponseHeadersRequired());
    }

    public function testSetUrl(): void
    {
        $request = new HttpRequest('https://example.com', ['param1' => '1']);
        self::assertSame('https://example.com?param1=1', $request->getUrl());

        $request->setUrl('https://example.com/');
        self::assertSame('https://example.com/?param1=1', $request->getUrl());
    }

    public function testSetWwwFormUrlencodedBody(): void
    {
        $request = (new HttpRequest('https://example.com', null, HttpMethod::Post))
            ->setUrlencodedBody(['p1' => '1', 'p2' => '& + &amp;']);
        self::assertSame('p1=1&p2=%26+%2B+%26amp%3B', $request->getBody());
        self::assertSame(['Content-Type: application/x-www-form-urlencoded'], $request->getHeaders());

        $request->setHeaders([]);
        self::assertSame(['Content-Type: application/x-www-form-urlencoded'], $request->getHeaders());
    }

    public function test__construct(): void
    {
        $request = new HttpRequest('https://example.com');
        self::assertSame('https://example.com', $request->getUrl());

        $request = new HttpRequest('https://example.com', []);
        self::assertSame('https://example.com', $request->getUrl());

        $request = new HttpRequest('https://example.com', ['param1' => '1']);
        self::assertSame('https://example.com?param1=1', $request->getUrl());

        $request = new HttpRequest('https://example.com', ['param1' => '1', 'param2' => '2']);
        self::assertSame('https://example.com?param1=1&param2=2', $request->getUrl());

        $request = new HttpRequest('https://example.com', ['param1' => ['1', '2']]);
        self::assertSame('https://example.com?param1%5B0%5D=1&param1%5B1%5D=2', $request->getUrl());

        $request = new HttpRequest('https://example.com', ['param' => '& + &amp;']);
        self::assertSame('https://example.com?param=%26+%2B+%26amp%3B', $request->getUrl());

        $request = new HttpRequest('https://example.com');
        self::assertSame('GET', $request->getMethod());

        $request = new HttpRequest('https://example.com', null, HttpMethod::Get);
        self::assertSame('GET', $request->getMethod());

        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);
        self::assertSame('POST', $request->getMethod());

        $request = new HttpRequest('https://example.com', null, HttpMethod::Put);
        self::assertSame('PUT', $request->getMethod());

        $request = new HttpRequest('https://example.com', null, HttpMethod::Patch);
        self::assertSame('PATCH', $request->getMethod());

        $request = new HttpRequest('https://example.com', null, HttpMethod::Delete);
        self::assertSame('DELETE', $request->getMethod());
    }

    /**
     * @param class-string $className
     */
    private static function failException(string $className): never
    {
        self::fail(sprintf('Failed asserting that exception of type "%s" is thrown.', $className));
    }
}
