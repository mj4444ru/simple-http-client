<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Contracts\HttpClientInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\Exceptions\HttpRequest\JsonEncodeExceptionHttp;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\InternalServerErrorException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\NotFoundException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\Http\UnexpectedHttpCodeException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\JsonDecodeException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\UnexpectedContentTypeException;
use Mj4444\SimpleHttpClient\HttpRequest\Body\NoBody;
use Mj4444\SimpleHttpClient\HttpRequest\Body\UrlencodedBody;
use Mj4444\SimpleHttpClient\JsonHttpClient;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;

/**
 * @api
 */
final class JsonHttpClientTest extends Unit
{
    /**
     * @psalm-suppress MixedAssignment
     */
    public function testAddHeader(): void
    {
        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->addHeader('X-H: Test; addHeader');
        $response = $client->get('https://example.com', null, ['X-H: Test']);
        self::assertSame([
            'GET',
            'https://example.com',
            ['X-H: Test; addHeader', 'X-H: Test', 'Accept: application/json; charset=utf-8'],
            null,
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->addHeader('X-H: Test; addHeader')
            ->addHeader('X-H: Test; addHeader');
        $response = $client->get('https://example.com', null, ['X-H: Test']);
        self::assertSame([
            'GET',
            'https://example.com',
            ['X-H: Test; addHeader', 'X-H: Test; addHeader', 'X-H: Test', 'Accept: application/json; charset=utf-8'],
            null,
        ], $response);

        /** @psalm-suppress InvalidArgument */
        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->addHeader('');
        $response = $client->get('https://example.com', null, ['X-H: Test']);
        self::assertSame([
            'GET',
            'https://example.com',
            [1 => 'X-H: Test', 2 => 'Accept: application/json; charset=utf-8'],
            null,
        ], $response);
    }

    public function testBadJsonRequest(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $client = new JsonHttpClient($this->createMockHttpClient());
        $body = new stdClass();
        $body->ref = $body;
        try {
            $client->post('https://example.com', $body);
            self::failException(JsonEncodeExceptionHttp::class);
        } catch (JsonEncodeExceptionHttp $e) {
            self::assertSame('Recursion detected', $e->getMessage());
            self::assertSame($body, $e->getData());
        } finally {
            unset($body->ref, $body);
        }
    }

    public function testBadJsonResponse(): void
    {
        $client = new JsonHttpClient($this->createMockHttpClientWithHtml('application/json; charset=utf-8'));
        try {
            $client->get('https://example.com');
            self::failException(JsonDecodeException::class);
        } catch (JsonDecodeException $e) {
            self::assertSame('Not valid json.', $e->getMessage());
        }
    }

    public function testDelete(): void
    {
        $client = new JsonHttpClient($this->createMockHttpClient());
        $response = $client->delete('https://example.com', ['param1' => 1], ['X-H: Test']);
        self::assertSame([
            'DELETE',
            'https://example.com?param1=1',
            ['X-H: Test', 'Accept: application/json; charset=utf-8'],
            null,
        ], $response);
    }

    public function testGet(): void
    {
        $client = new JsonHttpClient($this->createMockHttpClient());
        $response = $client->get('https://example.com', ['param1' => 1], ['X-H: Test']);
        self::assertSame([
            'GET',
            'https://example.com?param1=1',
            ['X-H: Test', 'Accept: application/json; charset=utf-8'],
            null,
        ], $response);
    }

    public function testHtmlResponse(): void
    {
        $client = new JsonHttpClient($this->createMockHttpClientWithHtml());
        try {
            $client->get('https://example.com');
            self::failException(UnexpectedContentTypeException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }
    }

    public function testHttpExceptions(): void
    {
        $client = new JsonHttpClient($this->createMockHttpClient(404));
        try {
            $client->get('https://example.com');
            self::failException(NotFoundException::class);
        } catch (NotFoundException $e) {
            self::assertSame('Not Found.', $e->getMessage());
        }

        $client = new JsonHttpClient($this->createMockHttpClient(500));
        try {
            $client->get('https://example.com');
            self::failException(InternalServerErrorException::class);
        } catch (InternalServerErrorException $e) {
            self::assertSame('Internal Server Error.', $e->getMessage());
        }
    }

    public function testPatch(): void
    {
        $client = new JsonHttpClient($this->createMockHttpClient());
        $response = $client->patch('https://example.com', ['test' => true], ['param1' => 1], ['X-H: Test']);
        self::assertSame([
            'PATCH',
            'https://example.com?param1=1',
            ['X-H: Test', 'Accept: application/json; charset=utf-8', 'Content-Type: application/json; charset=utf-8'],
            '{"test":true}',
        ], $response);
    }

    public function testPost(): void
    {
        $client = new JsonHttpClient($this->createMockHttpClient());
        $response = $client->post('https://example.com', ['test' => true], ['param1' => 1], ['X-H: Test']);
        self::assertSame([
            'POST',
            'https://example.com?param1=1',
            ['X-H: Test', 'Accept: application/json; charset=utf-8', 'Content-Type: application/json; charset=utf-8'],
            '{"test":true}',
        ], $response);
    }

    public function testPut(): void
    {
        $client = new JsonHttpClient($this->createMockHttpClient());
        $response = $client->put('https://example.com', ['test' => true], ['param1' => 1], ['X-H: Test']);
        self::assertSame([
            'PUT',
            'https://example.com?param1=1',
            ['X-H: Test', 'Accept: application/json; charset=utf-8', 'Content-Type: application/json; charset=utf-8'],
            '{"test":true}',
        ], $response);
    }

    public function testSetAllowedResponseCode(): void
    {
        $client = new JsonHttpClient($this->createMockHttpClient(201));
        $client->setAllowedHttpCode(201);
        $client->get('https://example.com');

        $client = new JsonHttpClient($this->createMockHttpClient(201));
        $client->setAllowedHttpCode([201]);
        $client->get('https://example.com');

        $client = new JsonHttpClient($this->createMockHttpClient(201));
        $client->setAllowedHttpCode([201, 202]);
        $client->get('https://example.com');

        $client = new JsonHttpClient($this->createMockHttpClient());
        $client->setAllowedHttpCode(201);
        try {
            $client->get('https://example.com');
            self::failException(UnexpectedHttpCodeException::class);
        } catch (UnexpectedHttpCodeException $e) {
            self::assertSame('Expected http code 201, but received http code 200.', $e->getMessage());
        }

        $client = new JsonHttpClient($this->createMockHttpClient());
        $client->setAllowedHttpCode([201]);
        try {
            $client->get('https://example.com');
            self::failException(UnexpectedHttpCodeException::class);
        } catch (UnexpectedHttpCodeException $e) {
            self::assertSame('Expected http code 201, but received http code 200.', $e->getMessage());
        }

        $client = new JsonHttpClient($this->createMockHttpClient());
        $client->setAllowedHttpCode([201, 202]);
        try {
            $client->get('https://example.com');
            self::failException(UnexpectedHttpCodeException::class);
        } catch (UnexpectedHttpCodeException $e) {
            self::assertSame('Expected http codes [201,202], but received http code 200.', $e->getMessage());
        }

        $client = new JsonHttpClient($this->createMockHttpClient());
        /** @psalm-suppress InvalidArgument */
        $client->setAllowedHttpCode([]);
        try {
            $client->get('https://example.com');
            self::failException(UnexpectedHttpCodeException::class);
        } catch (UnexpectedHttpCodeException $e) {
            self::assertSame('Expected http codes [], but received http code 200.', $e->getMessage());
        }
    }

    public function testSetExpectedContentType(): void
    {
        $client = (new JsonHttpClient($this->createMockHttpClient(contentType: 'application/json; test')))
            ->setExpectedContentType('application/json; test');
        $client->get('https://example.com');

        $client = (new JsonHttpClient($this->createMockHttpClient(contentType: null)))
            ->setExpectedContentType(null);
        $client->get('https://example.com');

        $client = (new JsonHttpClient($this->createMockHttpClient(contentType: null)))
            ->setExpectedContentType(['application/json', null]);
        $client->get('https://example.com');

        $client = new JsonHttpClient($this->createMockHttpClient(contentType: 'application/json; test'));
        try {
            $client->get('https://example.com');
            self::failException(UnexpectedContentTypeException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }

        $client = new JsonHttpClient($this->createMockHttpClient(contentType: null));
        try {
            $client->get('https://example.com');
            self::failException(UnexpectedContentTypeException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }
    }

    /**
     * @psalm-suppress MixedAssignment
     */
    public function testSetHeader(): void
    {
        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setHeader('X-H', 'X-H: Test; setHeader');
        $response = $client->get('https://example.com', null, ['X-H: Test']);
        self::assertSame([
            'GET',
            'https://example.com',
            ['X-H' => 'X-H: Test; setHeader', 'X-H: Test', 'Accept: application/json; charset=utf-8'],
            null,
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setHeader('X-H', 'X-H: Test')
            ->setHeader('X-H', 'X-H: Test');
        $response = $client->get('https://example.com');
        self::assertSame([
            'GET',
            'https://example.com',
            ['X-H' => 'X-H: Test', 'Accept: application/json; charset=utf-8'],
            null,
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setHeader('X-H', 'X-H: Test')
            ->setHeader('X-H', null);
        $response = $client->get('https://example.com');
        self::assertSame([
            'GET',
            'https://example.com',
            ['Accept: application/json; charset=utf-8'],
            null,
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->addHeader('X-H: Test')
            ->setHeader(0, null);
        $response = $client->get('https://example.com');
        self::assertSame([
            'GET',
            'https://example.com',
            ['Accept: application/json; charset=utf-8'],
            null,
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setHeader('X-H', 'X-H: Test; setHeader');
        $response = $client->get('https://example.com', null, ['X-H' => 'X-H: Test']);
        self::assertSame([
            'GET',
            'https://example.com',
            ['X-H' => 'X-H: Test', 'Accept: application/json; charset=utf-8'],
            null,
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setHeader('X-H', 'X-H: Test; setHeader');
        /** @psalm-suppress InvalidArgument */
        $response = $client->get('https://example.com', null, ['X-H' => null]);
        self::assertSame([
            'GET',
            'https://example.com',
            ['Accept: application/json; charset=utf-8'],
            null,
        ], $response);
    }

    /**
     * @psalm-suppress MixedAssignment
     */
    public function testSetHeaders(): void
    {
        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setHeaders(['X-H: Test; setHeaders']);
        $response = $client->get('https://example.com', null, ['X-H: Test']);
        self::assertSame([
            'GET',
            'https://example.com',
            ['X-H: Test; setHeaders', 'X-H: Test', 'Accept: application/json; charset=utf-8'],
            null,
        ], $response);

        /** @psalm-suppress InvalidArgument */
        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setHeaders(['']);
        $response = $client->get('https://example.com', null, ['X-H: Test']);
        self::assertSame([
            'GET',
            'https://example.com',
            [1 => 'X-H: Test', 2 => 'Accept: application/json; charset=utf-8'],
            null,
        ], $response);
    }

    /**
     * @psalm-suppress MixedAssignment
     */
    public function testSetRequestAccept(): void
    {
        $client = new JsonHttpClient($this->createMockHttpClient());
        $response = $client->get('https://example.com');
        self::assertSame([
            'GET',
            'https://example.com',
            ['Accept: application/json; charset=utf-8'],
            null,
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setRequestAccept('application/json');
        $response = $client->get('https://example.com');
        self::assertSame([
            'GET',
            'https://example.com',
            ['Accept: application/json'],
            null,
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setRequestAccept(null);
        $response = $client->get('https://example.com');
        self::assertSame([
            'GET',
            'https://example.com',
            [],
            null,
        ], $response);

        /** @psalm-suppress InvalidArgument */
        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setRequestAccept('');
        $response = $client->get('https://example.com');
        self::assertSame([
            'GET',
            'https://example.com',
            [],
            null,
        ], $response);
    }

    /**
     * @psalm-suppress MixedAssignment
     */
    public function testSetRequestContentType(): void
    {
        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setRequestContentType('application/json');
        $response = $client->post('https://example.com', ['test' => true]);
        self::assertSame([
            'POST',
            'https://example.com',
            ['Accept: application/json; charset=utf-8', 'Content-Type: application/json'],
            '{"test":true}',
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setRequestContentType('application/json');
        $response = $client->post('https://example.com', null);
        self::assertSame([
            'POST',
            'https://example.com',
            ['Accept: application/json; charset=utf-8', 'Content-Type: application/json'],
            'null',
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setRequestContentType('application/json');
        $response = $client->post('https://example.com', new NoBody());
        self::assertSame([
            'POST',
            'https://example.com',
            ['Accept: application/json; charset=utf-8'],
            '',
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setRequestContentType('application/json');
        $response = $client->post('https://example.com', new UrlencodedBody(['test' => true]));
        self::assertSame([
            'POST',
            'https://example.com',
            ['Accept: application/json; charset=utf-8', 'Content-Type: application/x-www-form-urlencoded'],
            'test=1',
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setRequestContentType('application/json');
        $response = $client->post('https://example.com', new UrlencodedBody([]));
        self::assertSame([
            'POST',
            'https://example.com',
            ['Accept: application/json; charset=utf-8', 'Content-Type: application/x-www-form-urlencoded'],
            '',
        ], $response);
    }

    /**
     * @psalm-suppress MixedAssignment
     */
    public function testSetResponseHeadersRequired(): void
    {
        $client = (new JsonHttpClient($this->createMockHttpClient()));
        $response = $client->get('https://example.com');
        self::assertSame([
            'GET',
            'https://example.com',
            ['Accept: application/json; charset=utf-8'],
            null,
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setResponseHeadersRequired();
        $response = $client->get('https://example.com');
        self::assertSame([
            'GET',
            'https://example.com',
            ['Accept: application/json; charset=utf-8'],
            null,
            'ResponseHeadersRequired',
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setResponseHeadersRequired(false);
        $response = $client->get('https://example.com');
        self::assertSame([
            'GET',
            'https://example.com',
            ['Accept: application/json; charset=utf-8'],
            null,
        ], $response);

        $client = (new JsonHttpClient($this->createMockHttpClient()))
            ->setResponseHeadersRequired()
            ->setResponseHeadersRequired(null);
        $response = $client->get('https://example.com');
        self::assertSame([
            'GET',
            'https://example.com',
            ['Accept: application/json; charset=utf-8'],
            null,
        ], $response);
    }

    private function createMockHttpClient(
        int $code = 200,
        ?string $contentType = 'application/json; charset=utf-8'
    ): MockObject&HttpClientInterface {
        /** @noinspection PhpUnhandledExceptionInspection */
        $mock = $this->createMock(HttpClientInterface::class);
        $mock->expects($this->once())
            ->method('request')
            ->willReturnCallback(static function (HttpRequestInterface $request) use ($code, $contentType) {
                $body = $request->getBody();
                $isPostRequest = $body !== null;
                $requestData = [$request->getMethod(), $request->getUrl(), $request->getHeaders($isPostRequest), $body];
                if ($request->isResponseHeadersRequired()) {
                    $requestData[] = 'ResponseHeadersRequired';
                }
                $requestDataJson = json_encode($requestData, JSON_THROW_ON_ERROR);

                return $request->makeResponse($code, $request->getUrl(), [], $contentType, $requestDataJson);
            });

        return $mock;
    }

    private function createMockHttpClientWithHtml(?string $contentType = 'text/html'): MockObject&HttpClientInterface
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $mock = $this->createMock(HttpClientInterface::class);
        $mock->expects($this->once())
            ->method('request')
            ->willReturnCallback(static function (HttpRequestInterface $request) use ($contentType) {
                return $request->makeResponse(200, $request->getUrl(), [], $contentType, '<html lang="en"></html>');
            });

        return $mock;
    }

    /**
     * @param class-string $className
     */
    private static function failException(string $className): never
    {
        self::fail(sprintf('Failed asserting that exception of type "%s" is thrown.', $className));
    }
}
