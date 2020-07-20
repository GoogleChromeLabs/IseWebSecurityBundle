<?php

namespace Ise\WebSecurityBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Ise\WebSecurityBundle\EventSubscriber\RequestSubscriber;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class IseWebSecurityFetchMetaTest extends TestCase
{
    public function testFetchMetaData()
    {
        $requestSubscriber = new RequestSubscriber(new Container(
            new ParameterBag(["ise_security.fetch_metadata.active" => true])
        ));
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

    // public function testFetchMetaDataCrossOrigin() {
    //     $requestSubscriber = new RequestSubscriber(new Container(
    //         new ParameterBag(["ise_security.fetch_metadata.active" => true])
    //     ));

    //     $httpKernelInterfaceMock = $this->getMockBuilder(HttpKernelInterface::class)
    //         ->disableOriginalConstructor()
    //         ->getMock();

    //     $req = Request::create(
    //         '/blog'
    //     );

    //     $requestEvent = new RequestEvent(
    //         $httpKernelInterfaceMock,
    //         $req,
    //         HttpKernelInterface::MASTER_REQUEST,
    //     );
        
    // }
}
