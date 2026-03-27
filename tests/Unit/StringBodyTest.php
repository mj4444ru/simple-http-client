<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\HttpRequest\Body\StringBody;

final class StringBodyTest extends Unit
{
    public function testConstructor(): void
    {
        // Simple test
        $body = new StringBody('test content');
        self::assertSame('test content', $body->value);
        self::assertNull($body->contentType);

        // Simple test
        $body = new StringBody('test content', 'text/plain');
        self::assertSame('test content', $body->value);
        self::assertSame('text/plain', $body->contentType);
    }

    public function testGetBody(): void
    {
        // Simple test
        $body = new StringBody('hello world');
        self::assertSame('hello world', $body->getBody());

        // Simple test
        $body = new StringBody('{"json":"data"}');
        self::assertSame('{"json":"data"}', $body->getBody());
    }

    public function testGetBodyWithEmptyString(): void
    {
        // Simple test
        $body = new StringBody('');
        self::assertSame('', $body->getBody());
    }

    public function testGetBodyWithMultilineContent(): void
    {
        // Simple test
        $body = new StringBody("line1\nline2\nline3");
        self::assertSame("line1\nline2\nline3", $body->getBody());
    }

    public function testGetBodyWithSpecialCharacters(): void
    {
        // Simple test
        $body = new StringBody('<html>&copy; 2024</html>');
        self::assertSame('<html>&copy; 2024</html>', $body->getBody());
    }

    public function testGetBodyWithUnicode(): void
    {
        // Simple test
        $body = new StringBody('Привет мир! 🌍');
        self::assertSame('Привет мир! 🌍', $body->getBody());
    }

    public function testGetContentType(): void
    {
        // Simple test
        $body = new StringBody('test');
        self::assertNull($body->getContentType());

        // Simple test
        $body = new StringBody('test', 'text/plain');
        self::assertSame('text/plain', $body->getContentType());

        // Simple test
        $body = new StringBody('test', 'application/json; charset=utf-8');
        self::assertSame('application/json; charset=utf-8', $body->getContentType());
    }
}
