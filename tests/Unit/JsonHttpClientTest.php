<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Contracts\HttpClientInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\Exceptions\Http\InternalServerErrorException;
use Mj4444\SimpleHttpClient\Exceptions\Http\NotFoundException;
use Mj4444\SimpleHttpClient\Exceptions\Http\UnexpectedHttpCodeException;
use Mj4444\SimpleHttpClient\Exceptions\JsonHttpClient\BodyNotValidJsonException;
use Mj4444\SimpleHttpClient\Exceptions\JsonHttpClient\ResponseNotJsonException;
use Mj4444\SimpleHttpClient\Exceptions\JsonHttpClient\ResponseNotValidJsonException;
use Mj4444\SimpleHttpClient\HttpResponse;
use Mj4444\SimpleHttpClient\JsonHttpClient;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;

/**
 * @api
 */
final class JsonHttpClientTest extends Unit
{
    public function testBadJsonRequest(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $client = new JsonHttpClient($this->createMock(HttpClientInterface::class));
        $body = new stdClass();
        $body->ref = $body;
        try {
            $client->post('https://example.com', $body);
            self::failException(BodyNotValidJsonException::class);
        } catch (BodyNotValidJsonException $e) {
            self::assertSame('Not valid json.', $e->getMessage());
            self::assertSame($body, $e->getBody());
        } finally {
            unset($body->ref, $body);
        }
    }

    public function testBadJsonResponse(): void
    {
        $client = new JsonHttpClient($this->createMockHttpClientWithHtml('application/json; charset=utf-8'));
        try {
            $client->get('https://example.com');
            self::failException(ResponseNotValidJsonException::class);
        } catch (ResponseNotValidJsonException $e) {
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
            null
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
            null
        ], $response);
    }

    public function testHtmlResponse(): void
    {
        $client = new JsonHttpClient($this->createMockHttpClientWithHtml());
        try {
            $client->get('https://example.com');
            self::failException(ResponseNotJsonException::class);
        } catch (ResponseNotJsonException $e) {
            self::assertSame('Response not json.', $e->getMessage());
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
            '{"test":true}'
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
            '{"test":true}'
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
            '{"test":true}'
        ], $response);
    }

    public function testSetAllowedResponseCodes(): void
    {
        $client = new JsonHttpClient($this->createMockHttpClient());
        $client->setAllowedResponseCodes([201]);
        try {
            $client->get('https://example.com');
            self::failException(UnexpectedHttpCodeException::class);
        } catch (UnexpectedHttpCodeException $e) {
            self::assertSame('Expected http code 201, but received http code 200.', $e->getMessage());
        }

        $client = new JsonHttpClient($this->createMockHttpClient());
        $client->setAllowedResponseCodes([201, 202]);
        try {
            $client->get('https://example.com');
            self::failException(UnexpectedHttpCodeException::class);
        } catch (UnexpectedHttpCodeException $e) {
            self::assertSame('Expected http codes [201,202], but received http code 200.', $e->getMessage());
        }

        $client = new JsonHttpClient($this->createMockHttpClient(500));
        $client->setAllowedResponseCodes([]);
        $response = $client->get('https://example.com');
        self::assertSame([
            'GET',
            'https://example.com',
            ['Accept: application/json; charset=utf-8'],
            null
        ], $response);
    }

    private function createMockHttpClient(int $code = 200): MockObject&HttpClientInterface
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $mock = $this->createMock(HttpClientInterface::class);
        $mock->expects($this->once())
            ->method('request')
            ->willReturnCallback(static function (HttpRequestInterface $request) use ($code) {
                $contentType = 'application/json; charset=utf-8';
                $requestData = [$request->getMethod(), $request->getUrl(), $request->getHeaders(), $request->getBody()];
                $requestDataJson = json_encode($requestData, JSON_THROW_ON_ERROR);

                return new HttpResponse($request, $code, [], $contentType, $requestDataJson);
            });

        return $mock;
    }

    private function createMockHttpClientWithHtml(?string $contentType = null): MockObject&HttpClientInterface
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $mock = $this->createMock(HttpClientInterface::class);
        $mock->expects($this->once())
            ->method('request')
            ->willReturnCallback(static function (HttpRequestInterface $request) use ($contentType) {
                $contentType ??= 'text/html';

                return new HttpResponse($request, 200, [], $contentType, '<html lang="en"></html>');
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
