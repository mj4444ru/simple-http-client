<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\HttpRequest\Body\BodyReader\FileReader;
use Mj4444\SimpleHttpClient\HttpRequest\Body\FileBody;

final class FileBodyTest extends Unit
{
    public function testConstructor(): void
    {
        // Simple test
        $body = new FileBody(__FILE__);
        self::assertSame(__FILE__, $body->fileName);
        self::assertNull($body->contentType);
        self::assertSame(0, $body->offset);
        self::assertNull($body->length);

        // Simple test
        $body = new FileBody(__FILE__, 'application/json');
        self::assertSame('application/json', $body->contentType);

        // Simple test
        $body = new FileBody(__FILE__, 'text/plain', 10);
        self::assertSame(10, $body->offset);
        self::assertNull($body->length);

        // Simple test
        $body = new FileBody(__FILE__, 'text/plain', 0, 100);
        self::assertSame(0, $body->offset);
        self::assertSame(100, $body->length);

        // Simple test - negative start allowed in constructor
        /** @psalm-suppress InvalidArgument */
        $body = new FileBody(__FILE__, null, -1);
        self::assertSame(-1, $body->offset);
        self::assertNull($body->length);

        // Simple test - negative length allowed in constructor
        $body = new FileBody(__FILE__, null, 0, -10);
        self::assertSame(0, $body->offset);
        self::assertSame(-10, $body->length);
    }

    public function testGetBody(): void
    {
        $testFileSize = filesize(__FILE__);
        self::assertIsInt($testFileSize);

        // Simple test
        $body = new FileBody(__FILE__);
        $reader = $body->getBody();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(FileReader::class, $reader);
        self::assertEquals($testFileSize, $reader->getBytesLeft());

        // Simple test
        $body = new FileBody(__FILE__, null, 10, 50);
        $reader = $body->getBody();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(FileReader::class, $reader);
        self::assertEquals(50, $reader->getBytesLeft());

        // Simple test
        $body = new FileBody(__FILE__, null, 0, -10);
        $reader = $body->getBody();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(FileReader::class, $reader);
        self::assertEquals($testFileSize - 10, $reader->getBytesLeft());
    }

    public function testGetBodyReturnsNewInstanceEachTime(): void
    {
        // Simple test
        $body = new FileBody(__FILE__);
        $result1 = $body->getBody();
        $result2 = $body->getBody();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(FileReader::class, $result1);
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(FileReader::class, $result2);
        self::assertNotSame($result1, $result2);
    }

    public function testGetContentType(): void
    {
        // Simple test
        $body = new FileBody(__FILE__);
        self::assertNull($body->getContentType());

        // Simple test
        $body = new FileBody(__FILE__, 'text/plain');
        self::assertSame('text/plain', $body->getContentType());

        // Simple test
        $body = new FileBody(__FILE__, 'application/json; charset=utf-8');
        self::assertSame('application/json; charset=utf-8', $body->getContentType());
    }
}
