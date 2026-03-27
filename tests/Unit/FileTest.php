<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\HttpRequest\Body\MultipartBody\File;

final class FileTest extends Unit
{
    public function testConstructor(): void
    {
        // Simple test
        $file = new File('/path/to/file.txt', 'file.txt');
        self::assertSame('/path/to/file.txt', $file->fileName);
        self::assertSame('file.txt', $file->postName);
        self::assertSame('application/octet-stream', $file->mime);

        // Simple test
        $file = new File('/path/to/data.json', 'data.json', 'application/json');
        self::assertSame('/path/to/data.json', $file->fileName);
        self::assertSame('data.json', $file->postName);
        self::assertSame('application/json', $file->mime);
    }

    public function testGetFileName(): void
    {
        // Simple test
        $file = new File('/path/to/image.png', 'image.png');
        self::assertSame('/path/to/image.png', $file->getFileName());
    }

    public function testGetMime(): void
    {
        // Simple test
        $file = new File('/path/to/file.txt', 'file.txt', 'text/plain');
        self::assertSame('text/plain', $file->getMime());

        // Simple test
        $file = new File('/path/to/file.txt', 'file.txt');
        self::assertSame('application/octet-stream', $file->getMime());
    }

    public function testGetPostName(): void
    {
        // Simple test
        $file = new File('/path/to/document.pdf', 'document.pdf');
        self::assertSame('document.pdf', $file->getPostName());
    }
}
