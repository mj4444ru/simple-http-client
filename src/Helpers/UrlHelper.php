<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\Helpers;

use RuntimeException;

use function is_string;
use function sprintf;

final class UrlHelper
{
    public static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function extractParamFromUrl(string $url, string $name): string|array|null
    {
        $params = self::extractParamsFromUrl($url);

        return $params[$name] ?? null;
    }

    /**
     * @return array<string|array>
     */
    public static function extractParamsFromUrl(string $url): array
    {
        $query = parse_url($url, PHP_URL_QUERY);
        if (!is_string($query)) {
            if ($query === null) {
                return [];
            }

            throw new RuntimeException('Invalid url.');
        }

        parse_str($query, $params);
        /** @var array<string|array> $params */

        return $params;
    }

    public static function extractRequireParamFromUrl(string $url, string $name): string|array
    {
        $value = self::extractParamFromUrl($url, $name);

        if ($value === null) {
            throw new RuntimeException(sprintf('Required parameter "%s" in url not found.', $name));
        }

        return $value;
    }
}
