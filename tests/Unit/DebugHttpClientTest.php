<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Contracts\HttpClientInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;
use Mj4444\SimpleHttpClient\DebugHttpClient;
use Mj4444\SimpleHttpClient\HttpRequest\HttpRequest;
use Mj4444\SimpleHttpClient\HttpResponse\HttpResponse;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @api
 */
class DebugHttpClientTest extends Unit
{
    public function testRequest(): void
    {
        $client = new DebugHttpClient($this->createMockHttpClient());
        $response = $client->request(new HttpRequest('https://example.com'));
        self::assertSame('https://example.com', $response->getRequest()->getUrl());
    }

    public function testSetDebug(): void
    {
        $client = new DebugHttpClient($this->createMockHttpClient());
        $client->setDebug(true);
        $client->request(new HttpRequest('https://example.com'));
        self::assertNotNull($client->lastRequest);
        self::assertNotNull($client->lastResponse);

        $client = new DebugHttpClient($this->createMockHttpClient());
        $client->setDebug(false);
        $client->request(new HttpRequest('https://example.com'));
        self::assertNull($client->lastRequest);
        self::assertNull($client->lastResponse);
    }

    public function testSetGlobalDebug(): void
    {
        $client = new DebugHttpClient($this->createMockHttpClient());
        DebugHttpClient::setGlobalDebug(true);
        $client->request(new HttpRequest('https://example.com'));
        self::assertNotNull(DebugHttpClient::$globalLastRequest);
        self::assertNotNull(DebugHttpClient::$globalLastResponse);

        $client = new DebugHttpClient($this->createMockHttpClient());
        DebugHttpClient::setGlobalDebug(false);
        $client->request(new HttpRequest('https://example.com'));
        /** @psalm-suppress DocblockTypeContradiction Psalm bug? */
        self::assertNull(DebugHttpClient::$globalLastRequest);
        /** @psalm-suppress DocblockTypeContradiction Psalm bug? */
        self::assertNull(DebugHttpClient::$globalLastResponse);
    }

    public function testSetMiddleware(): void
    {
        $fn = static function (HttpRequestInterface $request, HttpClientInterface $client): HttpResponseInterface {
            /** @var HttpRequest $request */
            $request->setUrl('https://example.com/');

            return $client->request($request);
        };

        $client = new DebugHttpClient($this->createMockHttpClient());
        $client->setMiddleware($fn);
        $response = $client->request(new HttpRequest('https://example.com'));
        self::assertSame('https://example.com/', $response->getRequest()->getUrl());
    }

    private function createMockHttpClient(): MockObject&HttpClientInterface
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $mock = $this->createMock(HttpClientInterface::class);
        $mock->expects($this->once())
            ->method('request')
            ->willReturnCallback(static function (HttpRequestInterface $request) {
                return $request->makeResponse(200, $request->getUrl(), [], 'text/html', '<html lang="en"></html>');
            });

        return $mock;
    }
}
