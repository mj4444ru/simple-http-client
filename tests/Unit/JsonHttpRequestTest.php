<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\HttpRequest\JsonHttpRequest;
use Mj4444\SimpleHttpClient\HttpResponse\JsonHttpResponse;

final class JsonHttpRequestTest extends Unit
{
    public function testConstructor(): void
    {
        // Simple test
        $request = new JsonHttpRequest('https://example.com');
        self::assertSame('https://example.com', $request->getUrl());
        self::assertSame('application/json; charset=utf-8', $request->accept);
        self::assertSame(
            ['application/json; charset=utf-8', 'application/json;charset=utf-8', 'application/json'],
            $request->expectedContentType
        );
    }

    public function testMakeResponse(): void
    {
        // Simple test
        $request = new JsonHttpRequest('https://example.com');
        $response = $request->makeResponse(
            200,
            'https://example.com',
            'https://example.com',
            null,
            ['X-Custom' => ['test']],
            'application/json',
            '{"key":"value"}'
        );
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(JsonHttpResponse::class, $response);
        self::assertSame(200, $response->httpCode);
        self::assertSame('https://example.com', $response->url);
        self::assertSame('https://example.com', $response->effectiveUrl);
        self::assertNull($response->redirectUrl);
        self::assertSame(['X-Custom' => ['test']], $response->headers);
        self::assertSame('application/json', $response->contentType);
        self::assertSame('{"key":"value"}', $response->getBody());

        // Simple test
        $request = new JsonHttpRequest('https://example.com', ['param' => 'value']);
        self::assertSame('https://example.com?param=value', $request->getUrl());

        // Simple test
        $request = new JsonHttpRequest('https://example.com');
        $response = $request->makeResponse(
            301,
            'https://example.com',
            'https://redirect.com',
            'https://redirect.com',
            [],
            'application/json',
            '{}'
        );
        self::assertSame(301, $response->httpCode);
        self::assertSame('https://example.com', $response->url);
        self::assertSame('https://redirect.com', $response->effectiveUrl);
        self::assertSame('https://redirect.com', $response->redirectUrl);
    }
}
