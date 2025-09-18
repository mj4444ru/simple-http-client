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
$request = new HttpRequest('http://www.google.com/search?q=demo');
```

```php
$request = new HttpRequest('http://www.google.com/search', ['q' => 'demo']);
```

#### Post request

```php
$request = new HttpRequest('http://www.google.com', null, HttpMethod::Post);

$request->setUrlencodedBody(['q' => 'demo']);

$request->setJsonBody('demo');

$request->setNoBody();

$request->setBody(http_build_query(['q' => 'demo']))
    ->setContentType('application/x-www-form-urlencoded');
```

#### Json client

```php
use Mj4444\SimpleHttpClient\CurlHttpClient;
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
