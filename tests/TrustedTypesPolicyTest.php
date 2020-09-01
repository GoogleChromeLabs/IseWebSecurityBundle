<?php

namespace Ise\WebSecurityBundle\Tests;

use Ise\WebSecurityBundle\EventSubscriber\TrustedTypesSubscriber;
use Ise\WebSecurityBundle\Options\ConfigProvider;
use Ise\WebSecurityBundle\Options\ContextChecker;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class IseTrustedTypesPolicyTest extends TestCase
{
    private $defaults = [
        'trusted_types' => [
            'active' => true,
            'policies' => ['foo'],
            'require_for' => ['script']
        ]
    ];

    private $nonPrefixed = " trusted-types foo; require-trusted-types-for 'script';";
    private $prefixed = "default-src 'script'; trusted-types foo; require-trusted-types-for 'script';";
    private $CSP = "default-src 'script'";

    public function testTrustedTypesSubscriberBase()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context = new ContextChecker($logger);
        
        $requestSub = new TrustedTypesSubscriber(
            new ConfigProvider($this->defaults, []),
            $context,
            $logger
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
        $this->assertEquals($res->getResponse()->headers->get('Content-Security-Policy'), $this->nonPrefixed);
    }

    public function testTrustedTypesSubscriberCSPPrefix()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context = new ContextChecker($logger);
        
        $requestSub = new TrustedTypesSubscriber(
            new ConfigProvider($this->defaults, []),
            $context,
            $logger
        );

        $kernel = $this->getMockBuilder(HttpKernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = Request::create('/test');
        $res = new ResponseEvent(
            $kernel,
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new Response('', 200, ['Content-Security-Policy' => $this->CSP])
        );

        $result = $requestSub->responseEvent($res);
        $this->assertNull($result);
        $this->assertEquals($res->getResponse()->headers->get('Content-Security-Policy'), $this->prefixed);
    }
}
