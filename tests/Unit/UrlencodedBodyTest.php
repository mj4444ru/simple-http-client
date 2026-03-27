<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\HttpRequest\Body\UrlencodedBody;
use stdClass;

final class UrlencodedBodyTest extends Unit
{
    public function testConstructor(): void
    {
        // Simple test
        $body = new UrlencodedBody(['key' => 'value']);
        self::assertSame(['key' => 'value'], $body->data);
        self::assertSame('application/x-www-form-urlencoded', $body->contentType);

        // Simple test
        $body = new UrlencodedBody(['key' => 'value'], 'application/x-www-form-urlencoded');
        self::assertSame('application/x-www-form-urlencoded', $body->contentType);
    }

    public function testGetBody(): void
    {
        // Simple test
        $body = new UrlencodedBody(['name' => 'John', 'age' => '30']);
        self::assertSame('name=John&age=30', $body->getBody());
    }

    public function testGetBodyWithCustomContentType(): void
    {
        // Simple test
        $body = new UrlencodedBody(['key' => 'value'], 'application/x-www-form-urlencoded');
        self::assertSame('application/x-www-form-urlencoded', $body->getContentType());
    }

    public function testGetBodyWithEmptyArray(): void
    {
        // Simple test
        $body = new UrlencodedBody([]);
        self::assertSame('', $body->getBody());
    }

    public function testGetBodyWithNestedArray(): void
    {
        // Simple test
        $body = new UrlencodedBody(['user' => ['name' => 'John', 'age' => '30']]);
        self::assertSame('user%5Bname%5D=John&user%5Bage%5D=30', $body->getBody());
    }

    public function testGetBodyWithNumericArray(): void
    {
        // Simple test
        $body = new UrlencodedBody(['items' => [1, 2, 3]]);
        self::assertSame('items%5B0%5D=1&items%5B1%5D=2&items%5B2%5D=3', $body->getBody());
    }

    public function testGetBodyWithObject(): void
    {
        // Simple test
        $data = new stdClass();
        $data->name = 'John';
        $data->age = '30';
        $body = new UrlencodedBody($data);
        self::assertSame('name=John&age=30', $body->getBody());
    }

    public function testGetBodyWithSpecialCharacters(): void
    {
        // Simple test
        $body = new UrlencodedBody(['param' => '& + &amp;']);
        self::assertSame('param=%26+%2B+%26amp%3B', $body->getBody());
    }

    public function testGetBodyWithUnicode(): void
    {
        // Simple test
        $body = new UrlencodedBody(['message' => 'Привет мир']);
        self::assertSame('message=%D0%9F%D1%80%D0%B8%D0%B2%D0%B5%D1%82+%D0%BC%D0%B8%D1%80', $body->getBody());
    }

    public function testGetContentType(): void
    {
        // Simple test
        $body = new UrlencodedBody(['key' => 'value']);
        self::assertSame('application/x-www-form-urlencoded', $body->getContentType());
    }
}
