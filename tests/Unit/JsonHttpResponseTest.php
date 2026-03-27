<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\JsonDecodeException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\UnexpectedContentTypeException;
use Mj4444\SimpleHttpClient\HttpRequest\JsonHttpRequest;
use Mj4444\SimpleHttpClient\HttpResponse\JsonHttpResponse;
use stdClass;

use function sprintf;

final class JsonHttpResponseTest extends Unit
{
    public function testGetDataAssociative(): void
    {
        // Simple test
        $response = $this->createResponse('{"key":"value","nested":{"foo":"bar"}}');
        $data = $response->getData();
        self::assertIsArray($data);
        self::assertIsArray($data['nested'] ?? null);
        self::assertSame('value', $data['key'] ?? null);
        self::assertSame('bar', $data['nested']['foo'] ?? null);
    }

    public function testGetDataEmptyArray(): void
    {
        // Simple test
        $response = $this->createResponse('[]');
        $data = $response->getData();
        self::assertSame([], $data);
    }

    public function testGetDataEmptyObject(): void
    {
        // Simple test
        $response = $this->createResponse('{}');
        $data = $response->getData();
        self::assertSame([], $data);
    }

    public function testGetDataInvalidJson(): void
    {
        // Simple test
        $response = $this->createResponse('{invalid json}');
        try {
            $response->getData();
            self::failException(JsonDecodeException::class);
        } catch (JsonDecodeException $e) {
            self::assertSame('JSON decode error.', $e->getMessage());
        }
    }

    public function testGetDataNestedArray(): void
    {
        // Simple test
        $response = $this->createResponse('[{"id":1},{"id":2}]');
        $data = $response->getData();
        self::assertIsArray($data);
        self::assertIsArray($data[0] ?? null);
        self::assertIsArray($data[1] ?? null);
        self::assertSame(1, $data[0]['id'] ?? null);
        self::assertSame(2, $data[1]['id'] ?? null);
    }

    public function testGetDataObject(): void
    {
        // Simple test
        JsonHttpResponse::$decodeAssociative = false;
        $response = $this->createResponse('{"key":"value","nested":{"foo":"bar"}}');
        $data = $response->getData();
        self::assertIsObject($data);
        $nested = $data->nested ?? null;
        self::assertIsObject($nested);
        self::assertSame('value', $data->key ?? null);
        self::assertSame('bar', $nested->foo ?? null);
        JsonHttpResponse::$decodeAssociative = true;
    }

    public function testGetDataWithBooleanAndNull(): void
    {
        // Simple test
        $response = $this->createResponse('{"active":true,"deleted":false,"removed":null}');
        $data = $response->getData();
        self::assertIsArray($data);
        self::assertTrue($data['active'] ?? null);
        self::assertFalse($data['deleted'] ?? null);
        self::assertArrayHasKey('removed', $data);
        self::assertNull($data['removed']);
    }

    public function testGetDataWithDecodeDepth(): void
    {
        $originalDepth = JsonHttpResponse::$decodeDepth;
        JsonHttpResponse::$decodeDepth = 3;

        // Simple test
        $response = $this->createResponse('{"level2":{"level3":"value"}}');
        $data = $response->getData();
        self::assertIsArray($data);
        self::assertIsArray($data['level2'] ?? null);
        self::assertSame('value', $data['level2']['level3'] ?? null);

        // Simple test
        $response = $this->createResponse('{"level2":{"level3":{"level4":"value"}}}');
        try {
            $response->getData();
            self::failException(JsonDecodeException::class);
        } catch (JsonDecodeException $e) {
            self::assertSame('JSON decode error.', $e->getMessage());
        }

        JsonHttpResponse::$decodeDepth = $originalDepth;
    }

    public function testGetDataWithDecodeFlags(): void
    {
        // Simple test
        $originalFlags = JsonHttpResponse::$decodeFlags;
        JsonHttpResponse::$decodeFlags = JSON_BIGINT_AS_STRING;
        $response = $this->createResponse('{"bigint":92233720368547758075}');
        $data = $response->getData();
        self::assertIsArray($data);
        self::assertSame('92233720368547758075', $data['bigint'] ?? null);
        JsonHttpResponse::$decodeFlags = $originalFlags;
    }

    public function testGetDataWithNumbers(): void
    {
        // Simple test
        $response = $this->createResponse('{"int":42,"float":3.14,"negative":-10}');
        $data = $response->getData();
        self::assertIsArray($data);
        self::assertSame(42, $data['int'] ?? null);
        self::assertSame(3.14, $data['float'] ?? null);
        self::assertSame(-10, $data['negative'] ?? null);
    }

    public function testGetDataWithNumbersAsObject(): void
    {
        // Simple test
        JsonHttpResponse::$decodeAssociative = false;
        $response = $this->createResponse('{"int":42,"float":3.14,"negative":-10}');
        $data = $response->getData();
        self::assertIsObject($data);
        self::assertSame(42, $data->int ?? null);
        self::assertSame(3.14, $data->float ?? null);
        self::assertSame(-10, $data->negative ?? null);
        JsonHttpResponse::$decodeAssociative = true;
    }

    public function testGetDataWithOverrideAssociative(): void
    {
        // Simple test
        $response = $this->createResponse('{"key":"value"}');
        $data = $response->getData(false);
        self::assertInstanceOf(stdClass::class, $data);
        self::assertSame('value', $data->key);
    }

    public function testGetDataWithUnicode(): void
    {
        // Simple test
        $response = $this->createResponse('{"message":"Привет мир"}');
        $data = $response->getData();
        self::assertIsArray($data);
        self::assertSame('Привет мир', $data['message'] ?? null);
    }

    public function testGetDataWrongContentType(): void
    {
        // Simple test
        $response = $this->createResponse('{"key":"value"}', 'text/html');
        try {
            $response->getData();
            self::failException(UnexpectedContentTypeException::class);
        } catch (UnexpectedContentTypeException $e) {
            self::assertSame('Unexpected ContentType.', $e->getMessage());
        }
    }

    /**
     * @param non-empty-string $body
     * @param non-empty-string $contentType
     */
    private function createResponse(string $body, string $contentType = 'application/json'): JsonHttpResponse
    {
        return new JsonHttpResponse(
            new JsonHttpRequest('https://example.com'),
            200,
            '-',
            '-',
            null,
            [],
            $contentType,
            $body,
            ['application/json']
        );
    }

    /**
     * @param class-string $className
     */
    private static function failException(string $className): never
    {
        self::fail(sprintf('Failed asserting that exception of type "%s" is thrown.', $className));
    }
}
