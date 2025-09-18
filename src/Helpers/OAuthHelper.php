<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Helpers;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

use function strlen;

final class OAuthHelper
{
    public static function computeCodeChallenge(string $verifierCode): string
    {
        return UrlHelper::base64UrlEncode(hash('sha256', $verifierCode, true));
    }

    /**
     * @param positive-int $length
     */
    public static function generateCodeVerifier(int $length = 43): string
    {
        return self::generateRandomPKCEString($length);
    }

    /**
     * @param positive-int $length
     */
    public static function generateNonce(int $length = 43): string
    {
        return base64_encode(self::generateRandomPKCEString($length));
    }

    /**
     * @param positive-int $length
     */
    public static function generateRandomHexString(int $length): string
    {
        try {
            if ($length & 1) {
                /** @psalm-suppress ArgumentTypeCoercion */
                return substr(bin2hex(random_bytes(($length >> 1) + 1)), 0, $length);
            }

            /** @psalm-suppress ArgumentTypeCoercion */
            return bin2hex(random_bytes($length >> 1));
        } catch (Throwable $e) {
            throw new RuntimeException('Unable to generate random string.', 0, $e);
        }
    }

    /**
     * @param positive-int $length
     */
    public static function generateRandomPKCEString(int $length = 43): string
    {
        if ($length < 43 || $length > 128) {
            throw new InvalidArgumentException(
                'The length of the code verifier must be between 43 and 128. See https://tools.ietf.org/html/rfc7636#section-4.1'
            );
        }

        $characterSet = /** @lang IgnoreLang */
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~';
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            try {
                $result .= $characterSet[random_int(0, strlen($characterSet) - 1)];
            } catch (Throwable $e) {
                throw new RuntimeException('Unable to generate random string.', 0, $e);
            }
        }

        return $result;
    }

    /**
     * @param positive-int $length
     */
    public static function generateState(int $length = 43): string
    {
        return base64_encode(self::generateRandomPKCEString($length));
    }
}
