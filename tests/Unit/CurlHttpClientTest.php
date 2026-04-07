<?php

declare(strict_types=1);

namespace Unit;

use Codeception\Test\Unit;
use CurlShareHandle;
use Mj4444\SimpleHttpClient\CurlHttpClient;
use Mj4444\SimpleHttpClient\Exceptions\CurlException;
use Mj4444\SimpleHttpClient\Exceptions\HttpRequest\BodyRequiredException;
use Mj4444\SimpleHttpClient\HttpRequest\HttpMethod;
use Mj4444\SimpleHttpClient\HttpRequest\HttpRequest;
use Mj4444\SimpleHttpClient\HttpResponse\HttpResponse;

use function sprintf;

final class CurlHttpClientTest extends Unit
{
    public function testBodyRequiredException(): void
    {
        // Simple test
        $client = new CurlHttpClient();
        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);

        try {
            $client->request($request);
            self::failException(BodyRequiredException::class);
        } catch (BodyRequiredException $e) {
            self::assertSame('Body required.', $e->getMessage());
            self::assertSame($request, $e->getRequest());
        }
    }

    public function testGetOptions(): void
    {
        // Simple test
        $client = new CurlHttpClient([CURLOPT_CONNECTTIMEOUT => 10]);
        self::assertEquals([CURLOPT_CONNECTTIMEOUT => 10], $client->getOptions());

        // Simple test
        $client = new CurlHttpClient([CURLOPT_CONNECTTIMEOUT => 10]);
        $options = $client->getOptions();
        self::assertEquals([CURLOPT_CONNECTTIMEOUT => 10], $options);
    }

    public function testInitShareData(): void
    {
        // Simple test
        $client = new CurlHttpClient();
        $client->initShareData(true);
        $options = $client->getOptions();
        self::assertArrayHasKey(CURLOPT_SHARE, $options);
        self::assertInstanceOf(CurlShareHandle::class, $options[CURLOPT_SHARE] ?? null);
    }

    public function testLastCurlInfo(): void
    {
        // Simple test
        $client = new CurlHttpClient();
        $client->curlInfoRequired = false;
        try {
            $client->request(new HttpRequest('nourl://nohost'));
            self::failException(CurlException::class);
        } catch (CurlException) {
        }
        self::assertNull($client->lastCurlInfo);

        // Simple test
        $client = new CurlHttpClient();
        $client->curlInfoRequired = false;
        try {
            $client->request(new HttpRequest('nourl://nohost'));
            self::failException(CurlException::class);
        } catch (CurlException) {
        }
        self::assertNull($client->lastCurlInfo);

        // Simple test
        $client = new CurlHttpClient();
        $client->curlInfoRequired = true;
        try {
            $client->request(new HttpRequest('nourl://nohost'));
            self::failException(CurlException::class);
        } catch (CurlException) {
        }
        self::assertIsArray($client->lastCurlInfo);
        self::assertArrayHasKey('url', $client->lastCurlInfo);
        self::assertSame('nourl://nohost/', $client->lastCurlInfo['url']);
    }

    public function testRequest(): void
    {
        // Simple test
        $client = $this->getMockBuilder(CurlHttpClient::class)
            ->onlyMethods(['execute'])
            ->getMock();

        $request = new HttpRequest('https://example.com');
        $response = new HttpResponse($request, 200, '-', '-', null, [], '-', '', null);

        $client->expects($this->once())
            ->method('execute')
            ->with(self::isArray(), self::equalTo($request))
            ->willReturn($response);

        $result = $client->request($request);
        self::assertSame($response, $result);

        // Simple test
        $client = $this->getMockBuilder(CurlHttpClient::class)
            ->onlyMethods(['execute'])
            ->getMock();

        $request = new HttpRequest('https://example.com', null, HttpMethod::Post);
        $request->setNoBody();
        $response = new HttpResponse($request, 200, '-', '-', null, [], '-', '', null);

        $client->expects($this->once())
            ->method('execute')
            ->with(self::isArray(), self::equalTo($request))
            ->willReturn($response);

        $result = $client->request($request);
        self::assertSame($response, $result);
    }

    public function testResetOptions(): void
    {
        // Simple test
        $client = new CurlHttpClient([CURLOPT_TIMEOUT => 30, CURLOPT_CONNECTTIMEOUT => 10]);
        $client->resetOptions();
        $options = $client->getOptions();
        self::assertEquals([], $options);
    }

    public function testSetConnectTimeout(): void
    {
        // Simple test
        $client = new CurlHttpClient();
        $options = $client->setConnectTimeout(10)->getOptions();
        self::assertEquals([CURLOPT_CONNECTTIMEOUT => 10], $options);
    }

    public function testSetFollowLocation(): void
    {
        // Simple test
        $client = new CurlHttpClient();
        $client->setFollowLocation(true);
        self::assertTrue($client->followLocation);

        // Simple test
        $client = new CurlHttpClient();
        $client->setFollowLocation(false);
        self::assertFalse($client->followLocation);
    }

    public function testSetHeader(): void
    {
        // Simple test
        $client = new CurlHttpClient();
        $client->setHeader(0, 'Content-Type: application/json');
        self::assertEquals(['Content-Type: application/json'], $client->headers);

        // Simple test
        $client = new CurlHttpClient();
        $client->setHeader('auth', 'Authorization: Bearer token');
        self::assertEquals(['auth' => 'Authorization: Bearer token'], $client->headers);
    }

    public function testSetHeaders(): void
    {
        // Simple test
        $client = new CurlHttpClient();
        $client->setHeaders(['Content-Type: application/json', 'Accept: application/json']);
        self::assertEquals(['Content-Type: application/json', 'Accept: application/json'], $client->headers);
    }

    public function testSetMaxRedirects(): void
    {
        // Simple test
        $client = new CurlHttpClient();
        $client->setMaxRedirects(5);
        self::assertSame(5, $client->maxRedirects);

        // Simple test
        $client = new CurlHttpClient();
        $client->setMaxRedirects(-1);
        self::assertSame(-1, $client->maxRedirects);
    }

    public function testSetOption(): void
    {
        // Simple test
        $client = new CurlHttpClient();
        $options = $client->setOption(CURLOPT_CONNECTTIMEOUT, 10)->getOptions();
        self::assertEquals([CURLOPT_CONNECTTIMEOUT => 10], $options);
    }

    public function testSetProxy(): void
    {
        // Prepare complex tests
        $client = new CurlHttpClient();

        // Complex test
        $client->setProxy('http://proxy.example.com:8080');
        $options = $client->getOptions();
        self::assertEquals([
            CURLOPT_PROXY => 'http://proxy.example.com:8080',
            CURLOPT_HTTPPROXYTUNNEL => true,
            CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
        ], $options);

        // Complex test
        $client->setProxy('socks5://proxy.example.com:1080', true);
        $options = $client->getOptions();
        self::assertEquals(CURLPROXY_SOCKS5, $options[CURLOPT_PROXYTYPE] ?? null);

        // Complex test
        $client->setProxy('');
        $options = $client->getOptions();
        self::assertArrayNotHasKey(CURLOPT_PROXY, $options);
    }

    public function testSetReferer(): void
    {
        // Simple test
        $client = new CurlHttpClient();
        $options = $client->setReferer('https://example.com')->getOptions();
        self::assertEquals([CURLOPT_REFERER => 'https://example.com'], $options);

        // Simple test
        $client = new CurlHttpClient();
        $options = $client->setReferer(null)->getOptions();
        self::assertEquals([CURLOPT_REFERER => null], $options);
    }

    public function testSetResponseHeadersRequired(): void
    {
        // Simple test
        $client = new CurlHttpClient();
        $client->setResponseHeadersRequired(true);
        self::assertTrue($client->responseHeadersRequired);

        // Simple test
        $client = new CurlHttpClient();
        $client->setResponseHeadersRequired(false);
        self::assertFalse($client->responseHeadersRequired);
    }

    public function testSetTimeout(): void
    {
        // Simple test
        $client = new CurlHttpClient();
        $options = $client->setTimeout(30)->getOptions();
        self::assertEquals([CURLOPT_TIMEOUT => 30], $options);

        // Simple test
        $client = new CurlHttpClient();
        $options = $client->setTimeout(null)->getOptions();
        self::assertEquals([CURLOPT_TIMEOUT => null], $options);
    }

    public function testSetUsePersistentCurlHandle(): void
    {
        // Simple test
        $client = new CurlHttpClient();
        $client->setUsePersistentCurlHandle(true);
        self::assertTrue($client->usePersistentCurlHandle);

        // Simple test
        $client = new CurlHttpClient();
        $client->setUsePersistentCurlHandle(false);
        self::assertFalse($client->usePersistentCurlHandle);
    }

    public function testSetUserAgent(): void
    {
        // Simple test
        $client = new CurlHttpClient();
        $options = $client->setUserAgent('MyApp/1.0')->getOptions();
        self::assertEquals([CURLOPT_USERAGENT => 'MyApp/1.0'], $options);

        // Simple test
        $client = new CurlHttpClient();
        $options = $client->setUserAgent(null)->getOptions();
        self::assertEquals([CURLOPT_USERAGENT => null], $options);
    }

    public function testUnsetOption(): void
    {
        // Simple test
        $client = new CurlHttpClient([CURLOPT_TIMEOUT => 30, CURLOPT_CONNECTTIMEOUT => 10]);
        $client->unsetOption(CURLOPT_TIMEOUT);
        $options = $client->getOptions();
        self::assertEquals([CURLOPT_CONNECTTIMEOUT => 10], $options);
    }

    /**
     * @param class-string $className
     */
    private static function failException(string $className): never
    {
        self::fail(sprintf('Failed asserting that exception of type "%s" is thrown.', $className));
    }
}
