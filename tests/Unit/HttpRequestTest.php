<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\NotAcceptableException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\UnexpectedContentTypeException;
use Mj4444\SimpleHttpClient\HttpRequest\Body\FileBody;
use Mj4444\SimpleHttpClient\HttpRequest\Body\JsonBody;
use Mj4444\SimpleHttpClient\HttpRequest\Body\MultipartBody\File;
use Mj4444\SimpleHttpClient\HttpRequest\Body\MultipartBody\StringFile;
use Mj4444\SimpleHttpClient\HttpRequest\Body\MultipartFormBody;
use Mj4444\SimpleHttpClient\HttpRequest\Body\NoBody;
use Mj4444\SimpleHttpClient\HttpRequest\Body\StreamBody;
use Mj4444\SimpleHttpClient\HttpRequest\Body\StringBody;
use Mj4444\SimpleHttpClient\HttpRequest\Body\UrlencodedBody;
use Mj4444\SimpleHttpClient\HttpRequest\HttpMethod;
use Mj4444\SimpleHttpClient\HttpRequest\HttpRequest;
use Mj4444\SimpleHttpClient\HttpResponse\BaseHttpResponse;

use function sprintf;

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

    public function testConstructor(): void
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

    public function testGetBody(): void
    {
        // Simple test
        $request = new HttpRequest('https://example.com', null, HttpMethod::Get);
        self::assertNull($request->getBody());

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

    public function testIsPost(): void
    {
        // Simple test
        $request = new HttpRequest('https://example.com', null, HttpMethod::Get);
        self::assertFalse($request->isPost());

        // Simple test
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);
        self::assertTrue($request->isPost());

        // Simple test
        $request = new HttpRequest('https://example.com', null, HttpMethod::Put);
        self::assertTrue($request->isPost());

        // Simple test
        $request = new HttpRequest('https://example.com', null, HttpMethod::Patch);
        self::assertTrue($request->isPost());

        // Simple test
        $request = new HttpRequest('https://example.com', null, HttpMethod::Delete);
        self::assertFalse($request->isPost());
    }

    public function testIsResponseHeadersRequired(): void
    {
        // Simple test
        $request = new HttpRequest('https://example.com');
        self::assertNull($request->isResponseHeadersRequired());
    }

    public function testMakeResponse(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com');
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
        $request->setBody(null);
        self::assertNull($request->getBody());

        // Simple test
        $body = new NoBody();
        self::assertSame($body, $request->setBody($body)->getBody());
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
        self::assertNull($response->expectedContentType);

        // Simple test
        $response = $this->createResponse(expectedContentType: 'text/html');
        self::assertSame('text/html', $response->expectedContentType);

        // Simple test
        $response = $this->createResponse(expectedContentType: ['text/html']);
        self::assertSame(['text/html'], $response->expectedContentType);

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

    public function testSetFileBody(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);

        // Complex test
        $body = $request->setFileBody('/path/to/file.txt')->getBody();
        self::assertInstanceOf(FileBody::class, $body);
        self::assertNull($body->getContentType());

        // Complex test
        $body = $request->setFileBody('/path/to/file.json', 'application/json')->getBody();
        self::assertInstanceOf(FileBody::class, $body);
        self::assertEquals('application/json', $body->getContentType());
    }

    public function testSetFollowLocation(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com');

        // Complex test
        self::assertNull($request->followLocation);
        self::assertNull($request->isFollowLocation());

        // Complex test
        $request->setFollowLocation(true);
        self::assertTrue($request->followLocation);
        self::assertTrue($request->isFollowLocation());

        // Complex test
        $request->setFollowLocation(false);
        self::assertFalse($request->followLocation);
        self::assertFalse($request->isFollowLocation());

        // Complex test
        $request->setFollowLocation(null);
        self::assertNull($request->followLocation);
        self::assertNull($request->isFollowLocation());
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
        self::assertNull($request->getBody());

        // Complex test
        $body = $request->setJsonBody(null)->getBody();
        self::assertInstanceOf(JsonBody::class, $body);
        self::assertSame('null', $body->getBody());
        self::assertSame('application/json; charset=utf-8', $body->getContentType());

        // Complex test
        $body = $request->setJsonBody(['foo' => 'bar'])->getBody();
        self::assertInstanceOf(JsonBody::class, $body);
        self::assertSame('{"foo":"bar"}', $body->getBody());
    }

    public function testSetMaxRedirects(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com');

        // Complex test
        self::assertNull($request->maxRedirects);

        // Complex test
        $request->setMaxRedirects(5);
        self::assertSame(5, $request->maxRedirects);

        // Complex test
        $request->setMaxRedirects(-1);
        self::assertSame(-1, $request->maxRedirects);

        // Complex test
        $request->setMaxRedirects(null);
        self::assertNull($request->maxRedirects);
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

    public function testSetMultipartFormBody(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);

        // Complex test
        $file1 = new File('file.txt', 'file.txt', 'text/plain');
        $file2 = new StringFile('Content', 'file.txt', 'text/plain');
        $request = $request->setMultipartFormBody([
            'field' => 'value',
            'int' => 5,
            'file1' => $file1,
            'file2' => $file2,
        ]);
        $body = $request->getBody();
        self::assertInstanceOf(MultipartFormBody::class, $body);
        self::assertNull($body->getContentType());
        self::assertSame([
            'field' => 'value',
            'int' => 5,
            'file1' => $file1,
            'file2' => $file2,
        ], $body->getBody()->getFields());
    }

    public function testSetNoBody(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);

        // Complex test
        self::assertNull($request->body);

        // Complex test
        $body = $request->setNoBody()->getBody();
        self::assertInstanceOf(NoBody::class, $body);
        self::assertEquals('', $body->getBody());
        self::assertNull($body->getContentType());
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

    public function testSetReferer(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com');

        // Complex test
        $request->setReferer('https://referer.com');
        self::assertSame(['referer' => 'Referer: https://referer.com'], $request->getHeaders());

        // Complex test
        $refererRequest = new HttpRequest('https://referer-request.com');
        /** @psalm-suppress InvalidArgument */
        $request->setReferer($refererRequest);
        self::assertSame(['referer' => 'Referer: https://referer-request.com'], $request->getHeaders());
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

    public function testSetStreamBody(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);

        // Complex test
        /** @var resource $resource */
        $resource = fopen('php://temp', 'rb+');
        $body = $request->setStreamBody($resource)->getBody();
        self::assertInstanceOf(StreamBody::class, $body);
        self::assertNull($body->getContentType());
        self::assertNull($body->offset);
        self::assertNull($body->length);
        fclose($resource);

        // Complex test
        /** @var resource $resource */
        $resource = fopen('php://temp', 'rb+');
        $body = $request->setStreamBody($resource, 'application/octet-stream')->getBody();
        self::assertInstanceOf(StreamBody::class, $body);
        self::assertEquals('application/octet-stream', $body->getContentType());
        self::assertNull($body->offset);
        self::assertNull($body->length);
        fclose($resource);

        // Complex test
        /** @var resource $resource */
        $resource = fopen('php://temp', 'rb+');
        $body = $request->setStreamBody($resource, 'application/octet-stream', 0, 100)->getBody();
        self::assertInstanceOf(StreamBody::class, $body);
        self::assertEquals('application/octet-stream', $body->getContentType());
        self::assertEquals(0, $body->offset);
        self::assertEquals(100, $body->length);
        fclose($resource);
    }

    public function testSetStringBody(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);

        // Complex test
        $body = $request->setStringBody('test content')->getBody();
        self::assertInstanceOf(StringBody::class, $body);
        self::assertSame('test content', $body->getBody());
        self::assertNull($body->getContentType());

        // Complex test
        $body = $request->setStringBody('test content', 'text/plain')->getBody();
        self::assertInstanceOf(StringBody::class, $body);
        self::assertSame('test content', $body->getBody());
        self::assertEquals('text/plain', $body->getContentType());
    }

    public function testSetStringStreamBody(): void
    {
        // Prepare complex tests
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);

        // Complex test
        /** @var resource $resource */
        $resource = fopen('php://temp', 'rb+');
        $body = $request->setStringStreamBody($resource)->getBody();
        self::assertInstanceOf(StreamBody::class, $body);
        self::assertNull($body->getContentType());
        self::assertNull($body->offset);
        self::assertNull($body->length);
        fclose($resource);

        // Complex test
        /** @var resource $resource */
        $resource = fopen('php://temp', 'rb+');
        $body = $request->setStringStreamBody($resource, 'application/octet-stream')->getBody();
        self::assertInstanceOf(StreamBody::class, $body);
        self::assertEquals('application/octet-stream', $body->getContentType());
        self::assertNull($body->offset);
        self::assertNull($body->length);
        fclose($resource);

        // Complex test
        /** @var resource $resource */
        $resource = fopen('php://temp', 'rb+');
        $body = $request->setStringStreamBody($resource, 'application/octet-stream', 0, 100)->getBody();
        self::assertInstanceOf(StreamBody::class, $body);
        self::assertEquals('application/octet-stream', $body->getContentType());
        self::assertEquals(0, $body->offset);
        self::assertEquals(100, $body->length);
        fclose($resource);
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
        $body = $request->setUrlencodedBody([])
            ->setUrlencodedBody(['p1' => '1', 'p2' => '& + &amp;'])
            ->getBody();
        self::assertInstanceOf(UrlencodedBody::class, $body);
        self::assertSame('p1=1&p2=%26+%2B+%26amp%3B', $body->getBody());
        self::assertSame('application/x-www-form-urlencoded', $body->getContentType());
    }

    /**
     * @param lowercase-string|non-empty-array<lowercase-string|null>|null $expectedContentType
     */
    private function createResponse(string|array|null $expectedContentType = null): BaseHttpResponse
    {
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
