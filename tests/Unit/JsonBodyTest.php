<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use JsonException;
use Mj4444\SimpleHttpClient\Exceptions\HttpRequest\JsonEncodeException;
use Mj4444\SimpleHttpClient\HttpRequest\Body\JsonBody;
use stdClass;

use function sprintf;

final class JsonBodyTest extends Unit
{
    public function testConstructor(): void
    {
        // Simple test
        $body = new JsonBody(['key' => 'value']);
        self::assertSame(['key' => 'value'], $body->data);
        self::assertSame('application/json; charset=utf-8', $body->contentType);

        // Simple test
        $body = new JsonBody(['key' => 'value'], 'application/json');
        self::assertSame('application/json', $body->contentType);
    }

    public function testGetBody(): void
    {
        // Simple test
        $body = new JsonBody(['key' => 'value']);
        self::assertSame('{"key":"value"}', $body->getBody());

        // Simple test
        $body = new JsonBody(['int' => 42, 'float' => 3.14, 'bool' => true]);
        self::assertSame('{"int":42,"float":3.14,"bool":true}', $body->getBody());
    }

    public function testGetBodyInvalidData(): void
    {
        // Simple test
        $resource = fopen('php://temp', 'rb');
        self::assertNotFalse($resource);
        try {
            /** @psalm-suppress UnusedFunctionCall */
            json_encode($resource, JSON_THROW_ON_ERROR);
            self::failException(JsonException::class);
        } catch (JsonException $e) {
            $errText = $e->getMessage();
        }
        $body = new JsonBody($resource);
        try {
            $body->getBody();
            self::failException(JsonEncodeException::class);
        } catch (JsonEncodeException $e) {
            self::assertEquals($errText, $e->getMessage());
            self::assertInstanceOf(JsonException::class, $e->getPrevious());
        }
        fclose($resource);
    }

    public function testGetBodyWithCustomFlags(): void
    {
        // Simple test
        $originalFlags = JsonBody::$encodeFlags;
        JsonBody::$encodeFlags = JSON_PRETTY_PRINT;
        $body = new JsonBody(['key' => 'value']);
        self::assertSame("{\n    \"key\": \"value\"\n}", $body->getBody());
        JsonBody::$encodeFlags = $originalFlags;
    }

    public function testGetBodyWithEmptyArray(): void
    {
        // Simple test
        $body = new JsonBody([]);
        self::assertSame('[]', $body->getBody());
    }

    public function testGetBodyWithEmptyObject(): void
    {
        // Simple test
        $body = new JsonBody(new stdClass());
        self::assertSame('{}', $body->getBody());
    }

    public function testGetBodyWithNestedData(): void
    {
        // Simple test
        $body = new JsonBody(['user' => ['name' => 'John', 'age' => 30]]);
        self::assertSame('{"user":{"name":"John","age":30}}', $body->getBody());
    }

    public function testGetBodyWithNull(): void
    {
        // Simple test
        $body = new JsonBody(null);
        self::assertSame('null', $body->getBody());
    }

    public function testGetBodyWithNumericArray(): void
    {
        // Simple test
        $body = new JsonBody([1, 2, 3]);
        self::assertSame('[1,2,3]', $body->getBody());
    }

    public function testGetBodyWithUnicode(): void
    {
        // Simple test
        $body = new JsonBody(['message' => 'Привет мир']);
        self::assertSame('{"message":"Привет мир"}', $body->getBody());
    }

    public function testGetContentType(): void
    {
        // Simple test
        $body = new JsonBody(['key' => 'value']);
        self::assertSame('application/json; charset=utf-8', $body->getContentType());

        // Simple test
        $body = new JsonBody(['key' => 'value'], 'application/json');
        self::assertSame('application/json', $body->getContentType());
    }

    /**
     * @param class-string $className
     */
    private static function failException(string $className): never
    {
        self::fail(sprintf('Failed asserting that exception of type "%s" is thrown.', $className));
    }
}
