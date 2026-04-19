<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Exceptions\HttpRequest\ReaderException;
use Mj4444\SimpleHttpClient\HttpRequest\Body\BodyReader\StringReader;
use Random\RandomException;

use function sprintf;
use function strlen;

final class StringReaderTest extends Unit
{
    public function testGetBytesLeft(): void
    {
        // Simple test
        $reader = new StringReader('test content');
        self::assertSame(12, $reader->getBytesLeft());
        self::assertSame(10, strlen($reader->read(10)));
        self::assertSame(2, $reader->getBytesLeft());
        self::assertSame(2, strlen($reader->read(10)));
        self::assertSame(0, $reader->getBytesLeft());
        self::assertSame(0, strlen($reader->read(10)));
    }

    public function testProgressCallback(): void
    {
        $content = 'test content for progress callback';
        $totalBytes = strlen($content);
        /** @var list<array{bytesSent: int, totalBytes: int}> $calls */
        $calls = [];

        $reader = new StringReader(
            $content,
            progressCallback: static function (int $bytesSent, int $totalBytes) use (&$calls): void {
                $calls[] = ['bytesSent' => $bytesSent, 'totalBytes' => $totalBytes];
            }
        );

        $reader->read(10);
        $reader->read(10);
        $reader->read($totalBytes);

        self::assertCount(3, $calls);
        /** @psalm-suppress PossiblyUndefinedArrayOffset */
        self::assertSame(['bytesSent' => 10, 'totalBytes' => $totalBytes], $calls[0]);
        self::assertSame(['bytesSent' => 20, 'totalBytes' => $totalBytes], $calls[1]);
        self::assertSame(['bytesSent' => $totalBytes, 'totalBytes' => $totalBytes], $calls[2]);
    }

    /**
     * @throws RandomException
     */
    public function testRead(): void
    {
        $bytes = bin2hex(random_bytes(500));

        // Simple test
        $this->testReadWithParams($bytes, $bytes);

        // Simple test
        $this->testReadWithParams($bytes, substr($bytes, 500), 500);

        // Simple test
        $this->testReadWithParams($bytes, substr($bytes, 0, 500), null, 500);

        // Simple test
        $this->testReadWithParams($bytes, substr($bytes, 250, 500), 250, 500);

        // Simple test
        $this->testReadWithParams($bytes, substr($bytes, 250, -250), 250, -250);

        // Simple test
        $this->testReadWithParams($bytes, '', 500, -500);

        // Simple test
        try {
            /** @psalm-suppress InvalidArgument */
            $this->testReadWithParams('', '', -1);
            self::failException(ReaderException::class);
        } catch (ReaderException $e) {
            self::assertSame('Offset cannot be negative.', $e->getMessage());
        }

        // Simple test
        try {
            $this->testReadWithParams($bytes, '', 500, -600);
            self::failException(ReaderException::class);
        } catch (ReaderException $e) {
            self::assertSame('A negative data length value was received.', $e->getMessage());
        }

        try {
            $this->testReadWithParams('', '', 500);
            self::failException(ReaderException::class);
        } catch (ReaderException $e) {
            self::assertSame('Invalid offset.', $e->getMessage());
        }
    }

    /**
     * @param class-string $className
     */
    private static function failException(string $className): never
    {
        self::fail(sprintf('Failed asserting that exception of type "%s" is thrown.', $className));
    }

    /**
     * @param non-negative-int|null $offset
     */
    private function testReadWithParams(
        string $bytes,
        string $expectedBytes,
        ?int $offset = null,
        ?int $length = null
    ): void {
        $expectedByteCount = strlen($expectedBytes);

        $reader = new StringReader($bytes, $offset ?? 0, $length);

        $readData = '';
        while (strlen($readData) < $expectedByteCount) {
            $rd = $reader->read(300);
            $readData .= $rd;

            if (strlen($readData) < $expectedByteCount) {
                self::assertEquals(min(300, $expectedByteCount - strlen($readData) + strlen($rd)), strlen($rd));
            }
        }

        self::assertEquals(strlen($readData), $expectedByteCount);
        self::assertSame($readData, $expectedBytes);

        $rd = $reader->read(300);
        self::assertEquals(0, strlen($rd));

        self::assertEquals(0, $reader->getBytesLeft());
    }
}
