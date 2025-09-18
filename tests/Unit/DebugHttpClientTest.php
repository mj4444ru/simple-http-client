<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Contracts\HttpClientInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\DebugHttpClient;
use Mj4444\SimpleHttpClient\HttpRequest\HttpRequest;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @api
 */
class DebugHttpClientTest extends Unit
{
    public function testRequest(): void
    {
        // Simple test
        $client = new DebugHttpClient($this->createMockHttpClient());
        $response = $client->request(new HttpRequest('https://example.com'));
        self::assertSame('https://example.com', $response->getRequest()->getUrl());
    }

    public function testSetDebug(): void
    {
        // Simple test
        $client = new DebugHttpClient($this->createMockHttpClient());
        $client->setDebug(true);
        $client->request(new HttpRequest('https://example.com'));
        self::assertNotNull($client->lastRequest);
        self::assertNotNull($client->lastResponse);

        // Simple test
        $client = new DebugHttpClient($this->createMockHttpClient());
        $client->setDebug(false);
        $client->request(new HttpRequest('https://example.com'));
        self::assertNull($client->lastRequest);
        self::assertNull($client->lastResponse);
    }

    public function testSetGlobalDebug(): void
    {
        // Simple test
        $client = new DebugHttpClient($this->createMockHttpClient());
        DebugHttpClient::setGlobalDebug(true);
        $client->request(new HttpRequest('https://example.com'));
        self::assertNotNull(DebugHttpClient::$globalLastRequest);
        self::assertNotNull(DebugHttpClient::$globalLastResponse);

        // Simple test
        $client = new DebugHttpClient($this->createMockHttpClient());
        DebugHttpClient::setGlobalDebug(false);
        $client->request(new HttpRequest('https://example.com'));
        /** @psalm-suppress DocblockTypeContradiction Psalm bug? */
        self::assertNull(DebugHttpClient::$globalLastRequest);
        /** @psalm-suppress DocblockTypeContradiction Psalm bug? */
        self::assertNull(DebugHttpClient::$globalLastResponse);
    }

    private function createMockHttpClient(): MockObject&HttpClientInterface
    {
        $mock = $this->createMock(HttpClientInterface::class);
        $mock->expects($this->once())
            ->method('request')
            ->willReturnCallback(static function (HttpRequestInterface $request) {
                $url = $request->getUrl();

                return $request->makeResponse(200, $url, $url, null, [], 'text/html', '<html lang="en"></html>');
            });

        return $mock;
    }
}
