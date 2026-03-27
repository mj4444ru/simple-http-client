# Simple Http Client

> [!NOTE]
> To use this software, make a copy of this package into your project.

> [!WARNING]
> The software is under development.
> It is strongly not recommended to connect it to yourself via composer or any other way.

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

require dirname(__DIR__) . '/vendor/autoload.php';

$client = new CurlHttpClient();
$request = new HttpRequest('http://www.google.com');
$response = $client->request($request);

$responseHttpCode = $response->getHttpCode();
$responseUrl = $response->getUrl();
$responseEffectiveUrl = $response->getEffectiveUrl();
$responseContentType = $response->getContentType();
$responseBody = $response->getBody();

echo $responseHttpCode .  PHP_EOL;
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
```

#### Json client

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

## Run tests

```shell
vendor/bin/codecept run
```
