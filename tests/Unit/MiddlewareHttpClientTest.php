<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Contracts\HttpClientInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface;
use Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface;
use Mj4444\SimpleHttpClient\HttpRequest\BaseHttpRequest;
use Mj4444\SimpleHttpClient\HttpRequest\HttpRequest;
use Mj4444\SimpleHttpClient\MiddlewareHttpClient;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @api
 */
class MiddlewareHttpClientTest extends Unit
{
    public function testSetMiddleware(): void
    {
        // Simple test
        $fn = static function (HttpRequestInterface $request, HttpClientInterface $client): HttpResponseInterface {
            /** @var BaseHttpRequest $request */
            $request->url .= '?middleware=1';

            return $client->request($request);
        };
        $client = new MiddlewareHttpClient($this->createMockHttpClient());

        /** @psalm-suppress InvalidArgument Psalm bug? https://github.com/vimeo/psalm/issues/11562 */
        $client->setMiddleware($fn);
        $response = $client->request(new HttpRequest('https://example.com'));
        self::assertSame('https://example.com?middleware=1', $response->getRequest()->getUrl());
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
