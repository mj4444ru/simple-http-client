<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\HttpRequest\Body\BodyReader\StreamReader;
use Mj4444\SimpleHttpClient\HttpRequest\Body\StreamBody;

final class StreamBodyTest extends Unit
{
    public function testConstructor(): void
    {
        // Simple test
        $resource = fopen('php://temp', 'rb+');
        self::assertNotFalse($resource);
        $body = new StreamBody($resource);
        self::assertSame($resource, $body->resource);
        self::assertNull($body->contentType);
        self::assertNull($body->offset);
        self::assertNull($body->length);
        fclose($resource);

        // Simple test
        $resource = fopen('php://temp', 'rb+');
        self::assertNotFalse($resource);
        $body = new StreamBody($resource, 'application/octet-stream');
        self::assertSame('application/octet-stream', $body->contentType);
        fclose($resource);

        // Simple test
        $resource = fopen('php://temp', 'rb+');
        self::assertNotFalse($resource);
        $body = new StreamBody($resource, null, 10);
        self::assertSame(10, $body->offset);
        self::assertNull($body->length);
        fclose($resource);

        // Simple test
        $resource = fopen('php://temp', 'rb+');
        self::assertNotFalse($resource);
        $body = new StreamBody($resource, null, 0, 100);
        self::assertSame(0, $body->offset);
        self::assertSame(100, $body->length);
        fclose($resource);
    }

    public function testGetBody(): void
    {
        // Simple test
        $resource = fopen('php://temp', 'rb+');
        self::assertNotFalse($resource);
        fwrite($resource, 'test content');
        rewind($resource);
        $body = new StreamBody($resource);
        $result = $body->getBody();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(StreamReader::class, $result);
        fclose($resource);
    }

    public function testGetBodyReturnsNewInstanceEachTime(): void
    {
        // Simple test
        $resource = fopen('php://temp', 'rb+');
        self::assertNotFalse($resource);
        $body = new StreamBody($resource);
        $result1 = $body->getBody();
        $result2 = $body->getBody();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(StreamReader::class, $result1);
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(StreamReader::class, $result2);
        self::assertNotSame($result1, $result2);
        fclose($resource);
    }

    public function testGetBodyWithStartAndLength(): void
    {
        // Simple test
        $resource = fopen('php://temp', 'rb+');
        self::assertNotFalse($resource);
        fwrite($resource, 'test content for stream');
        rewind($resource);
        $body = new StreamBody($resource, null, 5, 10);
        $result = $body->getBody();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(StreamReader::class, $result);
        fclose($resource);
    }

    public function testGetContentType(): void
    {
        // Simple test
        $resource = fopen('php://temp', 'rb+');
        self::assertNotFalse($resource);
        $body = new StreamBody($resource);
        self::assertNull($body->getContentType());
        fclose($resource);

        // Simple test
        $resource = fopen('php://temp', 'rb+');
        self::assertNotFalse($resource);
        $body = new StreamBody($resource, 'application/json');
        self::assertSame('application/json', $body->getContentType());
        fclose($resource);

        // Simple test
        $resource = fopen('php://temp', 'rb+');
        self::assertNotFalse($resource);
        $body = new StreamBody($resource, 'text/plain; charset=utf-8');
        self::assertSame('text/plain; charset=utf-8', $body->getContentType());
        fclose($resource);
    }
}
