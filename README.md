# Simple Http Client

This client makes complex HTTP requests easy.

It's useful for building API clients, various parsers, and handling complex multi-step request workflows.

## Key Features

- The client, request, and response are fully abstract and interface-based.
- A set of ready-to-use classes simplifies working with any types of sent and received data.
- Tools for working with streaming data (upload, download).
- A dedicated client for JSON responses.
- Using your own implementations of request and response interfaces keeps your code organized.

> [!WARNING]
> Versions of this package earlier than "v1" may be incompatible.
> When connecting, please specify the exact package version to avoid automatic updates.

## Installation

### Via composer

```shell
composer require --prefer-dist mj4444/simple-http-client
```

### By cloning the code

```shell
git clone https://github.com/mj4444ru/simple-http-client.git
```

Add the following lines to the `composer.json` file:

```
"autoload": {
    "psr-4": {
        "Mj4444\\SimpleHttpClient\\": "simple-http-client/src/"
    }
}
```

## Examples

### Simple requests

#### Get request

```php
use Mj4444\SimpleHttpClient\CurlHttpClient;
use Mj4444\SimpleHttpClient\HttpRequest\HttpRequest;

$client = new CurlHttpClient();
$request = new HttpRequest('http://www.google.com');
$response = $client->request($request);

$responseHttpCode = $response->getHttpCode();
$responseUrl = $response->getUrl();
$responseEffectiveUrl = $response->getEffectiveUrl();
$responseContentType = $response->getContentType();
$responseBody = $response->getBody();

echo $responseHttpCode . PHP_EOL;
echo $responseUrl . PHP_EOL;
echo $responseEffectiveUrl . PHP_EOL;
echo $responseContentType . PHP_EOL;
echo PHP_EOL . $responseBody . PHP_EOL . PHP_EOL;
```

#### Get request with query

```php
use Mj4444\SimpleHttpClient\HttpRequest\HttpRequest;

$request = new HttpRequest('http://www.google.com/search?q=demo');
```

```php
use Mj4444\SimpleHttpClient\HttpRequest\HttpRequest;

$request = new HttpRequest('http://www.google.com/search', ['q' => 'demo']);
```

#### Post request

```php
use Mj4444\SimpleHttpClient\HttpRequest\Body\MultipartBody\File;
use Mj4444\SimpleHttpClient\HttpRequest\Body\MultipartBody\StringFile;
use Mj4444\SimpleHttpClient\HttpRequest\HttpMethod;
use Mj4444\SimpleHttpClient\HttpRequest\HttpRequest;

$request = new HttpRequest('http://www.google.com', null, HttpMethod::Post);

$request->setUrlencodedBody(['q' => 'demo']);

$request->setMultipartFormBody([
    'field1' => 'value1',
    'field2' => 2,
    'field3' => new File($fileName, $postName, $mime),
    'field4' => new StringFile($data, $postName, $mime),
]);

$request->setJsonBody('demo');

$request->setStringBody(http_build_query(['q' => 'demo']), 'application/x-www-form-urlencoded');

$request->setNoBody();

$request->setFileBody(...);

$request->setStreamBody(...);

$request->setStringStreamBody(...);
```

#### JSON client

```php
use Mj4444\SimpleHttpClient\CurlHttpClient;
use Mj4444\SimpleHttpClient\HttpRequest\Body\NoBody;
use Mj4444\SimpleHttpClient\HttpRequest\Body\UrlencodedBody;
use Mj4444\SimpleHttpClient\JsonHttpClient;

$client = new JsonHttpClient(new CurlHttpClient());

$data = $client->get('https://example.com', ['q' => 'demo']);

$data = $client->post('https://example.com', ['body' => 'demo'], ['q' => 'demo']);

$data = $client->post('https://example.com', new NoBody());

$data = $client->post('https://example.com', new UrlencodedBody([['q' => 'demo']]));
```

#### Extended request

```php
use Mj4444\SimpleHttpClient\CurlHttpClient;
use Mj4444\SimpleHttpClient\HttpRequest\HttpRequestEx;

$client = new CurlHttpClient();
$fp = fopen('php://temp', 'rb+');
$request = new HttpRequestEx('https://google.com');
$request->setFollowLocation(true);
$request->setResourceForResponseBody($fp);
$progressCallback = static function ($bytesToDownload, $bytesDownloaded, $bytesToUpload, $bytesUploaded): bool {
    echo sprintf("%d / %d -- %d / %d\n", $bytesToDownload, $bytesDownloaded, $bytesToUpload, $bytesUploaded);

    return true;
};
$request->setProgressCallback($progressCallback);
$response = $client->request($request);
$response->checkHttpCode(200);
```

## Supported Body

- [FileBody](src/HttpRequest/Body/FileBody.php)
- [JsonBody](src/HttpRequest/Body/JsonBody.php)
- [MultipartFormBody](src/HttpRequest/Body/MultipartFormBody.php)
    - [File](src/HttpRequest/Body/MultipartBody/File.php)
    - [StringFile](src/HttpRequest/Body/MultipartBody/StringFile.php)
- [NoBody](src/HttpRequest/Body/NoBody.php)
- [StreamBody](src/HttpRequest/Body/StreamBody.php)
- [StringBody](src/HttpRequest/Body/StringBody.php)
- [StringStreamBody](src/HttpRequest/Body/StringStreamBody.php)
- [UrlencodedBody](src/HttpRequest/Body/UrlencodedBody.php)
- Your Body Implementations

## Run tests

```shell
vendor/bin/codecept run
```
