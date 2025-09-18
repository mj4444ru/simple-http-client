<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpResponse;

use JsonException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\JsonDecodeException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\UnexpectedContentTypeException;
use Mj4444\SimpleHttpClient\HttpRequest\JsonHttpRequest;

/**
 * @extends BaseHttpResponse<JsonHttpRequest>
 */
final class JsonHttpResponse extends BaseHttpResponse
{
    public static ?bool $decodeAssociative = true;
    /**
     * @var int<1,256>
     */
    public static int $decodeDepth = 16;
    public static int $decodeFlags = 0;

    /**
     * @throws UnexpectedContentTypeException
     * @throws JsonDecodeException
     */
    public function getData(?bool $associative = null): mixed
    {
        $this->checkContentType();

        try {
            return json_decode(
                $this->body,
                $associative ?? self::$decodeAssociative,
                self::$decodeDepth,
                self::$decodeFlags | JSON_THROW_ON_ERROR
            );
        } catch (JsonException $e) {
            /** @psalm-var BaseHttpResponse $this Psalm bug? https://github.com/vimeo/psalm/issues/11562 */
            throw new JsonDecodeException($this, $e);
        }
    }
}
