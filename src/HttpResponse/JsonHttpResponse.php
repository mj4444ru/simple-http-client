<?php

declare(strict_types=1);

namespace Mj4444\SimpleHttpClient\HttpResponse;

use JsonException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\JsonDecodeException;
use Mj4444\SimpleHttpClient\Exceptions\HttpResponse\UnexpectedContentTypeException;

class JsonHttpResponse extends HttpResponse
{
    public static ?bool $decodeAssociative = true;
    /**
     * @var int<1,256>
     */
    public static int $decodeDepth = 16;
    public static int $decodeFlags = 0;

    /**
     * @inheritDoc
     *
     * @throws UnexpectedContentTypeException
     * @throws JsonDecodeException
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getData(): mixed
    {
        $this->checkContentType();

        try {
            return static::jsonDecode($this->body);
        } catch (JsonException $e) {
            throw new JsonDecodeException($this, $e);
        }
    }

    /**
     * @throws JsonException
     */
    protected static function jsonDecode(string $body): mixed
    {
        return json_decode(
            $body,
            self::$decodeAssociative,
            self::$decodeDepth,
            self::$decodeFlags | JSON_THROW_ON_ERROR
        );
    }
}
