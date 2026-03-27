<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\HttpRequest\Body\MultipartBody\File;
use Mj4444\SimpleHttpClient\HttpRequest\Body\MultipartBody\StringFile;
use Mj4444\SimpleHttpClient\HttpRequest\Body\MultipartFormBody;

final class MultipartFormBodyTest extends Unit
{
    public function testConstructor(): void
    {
        // Simple test
        $body = new MultipartFormBody(['name' => 'John']);
        self::assertSame(['name' => 'John'], $body->fields);
    }

    public function testGetBody(): void
    {
        // Simple test
        $body = new MultipartFormBody(['name' => 'John']);
        $result = $body->getBody();
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(MultipartFormBody::class, $result);
        self::assertSame($body, $result);
    }

    public function testGetContentType(): void
    {
        // Simple test
        $body = new MultipartFormBody(['name' => 'John']);
        self::assertNull($body->getContentType());
    }

    public function testGetFields(): void
    {
        // Simple test
        $body = new MultipartFormBody(['name' => 'John', 'age' => '30']);
        self::assertSame(['name' => 'John', 'age' => '30'], $body->getFields());
    }

    public function testGetFieldsWithEmptyString(): void
    {
        // Simple test
        $body = new MultipartFormBody(['field' => '']);
        $fields = $body->getFields();
        self::assertSame('', $fields['field']);
    }

    public function testGetFieldsWithFile(): void
    {
        // Simple test
        $file = new File('/path/to/file.txt', 'file.txt', 'text/plain');
        $body = new MultipartFormBody(['document' => $file]);
        $fields = $body->getFields();
        self::assertInstanceOf(File::class, $fields['document']);
    }

    public function testGetFieldsWithIntegerValues(): void
    {
        // Simple test
        $body = new MultipartFormBody([
            'age' => 25,
            'count' => 100
        ]);
        $fields = $body->getFields();
        self::assertSame(25, $fields['age']);
        self::assertSame(100, $fields['count']);
    }

    public function testGetFieldsWithMixedValues(): void
    {
        // Simple test
        $file = new File('/path/to/file.txt', 'file.txt');
        $stringFile = new StringFile('content', 'data.txt');
        $body = new MultipartFormBody([
            'name' => 'John',
            'age' => 30,
            'document' => $file,
            'data' => $stringFile
        ]);
        $fields = $body->getFields();
        self::assertSame('John', $fields['name']);
        self::assertSame(30, $fields['age']);
        self::assertInstanceOf(File::class, $fields['document']);
        self::assertInstanceOf(StringFile::class, $fields['data']);
    }

    public function testGetFieldsWithSpecialCharacters(): void
    {
        // Simple test
        $body = new MultipartFormBody([
            'message' => 'Hello & World <test>',
            'unicode' => 'Привет мир'
        ]);
        $fields = $body->getFields();
        self::assertSame('Hello & World <test>', $fields['message']);
        self::assertSame('Привет мир', $fields['unicode']);
    }

    public function testGetFieldsWithStringFile(): void
    {
        // Simple test
        $stringFile = new StringFile('file content', 'data.txt', 'text/plain');
        $body = new MultipartFormBody(['data' => $stringFile]);
        $fields = $body->getFields();
        self::assertInstanceOf(StringFile::class, $fields['data']);
        self::assertSame('file content', $fields['data']->getData());
    }

    public function testGetFieldsWithStringValues(): void
    {
        // Simple test
        $body = new MultipartFormBody([
            'username' => 'john_doe',
            'email' => 'john@example.com'
        ]);
        $fields = $body->getFields();
        self::assertSame('john_doe', $fields['username']);
        self::assertSame('john@example.com', $fields['email']);
    }
}
