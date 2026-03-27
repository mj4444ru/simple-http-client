<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\HttpRequest\Body\MultipartBody\StringFile;

final class StringFileTest extends Unit
{
    public function testConstructor(): void
    {
        // Simple test
        $file = new StringFile('test data', 'file.txt');
        self::assertSame('test data', $file->data);
        self::assertSame('file.txt', $file->postName);
        self::assertSame('application/octet-stream', $file->mime);

        // Simple test
        $file = new StringFile('{"key":"value"}', 'data.json', 'application/json');
        self::assertSame('{"key":"value"}', $file->data);
        self::assertSame('data.json', $file->postName);
        self::assertSame('application/json', $file->mime);
    }

    public function testGetData(): void
    {
        // Simple test
        $file = new StringFile('binary content', 'image.png');
        self::assertSame('binary content', $file->getData());
    }

    public function testGetMime(): void
    {
        // Simple test
        $file = new StringFile('content', 'file.txt', 'text/plain');
        self::assertSame('text/plain', $file->getMime());

        // Simple test
        $file = new StringFile('content', 'file.txt');
        self::assertSame('application/octet-stream', $file->getMime());
    }

    public function testGetPostName(): void
    {
        // Simple test
        $file = new StringFile('content', 'document.pdf');
        self::assertSame('document.pdf', $file->getPostName());
    }
}
