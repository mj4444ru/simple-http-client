<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Exceptions\ReaderException;
use Mj4444\SimpleHttpClient\HttpRequest\Body\BodyReader\FileReader;

use function sprintf;

final class FileReaderTest extends Unit
{
    public function testConstructor(): void
    {
        // Simple test
        $reader = new FileReader(__FILE__);
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(FileReader::class, $reader);
    }

    public function testConstructorWithNonExistentFile(): void
    {
        // Simple test
        try {
            new FileReader('/non/existent/file.txt');
            self::failException(ReaderException::class);
        } catch (ReaderException $e) {
            self::assertStringContainsString('Unable to open file:', $e->getMessage());
        }
    }

    public function testConstructorWithOffsetAndLength(): void
    {
        // Simple test
        $reader = new FileReader(__FILE__, 100, -100);
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertInstanceOf(FileReader::class, $reader);
        /** @psalm-suppress ArgumentTypeCoercion, PossiblyFalseArgument */
        $content = $reader->read(filesize(__FILE__));
        /** @psalm-suppress PossiblyFalseArgument */
        self::assertSame($content, substr(file_get_contents(__FILE__), 100, -100));
    }

    public function testGetBytesLeft(): void
    {
        // Simple test
        $reader = new FileReader(__FILE__);
        self::assertSame(filesize(__FILE__), $reader->getBytesLeft());
    }

    public function testGetBytesLeftWithOffset(): void
    {
        // Simple test
        $reader = new FileReader(__FILE__, 100);
        /** @psalm-suppress PossiblyFalseOperand */
        self::assertSame(filesize(__FILE__) - 100, $reader->getBytesLeft());
    }

    public function testGetBytesLeftWithOffsetAndLength(): void
    {
        // Simple test
        $reader = new FileReader(__FILE__, 100, 100);
        self::assertSame(100, $reader->getBytesLeft());
    }

    public function testProgressCallback(): void
    {
        // Simple test
        $fileSize = filesize(__FILE__);
        self::assertNotFalse($fileSize);
        /** @var list<array{bytesSent: int, totalBytes: int}> $calls */
        $calls = [];

        $reader = new FileReader(
            __FILE__,
            progressCallback: static function (int $bytesSent, int $totalBytes) use (&$calls): void {
                $calls[] = ['bytesSent' => $bytesSent, 'totalBytes' => $totalBytes];
            }
        );

        $reader->read(100);
        $reader->read(200);
        /** @psalm-suppress ArgumentTypeCoercion */
        $reader->read($fileSize);

        self::assertCount(3, $calls);
        /** @psalm-suppress PossiblyUndefinedArrayOffset */
        self::assertSame(['bytesSent' => 100, 'totalBytes' => $fileSize], $calls[0]);
        self::assertSame(['bytesSent' => 300, 'totalBytes' => $fileSize], $calls[1]);
        self::assertSame(['bytesSent' => $fileSize, 'totalBytes' => $fileSize], $calls[2]);
    }

    public function testRead(): void
    {
        // Simple test
        $reader = new FileReader(__FILE__);

        /** @psalm-suppress ArgumentTypeCoercion, PossiblyFalseArgument */
        $data = $reader->read(filesize(__FILE__));
        self::assertStringEqualsFile(__FILE__, $data);
        self::assertSame(0, $reader->getBytesLeft());
    }

    /**
     * @param class-string $className
     */
    private static function failException(string $className): never
    {
        self::fail(sprintf('Failed asserting that exception of type "%s" is thrown.', $className));
    }
}
