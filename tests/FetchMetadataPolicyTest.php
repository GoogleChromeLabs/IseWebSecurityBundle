<?php

namespace Ise\WebSecurityBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Ise\WebSecurityBundle\EventSubscriber\FetchMetadataRequestSubscriber;
use Ise\WebSecurityBundle\Policies\FetchMetadataPolicyProvider;
use Ise\WebSecurityBundle\Options\ConfigProvider;
use Ise\WebSecurityBundle\Policies\FetchMetadataDefaultPolicy;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class IseWebSecurityFetchMetadataPolicyTest extends TestCase
{
    private $defaults = [
        'fetch_metadata' => [
            'active' => true,
            'policy' => null,
            'allowed_endpoints' => []
        ]
    ];

    public function testFetchMetaDataSubscriber()
    {
        $fetchMetaPolicy = new FetchMetadataDefaultPolicy([]);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestSubscriber = new FetchMetadataRequestSubscriber(
            new FetchMetadataPolicyProvider,
            new ConfigProvider($this->defaults, []),
            $logger
        );
        
        $req = Request::create(
            '/blog'
        );

        $kernel = $this->getMockBuilder(HttpKernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = Request::create('/test');
        $res = new RequestEvent(
            $kernel,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $result = $requestSubscriber->requestEvent($res);
        $this->assertNull($result);
    }

    public function testBCInPolicy()
    {
        $fetchMetaPolicy = new FetchMetadataDefaultPolicy([]);

        $request = Request::create('/test');

        $res = $fetchMetaPolicy->applyPolicy($request);
        $this->assertTrue($res);
    }

    public function testSameOriginInPolicy()
    {
        $fetchMetaPolicy = new FetchMetadataDefaultPolicy([]);
        $request = Request::create('/test');

        $request->headers->set('sec-fetch-site', 'same-site');
        $res = $fetchMetaPolicy->applyPolicy($request);
        $this->assertTrue($res);

        $request->headers->set('sec-fetch-site', 'same-origin', true);
        $res = $fetchMetaPolicy->applyPolicy($request);
        $this->assertTrue($res);

        $request->headers->set('sec-fetch-site', 'none', true);
        $res = $fetchMetaPolicy->applyPolicy($request);
        $this->assertTrue($res);
    }

    public function testAllowTopLevelNavigation()
    {
        $fetchMetaPolicy = new FetchMetadataDefaultPolicy([]);

        $request = Request::create('/test');
        $request->headers->set('sec-fetch-site', 'cross-origin');
        $request->headers->set('sec-fetch-mode', 'navigate');
        $request->headers->set('sec-fetch-dest', 'image');
        $request->setMethod('GET');

        $res = $fetchMetaPolicy->applyPolicy($request);
        $this->assertTrue($res);
    }

    public function testDisallowPostRequest()
    {
        $fetchMetaPolicy = new FetchMetadataDefaultPolicy([]);

        $request = Request::create('/test');
        $request->headers->set('sec-fetch-site', 'cross-origin');
        $request->headers->set('sec-fetch-mode', 'navigate');
        $request->headers->set('sec-fetch-dest', 'image');
        $request->setMethod('POST');

        $res = $fetchMetaPolicy->applyPolicy($request);
        $this->assertFalse($res);
    }

    public function testDisallowObjectEmbedRequests()
    {
        $fetchMetaPolicy = new FetchMetadataDefaultPolicy([]);

        $request = Request::create('/test');
        $request->headers->set('sec-fetch-site', 'cross-origin');
        $request->headers->set('sec-fetch-mode', 'navigate');
        $request->headers->set('sec-fetch-dest', 'object');
        $request->setMethod('GET');

        $res = $fetchMetaPolicy->applyPolicy($request);
        $this->assertFalse($res);

        $request = Request::create('/test');
        $request->headers->set('sec-fetch-site', 'cross-origin', true);
        $request->headers->set('sec-fetch-mode', 'navigate', true);
        $request->headers->set('sec-fetch-dest', 'embed', true);
        $request->setMethod('GET');

        $res = $fetchMetaPolicy->applyPolicy($request);
        $this->assertFalse($res);
    }

    public function testDisallowNonNavRequests()
    {
        $fetchMetaPolicy = new FetchMetadataDefaultPolicy([]);

        $request = Request::create('/test');
        $request->headers->set('sec-fetch-site', 'cross-origin');
        $request->headers->set('sec-fetch-mode', 'websocket');
        $request->headers->set('sec-fetch-dest', 'object');
        $request->setMethod('GET');

        $res = $fetchMetaPolicy->applyPolicy($request);
        $this->assertFalse($res);

        $request = Request::create('/test');
        $request->headers->set('sec-fetch-site', 'cross-origin', true);
        $request->headers->set('sec-fetch-mode', 'cors', true);
        $request->headers->set('sec-fetch-dest', 'embed', true);
        $request->setMethod('GET');

        $res = $fetchMetaPolicy->applyPolicy($request);
        $this->assertFalse($res);
    }

    public function testAllowedEndpointsRequest()
    {
        $fetchMetaPolicy = new FetchMetadataDefaultPolicy(['/allowedEndpoint']);

        $request = Request::create('/allowedEndpoint');
        $request->headers->set('sec-fetch-site', 'cross-origin');
        $request->headers->set('sec-fetch-mode', 'websocket');
        $request->headers->set('sec-fetch-dest', 'object');
        $request->setMethod('GET');

        $res = $fetchMetaPolicy->applyPolicy($request);
        $this->assertTrue($res);
    }
}
