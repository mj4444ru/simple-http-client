<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Helpers\UrlHelper;
use RuntimeException;

use function sprintf;

final class UrlHelperTest extends Unit
{
    public function testBase64UrlEncode(): void
    {
        // Simple test
        $str = UrlHelper::base64UrlEncode('Test');
        self::assertSame('VGVzdA', $str);
    }

    public function testExtractParamFromUrl(): void
    {
        // Simple test
        $param = UrlHelper::extractParamFromUrl('http://example.com/?foo=bar', 'foo');
        self::assertSame('bar', $param);

        // Simple test
        $param = UrlHelper::extractParamFromUrl('http://example.com/?foo=bar', 'bar');
        self::assertNull($param);
    }

    public function testExtractParamsFromUrl(): void
    {
        // Simple test
        $params = UrlHelper::extractParamsFromUrl('http://example.com');
        self::assertSame([], $params);

        // Simple test
        $params = UrlHelper::extractParamsFromUrl('http://example.com/?foo=bar');
        self::assertSame(['foo' => 'bar'], $params);
    }

    public function testExtractRequireParamFromUrl(): void
    {
        // Simple test
        $param = UrlHelper::extractRequireParamFromUrl('http://example.com/?foo=bar', 'foo');
        self::assertSame('bar', $param);

        // Simple test
        try {
            UrlHelper::extractRequireParamFromUrl('http://example.com/?foo=bar', 'bar');
            self::failException(RuntimeException::class);
        } catch (RuntimeException $e) {
            self::assertSame('Required parameter "bar" in url not found.', $e->getMessage());
        }
    }

    /**
     * @param class-string $className
     */
    private static function failException(string $className): never
    {
        self::fail(sprintf('Failed asserting that exception of type "%s" is thrown.', $className));
    }
}
