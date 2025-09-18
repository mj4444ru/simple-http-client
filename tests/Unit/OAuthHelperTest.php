<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use Mj4444\SimpleHttpClient\Helpers\OAuthHelper;

use function strlen;

/**
 * @api
 */
final class OAuthHelperTest extends Unit
{
    public function testComputeCodeChallenge(): void
    {
        // Simple test
        $codeVerifier = 'wS-Ed-me0tTq4sROIzqp0Mm00BNRtF4Bxd_3sOySqGR';
        $computeCodeChallenge = OAuthHelper::computeCodeChallenge($codeVerifier);
        $this->assertSame('7U72YtL4Z3Q_prqWyfm5UBZaXG4Q52HTimlRN9dnaPk', $computeCodeChallenge);
    }

    public function testGenerateCodeVerifier(): void
    {
        // Simple test
        $value = OAuthHelper::generateCodeVerifier();
        $this->assertSame(43, strlen($value));

        // Simple test
        $value1 = OAuthHelper::generateCodeVerifier();
        $value2 = OAuthHelper::generateCodeVerifier();
        $this->assertNotSame($value1, $value2);
    }

    public function testGenerateNonce(): void
    {
        // Simple test
        $value = OAuthHelper::generateNonce();
        $this->assertSame(60, strlen($value));

        // Simple test
        $value1 = OAuthHelper::generateNonce();
        $value2 = OAuthHelper::generateNonce();
        $this->assertNotSame($value1, $value2);
    }

    public function testGenerateRandomHexString(): void
    {
        // Simple test
        $value = OAuthHelper::generateRandomHexString(15);
        $this->assertSame(15, strlen($value));

        // Simple test
        $value = OAuthHelper::generateRandomHexString(16);
        $this->assertSame(16, strlen($value));

        // Simple test
        $value1 = OAuthHelper::generateRandomHexString(60);
        $value2 = OAuthHelper::generateRandomHexString(60);
        $this->assertNotSame($value1, $value2);
    }

    public function testGenerateRandomPKCEString(): void
    {
        // Simple test
        $value = OAuthHelper::generateRandomPKCEString();
        $this->assertSame(43, strlen($value));

        // Simple test
        $value1 = OAuthHelper::generateRandomPKCEString();
        $value2 = OAuthHelper::generateRandomPKCEString();
        $this->assertNotSame($value1, $value2);
    }

    public function testGenerateState(): void
    {
        // Simple test
        $value = OAuthHelper::generateState();
        $this->assertSame(60, strlen($value));

        // Simple test
        $value1 = OAuthHelper::generateState();
        $value2 = OAuthHelper::generateState();
        $this->assertNotSame($value1, $value2);
    }
}
