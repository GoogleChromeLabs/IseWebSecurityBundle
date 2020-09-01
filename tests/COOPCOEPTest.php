<?php

namespace Ise\WebSecurityBundle\Tests;

use Ise\WebSecurityBundle\EventSubscriber\ResponseSubscriber;
use Ise\WebSecurityBundle\Options\ConfigProvider;
use Ise\WebSecurityBundle\Options\ContextChecker;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class COOPCOEPTest extends TestCase
{
    private $default = [
        "coop" => [
            "active" => true,
            "policy" => 'same-origin'
        ],
        "coep" => [
            "active" => true,
            "policy" => 'require-corp'
        ]
    ];

    private $coop = "same-origin";
    private $coep = "require-corp";

    public function testCOOP()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context = new ContextChecker($logger);
        $requestSub = new ResponseSubscriber(
            new ConfigProvider($this->default, []),
            $context
        );

        $kernel = $this->getMockBuilder(HttpKernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = Request::create('/test');
        $res = new ResponseEvent(
            $kernel,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $result = $requestSub->responseEvent($res);
        $this->assertNull($result);
        $this->assertEquals($res->getResponse()->headers->get('Cross-Origin-Opener-Policy'), $this->coop);
    }

    public function testCOEP()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $context = new ContextChecker($logger);
        $requestSub = new ResponseSubscriber(
            new ConfigProvider($this->default, []),
            $context
        );

        $kernel = $this->getMockBuilder(HttpKernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = Request::create('/test');
        $res = new ResponseEvent(
            $kernel,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response()
        );

        $result = $requestSub->responseEvent($res);
        $this->assertNull($result);
        $this->assertEquals($res->getResponse()->headers->get('Cross-Origin-Embedder-Policy'), $this->coep);
    }
}
