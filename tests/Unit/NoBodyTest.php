<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\HttpRequest\Body\NoBody;

final class NoBodyTest extends Unit
{
    public function testGetBody(): void
    {
        // Simple test
        $body = new NoBody();
        self::assertSame('', $body->getBody());
    }

    public function testGetContentType(): void
    {
        // Simple test
        $body = new NoBody();
        self::assertNull($body->getContentType());
    }
}
