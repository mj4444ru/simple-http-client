<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpRequest\Body\BodyReader;

use Mj4444\SimpleHttpClient\Exceptions\ReaderException;

use function sprintf;

/**
 * @internal
 */
final class FileReader extends StreamReader
{
    /**
     * @param non-empty-string $fileName
     * @param non-negative-int $offset
     */
    public function __construct(
        string $fileName,
        int $offset = 0,
        ?int $length = null
    ) {
        $resource = @fopen($fileName, 'rb');

        if (!$resource) {
            throw new ReaderException(sprintf('Unable to open file: %s', $fileName));
        }

        parent::__construct($resource, $offset, $length);
    }
}
