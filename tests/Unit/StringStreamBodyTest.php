<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\HttpRequest\Body\BodyReader\StringReader;
use Mj4444\SimpleHttpClient\HttpRequest\Body\StringStreamBody;

final class StringStreamBodyTest extends Unit
{
    public function testConstructor(): void
    {
        // Simple test
        $body = new StringStreamBody('test content');
        self::assertSame('test content', $body->content);
        self::assertNull($body->contentType);
        self::assertSame(0, $body->offset);
        self::assertNull($body->length);

        // Simple test
        $body = new StringStreamBody('test content', 'application/octet-stream');
        self::assertSame('application/octet-stream', $body->contentType);
        self::assertSame('test content', $body->content);

        // Simple test
        $body = new StringStreamBody('test content', null, 10);
        self::assertSame(10, $body->offset);
        self::assertNull($body->length);

        // Simple test
        $body = new StringStreamBody('test content', null, 0, 100);
        self::assertSame(0, $body->offset);
        self::assertSame(100, $body->length);
    }

    public function testGetBody(): void
    {
        // Simple test
        $body = new StringStreamBody('test content');
        $result = $body->getBody();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(StringReader::class, $result);
    }

    public function testGetBodyReturnsNewInstanceEachTime(): void
    {
        // Simple test
        $body = new StringStreamBody('test content');
        $result1 = $body->getBody();
        $result2 = $body->getBody();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(StringReader::class, $result1);
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(StringReader::class, $result2);
        self::assertNotSame($result1, $result2);
    }

    public function testGetBodyWithStartAndLength(): void
    {
        // Simple test
        $body = new StringStreamBody('test content for string', null, 5, 10);
        $result = $body->getBody();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(StringReader::class, $result);
    }

    public function testGetContentType(): void
    {
        // Simple test
        $body = new StringStreamBody('test content');
        self::assertNull($body->getContentType());

        // Simple test
        $body = new StringStreamBody('test content', 'application/json');
        self::assertSame('application/json', $body->getContentType());

        // Simple test
        $body = new StringStreamBody('test content', 'text/plain; charset=utf-8');
        self::assertSame('text/plain; charset=utf-8', $body->getContentType());
    }
}
